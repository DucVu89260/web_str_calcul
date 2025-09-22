<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;

class SteelDesignController extends Controller
{
    // Hiển thị view và danh sách các sections
    public function index()
    {
        $members = DB::table('sections')
            ->join('models', 'sections.model_id', '=', 'models.id')
            ->select('sections.id','sections.name','models.name as model_name')
            ->orderBy('models.name')
            ->orderBy('sections.name')
            ->get();

        return view('steel_design.index', compact('members'));
    }

    // Trigger Python script thiết kế
    public function run(Request $request)
    {
        $request->validate([
            'section_id' => 'required|integer',
            'action' => 'required|string',
        ]);

        $section = DB::table('sections')->find($request->section_id);
        if (!$section) {
            return response()->json(['error' => 'Section not found'], 404);
        }

        try {
            $process = new Process([
                'python',
                base_path('scripts/steel_design.py'),
                $section->id,
                $request->action
            ]);

            $process->setTimeout(300); // 5 phút
            $process->run();

            $stdout = $process->getOutput();
            $stderr = $process->getErrorOutput();

            if (!$process->isSuccessful()) {
                Log::error('Steel design failed', ['section_id'=>$section->id, 'stderr'=>$stderr]);
                return response()->json([
                    'error' => 'Design script failed',
                    'details' => $stderr ?: $stdout
                ], 500);
            }

            $output = json_decode($stdout,true) ?? $stdout;

            return response()->json([
                'success' => true,
                'section' => $section->name,
                'model' => DB::table('models')->where('id',$section->model_id)->value('name'),
                'result' => $output
            ]);

        } catch (\Throwable $e) {
            Log::error('Steel design exception', ['exception'=>$e]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
