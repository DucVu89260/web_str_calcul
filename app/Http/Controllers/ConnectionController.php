<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Services\Connections\Aisc360Service;

class ConnectionController extends Controller
{
    /**
     * Danh sách liên kết + tiêu chuẩn
     */
    public function index()
    {
        $types = [
            'moment'     => 'Liên kết Moment',
            'shear'      => 'Liên kết Cắt',
            'baseplate'  => 'Liên kết Bản đế',
        ];

        $standards = [
            'aisc360-10'    => 'AISC 360-10',
            'ec3'           => 'Eurocode 3',
            'tcvn5575-2012' => 'TCVN 5575:2012',
        ];

        return view('admins.connections.index', compact('types', 'standards'));
    }

    /**
     * Hiển thị form nhập theo type + standard
     */
    public function create($type, $standard)
    {
        $viewPath = "admins.connections.forms.$type.$standard";
        
        if (!view()->exists($viewPath)) {
            abort(404, "Form cho {$type} - {$standard} chưa được hỗ trợ");
        }

        return view($viewPath, compact('type', 'standard'));
    }

    /**
     * Xử lý tính toán
     */
    public function calculate(Request $request, $type, $standard)
    {
        // Validate input cơ bản
        $validator = Validator::make($request->all(), [
            'Mmax' => 'nullable|numeric',
            'Mmin' => 'nullable|numeric',
            'Pmax' => 'nullable|numeric',
            'Pmin' => 'nullable|numeric',
            'Qmax' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $input = $request->all();
        $result = $this->runCalculation($type, $standard, $input);

        $viewPath = "admins.connections.results.$type.$standard";
        if (!view()->exists($viewPath)) {
            $viewPath = "admins.connections.results.$type"; // fallback
        }

        return view($viewPath, compact('type', 'standard', 'input', 'result'));
    }

    /**
     * Core tính toán
     */
    private function runCalculation($type, $standard, $input)
    {
        switch ($standard) {
            case 'aisc360-10':
                return $this->calcAisc($type, $input);

            case 'ec3':
                return $this->calcEc3($type, $input);

            case 'tcvn5575-2012':
                return $this->calcTcvn($type, $input);

            default:
                return ['status' => 'error', 'message' => 'Tiêu chuẩn chưa hỗ trợ'];
        }
    }

    private function calcAisc($type, $input)
    {
        $service = new Aisc360Service();
        return $service->calculate($type, $input);
    }

    private function calcEc3($type, $input)
    {
        return [
            'status' => 'OK',
            'note'   => 'Theo Eurocode 3',
        ];
    }

    private function calcTcvn($type, $input)
    {
        return [
            'status' => 'OK',
            'note'   => 'Theo TCVN 5575:2012',
        ];
    }

    
    public function exportPdf($type, $standard, $input, $result)
    {
        $pdf = Pdf::loadView("admins.connections.reports.$type.$standard", compact('type','standard','input','result'));
        return $pdf->download("report-{$type}-{$standard}.pdf");
    }
}
