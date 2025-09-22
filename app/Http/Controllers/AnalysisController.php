<?php

namespace App\Http\Controllers;

use App\Models\AnalysisRun;
use App\Models\StrcModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;

class AnalysisController extends Controller
{
    public function index(Request $request)
    {
        $models = StrcModel::with(['project', 'latestRun'])
            ->orderBy('updated_at', 'desc')
            ->paginate(15);

        foreach ($models as $model) {
            $latestRun = $model->analysisRuns->first();
            $model->latestRun = $latestRun;

            $model->loadCases = collect($latestRun?->meta['load_cases'] ?? []);
            $model->loadCombinations = collect($latestRun?->meta['load_combinations'] ?? []);
            $model->connected = $latestRun?->status === 'success';
        }

        $summary = [
            'total'   => AnalysisRun::count(),
            'running' => AnalysisRun::where('status', 'running')->count(),
            'success' => AnalysisRun::where('status', 'success')->count(),
            'failed'  => AnalysisRun::where('status', 'failed')->count(),
        ];

        return view('admins.analysis.index', [
            'models' => $models,
            'summary' => $summary,
        ]);
    }

    public function connect(Request $request)
    {
        $modelPath = $request->input('model_path');
        if (!$modelPath || !file_exists($modelPath)) {
            return response()->json(['status' => 'error', 'message' => 'Model file not found'], 400);
        }

        try {
            $script = base_path('scripts/connect_and_run.py');
            $process = new Process(['python', $script, $modelPath]);
            $process->setTimeout(600);
            $process->run();

            $stdout = trim($process->getOutput());
            $output = json_decode($stdout, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid JSON returned from Python',
                    'raw' => $stdout,
                ], 500);
            }

            if (($output['status'] ?? null) !== 'success') {
                return response()->json([
                    'status' => 'error',
                    'message' => $output['message'] ?? 'Connection failed',
                ], 500);
            }

            // Lưu trạng thái connected
            AnalysisRun::create([
                'initiated_by' => Auth::id(),
                'runner'       => 'python_sap_wrapper',
                'status'       => 'success',
                'started_at'   => now(),
                'finished_at'  => now(),
                'meta' => [
                    'model_path'        => $modelPath,
                    'load_cases'        => $output['load_cases'] ?? [],
                    'load_combinations' => $output['load_combinations'] ?? [],
                ],
            ]);

