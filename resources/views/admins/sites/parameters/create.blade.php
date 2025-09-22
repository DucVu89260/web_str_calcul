@extends('admins.layouts.master')

@section('content')
<div class="container my-4">
    <h2>Add New Site Parameter</h2>

    <form action="{{ route('parameters.store') }}" method="POST">
        @csrf
        <div class="form-group mb-2">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
            <small class="form-text text-muted">Tên địa điểm hoặc site (ví dụ: Hà Nội, KCN VSIP)</small>
        </div>

        <div class="form-group mb-2">
            <label>Country</label>
            <input type="text" name="country" class="form-control" value="VN">
            <small class="form-text text-muted">Mã quốc gia (VN, US, ...)</small>
        </div>

        <div class="form-group mb-2">
            <label>Latitude</label>
            <input type="number" step="0.000001" name="latitude" class="form-control">
            <small class="form-text text-muted">Vĩ độ địa điểm (tham chiếu bản đồ gió/động đất)</small>
        </div>

        <div class="form-group mb-2">
            <label>Longitude</label>
            <input type="number" step="0.000001" name="longitude" class="form-control">
            <small class="form-text text-muted">Kinh độ địa điểm</small>
        </div>

        <div class="form-group mb-2">
            <label>Elevation (m)</label>
            <input type="number" step="0.01" name="elevation" class="form-control">
            <small class="form-text text-muted">Cao độ so với mực nước biển (dùng điều chỉnh áp lực gió)</small>
        </div>

        <div class="form-group mb-2">
            <label>Terrain Category (I, II, III, IV)</label>
            <input type="text" name="terrain_category" class="form-control">
            <small class="form-text text-muted">
                Theo TCVN 2737:2023: I (địa hình trống trải) → IV (nhiều vật cản).  
                Tương đương Exposure Category trong ASCE.
            </small>
        </div>

        <div class="form-group mb-2">
            <label>Exposure Category (B, C, D)</label>
            <input type="text" name="exposure_category" class="form-control">
            <small class="form-text text-muted">
                Theo ASCE7-10:  
                B (khu dân cư/cây xanh), C (địa hình trống trải), D (gần biển).
            </small>
        </div>

        <div class="form-group mb-2">
            <label>Topography Factor (Kzt)</label>
            <input type="number" step="0.01" name="topography_factor" class="form-control">
            <small class="form-text text-muted">
                Hệ số địa hình (TCVN: hệ số địa hình <em>kz</em>, ASCE: <em>Kzt</em>).
            </small>
        </div>

        <div class="form-group mb-2">
            <label>Importance Category</label>
            <input type="text" name="importance_category" class="form-control">
            <small class="form-text text-muted">
                Cấp công trình (TCVN: cấp I–IV, ASCE: Risk Category I–IV).
            </small>
        </div>

        <button class="btn btn-success mt-2">Save</button>
        <a href="{{ route('parameters.index') }}" class="btn btn-secondary mt-2">Cancel</a>
    </form>
</div>
@endsection