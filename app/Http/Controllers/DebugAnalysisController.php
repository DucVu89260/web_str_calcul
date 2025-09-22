<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class DebugAnalysisController extends Controller
{
    public function run(Request $request)
    {
        $modelPath = $request->input('model_path');
        $loadCase = $request->input('load_case', 'D');

        if (!$modelPath) {
            return response()->json(['error' => 'Missing model_path'], 400);
        }

        try {
            $process = new Process([
                'python',
                base_path('scripts/run_analysis.py'),
                $modelPath,
                $loadCase
            ]);
            $process->run();

            if (!$process->isSuccessful()) {
                return response()->json([
                    'error' => 'Python script failed',
                    'stderr' => $process->getErrorOutput(),
                ], 500);
            }

            $output = $process->getOutput();
            $decoded = json_decode($output, true);

            return response()->json([
                'message' => 'Debug analysis executed',
                'stdout_raw' => $output,
                'parsed' => $decoded ?? $output,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}