            return response()->json([
                'status' => 'success',
                'model' => $output['model'] ?? $modelPath,
                'load_cases' => $output['load_cases'] ?? [],
                'load_combinations' => $output['load_combinations'] ?? [],
                'message' => 'Connected & analyzed successfully',
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Connect analysis exception', ['exception' => $e]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function loads()
    {
        return $this->runPythonScript('scripts/get_loads.py');
    }

    public function selection()
    {
        return $this->runPythonScript('scripts/get_selection.py');
    }

    public function results(Request $request)
    {
        $loadCase  = $request->query('loadcase');
        $loadCombo = $request->query('loadcombination');
        $runFlag   = $request->boolean('run'); // thêm query ?run=1 để ép chạy analysis

        $args = ['python', base_path('scripts/get_results.py')];

        if ($loadCase) {
            $args[] = '--loadcase';
            $args[] = $loadCase;
        }

        if ($loadCombo) {
            $args[] = '--loadcombination';
            $args[] = $loadCombo;
        }

        if ($runFlag) {
            $args[] = '--run';
        }

        $response = $this->runPythonProcess($args);

        if ($response->getStatusCode() === 200) {
            $data = $response->getData();

            if (!empty($data->results)) {
                // --- Frames ---
                if (!empty($data->results->frames)) {
                    $frames = [];
                    foreach ($data->results->frames as $f) {
                        $forces = $f->forces ?? [];
                        if (is_object($forces)) $forces = (array)$forces;
                        if (!empty($forces) && array_keys($forces) !== range(0, count($forces) - 1)) {
                            $forces = array_values($forces);
                        }

                        $frames[] = [
                            'name'   => $f->name ?? ($f->Obj ?? 'UNKNOWN'),
                            'forces' => $forces,
                        ];
                    }
                    $data->results->frames = $frames;
                }

                // --- Joints ---
                if (!empty($data->results->joints)) {
                    $joints = [];
                    foreach ($data->results->joints as $j) {
                        $joints[] = [
                            'name' => $j->Joint ?? $j->name ?? 'UNKNOWN',
                            'displacements' => [[
                                'LoadCase' => $j->LoadCase ?? '',
                                'UX' => (float)($j->UX ?? 0),
                                'UY' => (float)($j->UY ?? 0),
                                'UZ' => (float)($j->UZ ?? 0),
                                'RX' => (float)($j->RX ?? 0),
                                'RY' => (float)($j->RY ?? 0),
                                'RZ' => (float)($j->RZ ?? 0),
                            ]],
                        ];
                    }
                    $data->results->joints = $joints;
                }

                // --- Reactions ---
                if (!empty($data->results->reactions)) {
                    $reactions = [];
                    foreach ($data->results->reactions as $r) {
                        $reactions[] = [
                            'name' => $r->Joint ?? $r->name ?? 'UNKNOWN',
                            'reactions' => [[
                                'LoadCase' => $r->LoadCase ?? '',
                                'FX' => (float)($r->FX ?? 0),
                                'FY' => (float)($r->FY ?? 0),
                                'FZ' => (float)($r->FZ ?? 0),
                                'MX' => (float)($r->MX ?? 0),
                                'MY' => (float)($r->MY ?? 0),
                                'MZ' => (float)($r->MZ ?? 0),
                            ]],
                        ];
                    }
                    $data->results->reactions = $reactions;
                }

                return response()->json([
                    'status'  => 'success',
                    'model'   => $data->model ?? null,
                    'load'    => $data->load ?? ($loadCase ?? $loadCombo ?? 'UNKNOWN'),
                    'results' => $data->results,
                ]);
            }
        }

        return $response;
    }

    public function run(Request $request)
    {
        $request->validate([
            'load_case' => 'nullable|string',
        ]);

        $user = Auth::user();
        $loadCase = $request->input('load_case', 'D');

        $run = AnalysisRun::create([
            'initiated_by' => $user?->id,
            'runner'       => 'python_sap_wrapper',
            'status'       => 'running',
            'started_at'   => now(),
            'meta'         => ['load_case' => $loadCase],
        ]);

        try {
            $process = new Process(['python', base_path('scripts/run_analysis.py'), $loadCase]);
            $process->setTimeout(300);
            $process->run();

            $stdout = trim($process->getOutput());
            $stderr = trim($process->getErrorOutput());

            if (!$process->isSuccessful()) {
                $run->update([
                    'status'      => 'failed',
                    'finished_at' => now(),
                    'error_log'   => $stderr ?: 'Python process failed',
                ]);
                return response()->json(['status'=>'error','message'=>$stderr ?: 'Python process failed'], 500);
            }

            $output = json_decode($stdout, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $run->update([
                    'status'      => 'failed',
                    'finished_at' => now(),
                    'error_log'   => $stdout,
                ]);
                return response()->json([
                    'status'=>'error',
                    'message'=>'Python output is not valid JSON',
                    'raw'=>$stdout,
                ], 500);
            }

            $run->update([
                'status'         => 'success',
                'finished_at'    => now(),
                'result_summary' => $output,
            ]);

            return response()->json(['status'=>'success'] + $output, 200);

        } catch (\Throwable $e) {
            $run->update([
                'status'      => 'failed',
                'finished_at' => now(),
                'error_log'   => $e->getMessage(),
            ]);
            Log::error('Analysis exception', ['exception'=>$e]);
            return response()->json(['status'=>'error','message'=>$e->getMessage()], 500);
        }
    }

    private function runPythonScript(string $script)
    {
        return $this->runPythonProcess(['python', base_path($script)]);
    }

    private function runPythonProcess(array $args)
    {
        try {
            Log::debug('Running Python process', ['args' => $args]);

            $process = new Process($args);
            $process->setTimeout(300);
            $process->run();

            $stdout = trim($process->getOutput());
            $stderr = trim($process->getErrorOutput());

            Log::debug('Python process output', [
                'stdout' => $stdout,
                'stderr' => $stderr,
                'exit_code' => $process->getExitCode(),
            ]);

            if (!$process->isSuccessful()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Python process failed',
                    'stderr' => $stderr,
                    'stdout' => $stdout,
                    'exit_code' => $process->getExitCode(),
                ], 500);
            }

            $decoded = json_decode($stdout, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Python returned invalid JSON',
                    'stderr' => $stderr,
                    'stdout' => $stdout,
                ], 500);
            }

            return response()->json($decoded, 200);

        } catch (\Throwable $e) {
            Log::error('Python process exception', ['exception' => $e, 'args' => $args]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}