@extends('admins.layouts.master')

@section('content')
<div class="container mt-4">
    <h3>Kết quả tính toán — Liên kết Moment (AISC 360-10)</h3>

    <div class="mb-3">
        <a href="{{ route('connections.create', ['type'=>'moment','standard'=>'aisc360-10']) }}" class="btn btn-outline-secondary">Sửa tham số</a>
        <a href="{{ route('connections.index') }}" class="btn btn-secondary">Quay về danh sách</a>
    </div>

    <div class="card mb-3">
        <div class="card-header">Tóm tắt đầu vào</div>
        <div class="card-body">
            <pre style="white-space:pre-wrap;">{{ json_encode($input, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">Kiểm tra bu-lông (tính từng bu-lông)</div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <strong>Nominal area (A<sub>b</sub>)</strong>: {{ number_format($result['Ab_mm2'] ?? 0,2) }} mm²
                        </li>
                        <li class="list-group-item">
                            <strong>R<sub>n, shear</sub> (per bolt)</strong>: {{ number_format(($result['Rn_shear_N'] ?? 0)/1000,2) }} kN
                        </li>
                        <li class="list-group-item">
                            <strong>R<sub>n, bearing</sub> (per bolt)</strong>: {{ number_format(($result['Rn_bearing_N'] ?? 0)/1000,2) }} kN
                        </li>
                        <li class="list-group-item">
                            <strong>Effective R<sub>n</sub> per bolt (governing)</strong>: {{ number_format(($result['Rn_effective_N'] ?? 0)/1000,2) }} kN
                        </li>
                        <li class="list-group-item">
                            <strong>Design factor used</strong>: {{ strtoupper($result['method'] ?? 'LRFD/ASD') }}
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Group / Moment capacity</div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <strong>Number of bolts</strong>: {{ $result['n_bolts'] ?? 0 }}
                        </li>
                        <li class="list-group-item">
                            <strong>Total design tensile/shear force (group)</strong>: {{ number_format(($result['group_force_N'] ?? 0)/1000,2) }} kN
                        </li>
                        <li class="list-group-item">
                            <strong>Lever arm used</strong>: {{ number_format($result['lever_arm_mm'] ?? 0,2) }} mm
                        </li>
                        <li class="list-group-item">
                            <strong>Available moment capacity</strong>: {{ number_format($result['M_resisting_kNm'] ?? 0,3) }} kN·m
                        </li>
                        <li class="list-group-item">
                            <strong>Applied moment M<sub>max</sub></strong>: {{ number_format($result['M_applied_kNm'] ?? 0,3) }} kN·m
                        </li>
                        <li class="list-group-item">
                            <strong>Utilization</strong>:
                            @if(($result['utilization'] ?? 999) <= 1)
                                <span class="badge badge-success">PASS ({{ number_format(($result['utilization'] ?? 0)*100,2) }}%)</span>
                            @else
                                <span class="badge badge-danger">FAIL ({{ number_format(($result['utilization'] ?? 0)*100,2) }}%)</span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>

        </div>

        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">Block shear & shear element checks (AISC J4)</div>
                <div class="card-body">
                    @if(isset($result['block_shear']))
                        <ul class="list-group">
                            <li class="list-group-item"><strong>R<sub>n, block-shear</sub></strong>: {{ number_format(($result['block_shear']['Rn_block_N'] ?? 0)/1000,2) }} kN</li>
                            <li class="list-group-item"><strong>Design (φ or 1/Ω)</strong>: {{ $result['block_shear']['design_label'] ?? '' }}</li>
                            <li class="list-group-item">
                                <strong>Block shear check</strong>:
                                @if($result['block_shear']['pass'])
                                    <span class="badge badge-success">PASS</span>
                                @else
                                    <span class="badge badge-danger">FAIL</span>
                                @endif
                            </li>
                        </ul>
                    @else
                        <p class="text-muted">Bạn chưa nhập diện tích Anv/Ant/Agv — bỏ qua kiểm tra block shear. (Nếu có, form sẽ tính căn cứ J4.3)</p>
                    @endif

                    @if(isset($result['shear_element']))
                        <hr>
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Shear yielding R<sub>n</sub></strong>: {{ number_format(($result['shear_element']['Rn_yield_N'] ?? 0)/1000,2) }} kN</li>
                            <li class="list-group-item"><strong>Shear rupture R<sub>n</sub></strong>: {{ number_format(($result['shear_element']['Rn_rupture_N'] ?? 0)/1000,2) }} kN</li>
                            <li class="list-group-item">
                                <strong>Shear element check</strong>:
                                @if($result['shear_element']['pass'])
                                    <span class="badge badge-success">PASS</span>
                                @else
                                    <span class="badge badge-danger">FAIL</span>
                                @endif
                            </li>
                        </ul>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">Ghi chú & tham chiếu</div>
                <div class="card-body">
                    <ul>
                        <li>Phép tính bolt: R<sub>n</sub> = F<sub>n</sub> A<sub>b</sub> (AISC J3.6 / J3-1). (φ = 0.75 LRFD; Ω = 2.0 ASD).</li>
                        <li>Bearing tại lỗ: dùng J3-6 (Rn = 1.2·l<sub>c</sub>·t·F<sub>u</sub> ≤ 2.4·d·t·F<sub>u</sub> khi xét biến dạng; hoặc 1.5·l<sub>c</sub>·t·F<sub>u</sub> ≤ 3.0·d·t·F<sub>u</sub> nếu không xét).</li>
                        <li>Block-shear: theo J4.3 R<sub>n</sub> = 0.60·F<sub>u</sub>·A<sub>nv</sub> + U<sub>bs</sub>·F<sub>u</sub>·A<sub>nt</sub> ≤ 0.60·F<sub>y</sub>·A<sub>gv</sub> + U<sub>bs</sub>·F<sub>u</sub>·A<sub>nt</sub>.</li>
                    </ul>

                    <small class="text-muted">Nguồn: AISC 360-10 (Sections J3 &amp; J4) — các công thức trên được dùng trực tiếp trong phép tính. (Xem phần Tài liệu tham khảo bên dưới.)</small>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection