@extends('admins.layouts.master')

@section('content')
<div class="container my-4">
    <h2 class="mb-4">Tính toán tải trọng gió (TCVN 2737:2023)</h2>

    <div id="error-message" class="alert alert-danger" style="display:none;"></div>

    <form id="windForm" class="row g-3">
        <div class="col-md-2">
            <label class="form-label">Wo (kN/m²)</label>
            <input type="number" step="0.01" class="form-control" name="Wo" id="Wo" value="0.85" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">C</label>
            <input type="number" step="0.01" class="form-control" name="C" id="C" value="1.0" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Gf</label>
            <input type="number" step="0.01" class="form-control" name="Gf" id="Gf" value="1.0" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Slope (°)</label>
            <input type="number" step="1" class="form-control" name="slope" id="slope" value="6" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Chiều cao H (m)</label>
            <input type="number" step="0.1" class="form-control" name="H" id="H" value="12" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Bề rộng B (m)</label>
            <input type="number" step="0.1" class="form-control" name="B" id="B" value="18" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Chiều dài L (m)</label>
            <input type="number" step="0.1" class="form-control" name="L" id="L" value="30" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Số nhịp</label>
            <input type="number" class="form-control" name="nSpans" id="nSpans" value="3" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Parapet (m)</label>
            <input type="number" step="0.1" class="form-control" name="parapet" id="parapet" value="0.5">
        </div>
        <div class="col-md-2">
            <label class="form-label">Terrain</label>
            <select class="form-select" id="terrain" name="terrain">
                <option value="I">I</option>
                <option value="II" selected>II</option>
                <option value="III">III</option>
                <option value="IV">IV</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Kzt</label>
            <input type="number" step="0.01" class="form-control" name="Kzt" id="Kzt" value="1.0">
        </div>
        <div class="col-md-2">
            <label class="form-label">Lifetime</label>
            <input type="number" step="1" class="form-control" name="lifetime" id="lifetime" value="50">
        </div>

        <div class="col-12 mt-3">
            <button type="button" id="calcBtn" class="btn btn-primary">Tính toán</button>
            <button type="reset" id="resetBtn" class="btn btn-secondary">Reset</button>
        </div>
    </form>

    <div class="row mt-4">
        <div class="col-12 mb-3">
            <h5>Elevation (Khung ngang)</h5>
            <svg id="elevationSvg" width="100%" height="400" style="border:1px solid #ccc"></svg>
        </div>
        <div class="col-12">
            <h5>Plan (Mặt bằng)</h5>
            <svg id="planSvg" width="100%" height="400" style="border:1px solid #ccc"></svg>
        </div>
    </div>

    <div id="summary" class="mt-4"></div>

    <div id="results" class="mt-4" style="display:none;">
        <h4>Kết quả tính toán áp lực gió (kN/m²)</h4>
        <p class="small"><i>Quy ước: dấu (+) = gió ấn vào (tức là lực hướng vào theo trục local-3), dấu (-) = hút ra.</i></p>

        <div class="row">
            <div class="col-md-6">
                <h5>Gió ngang θ=0° – GCpi = +0.18</h5>
                <table class="table table-bordered"><thead><tr><th>Zone</th><th>Ce</th><th>p_net (kN/m²)</th></tr></thead><tbody id="windAcross_Pos"></tbody></table>
            </div>
            <div class="col-md-6">
                <h5>Gió ngang θ=0° – GCpi = -0.18</h5>
                <table class="table table-bordered"><thead><tr><th>Zone</th><th>Ce</th><th>p_net (kN/m²)</th></tr></thead><tbody id="windAcross_Neg"></tbody></table>
            </div>

            <div class="col-md-6 mt-3">
                <h5>Gió dọc θ=90° – GCpi = +0.18</h5>
                <table class="table table-bordered"><thead><tr><th>Zone</th><th>Ce</th><th>p_net (kN/m²)</th></tr></thead><tbody id="windAlong_Pos"></tbody></table>
            </div>
            <div class="col-md-6 mt-3">
                <h5>Gió dọc θ=90° – GCpi = -0.18</h5>
                <table class="table table-bordered"><thead><tr><th>Zone</th><th>Ce</th><th>p_net (kN/m²)</th></tr></thead><tbody id="windAlong_Neg"></tbody></table>
            </div>
        </div>

        <hr>

        <h5>Chi tiết vùng A,B,C,D, M1,M2 và E-zones</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Zone</th>
                    <th>Ce (θ=0°)</th>
                    <th>p (θ=0°) kN/m²</th>
                    <th>Ce (θ=90°)</th>
                    <th>p (θ=90°) kN/m²</th>
                </tr>
            </thead>
            <tbody id="extraZones"></tbody>
        </table>

        <h6>Parapet (kết quả net)</h6>
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <thead><tr><th>Vị trí (θ=0°)</th><th>p_net (kN/m²)</th></tr></thead>
                    <tbody id="parapetAcross"></tbody>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <thead><tr><th>Vị trí (θ=90°)</th><th>p_net (kN/m²)</th></tr></thead>
                    <tbody id="parapetAlong"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/wind_tcvn2737.js') }}"></script>
@endpush