<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Process\Process;

class LoadCombinationController extends Controller
{
    public function index()
    {
        $combinations = session('load_combinations', []);
        return view('admins.load_combinations.index', compact('combinations'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'dl' => 'required|string|regex:/^[A-Za-z0-9+-]+$/',
            'll' => 'required|string|regex:/^[A-Za-z0-9+-]+$/',
            'winds' => 'nullable|string|regex:/^[A-Za-z0-9+-, ]*$/',
            'cranes' => 'nullable|string|regex:/^[A-Za-z0-9+-, ]*$/',
        ], [
            'dl.regex' => 'Dead Load chỉ được chứa chữ, số, dấu + hoặc -',
            'll.regex' => 'Live Load chỉ được chứa chữ, số, dấu + hoặc -',
            'winds.regex' => 'Winds chỉ được chứa chữ, số, dấu +,-, dấu phẩy và khoảng trắng',
            'cranes.regex' => 'Cranes chỉ được chứa chữ, số, dấu +,-, dấu phẩy và khoảng trắng',
        ]);

        $dl = $request->input('dl', 'DL');
        $ll = $request->input('ll', 'LL');
        $winds = array_unique(array_filter(array_map('trim', explode(',', $request->input('winds', '')))));
        $cranes = array_unique(array_filter(array_map('trim', explode(',', $request->input('cranes', '')))));

        $combinations = $this->generateCombinations($dl, $ll, $winds, $cranes);
        session(['load_combinations' => $combinations]);

        return view('admins.load_combinations.index', compact('combinations', 'dl', 'll', 'winds', 'cranes'));
    }

    private function generateCombinations(string $dl, string $ll, array $winds, array $cranes): array
    {
        $combinations = [];
        $combinations[] = $this->formatCombo(['1.2' => $dl, '1.6' => $ll]);
        foreach ($winds as $w) {
            $combinations[] = $this->formatCombo(['1.2' => $dl, '1.0' => $w, '0.5' => $ll]);
        }
        foreach ($cranes as $c) {
            $combinations[] = $this->formatCombo(['1.2' => $dl, '1.0' => $c, '0.5' => $ll]);
            foreach ($winds as $w) {
                $combinations[] = $this->formatCombo(['1.2' => $dl, '1.0' => $w, '1.0' => $c]);
            }
        }
        if (!empty($winds)) {
            $wind_parts = array_map(fn($w) => "1.0$w", $winds);
            $combinations[] = "1.2$dl + " . implode(' + ', $wind_parts) . " + 0.5$ll";
        }
        return array_unique($combinations);
    }

    private function formatCombo(array $comboParts): string
    {
        $parts = [];
        foreach ($comboParts as $factor => $load) {
            $parts[] = $factor . $load;
        }
        return implode(' + ', $parts);
    }

    public function export()
    {
        $combinations = session('load_combinations', []);
        if (empty($combinations)) {
            return redirect()->route('load_combinations.index')->with('error', 'No load combinations to export.');
        }
        return new StreamedResponse(function () use ($combinations) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['No', 'Combination']);
            foreach ($combinations as $i => $combo) {
                fputcsv($handle, [$i + 1, $combo]);
            }
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="load_combinations.csv"',
        ]);
    }

    public function pushToSap()
    {
        $combinations = session('load_combinations', []);
        if (empty($combinations)) {
            return redirect()->route('load_combinations.index')->with('error', 'No load combinations to push to SAP2000.');
        }
        $csvPath = storage_path('app/load_combinations.csv');
        $handle = fopen($csvPath, 'w');
        fputcsv($handle, ['No', 'Combination']);
        foreach ($combinations as $i => $combo) {
            fputcsv($handle, [$i + 1, $combo]);
        }
        fclose($handle);
        $script = base_path('scripts/import_load_combinations.py');
        $pythonPath = 'C:\laragon\bin\python\python-3.10\python.exe';
        $process = new Process([$pythonPath, $script, $csvPath]);
        $process->setTimeout(60);
        $process->run();
        if (!$process->isSuccessful()) {
            return redirect()->route('load_combinations.index')->with('error', 'Push to SAP2000 failed: ' . $process->getErrorOutput());
        }
        return redirect()->route('load_combinations.index')->with('success', 'Load combinations imported into SAP2000!');
    }
}