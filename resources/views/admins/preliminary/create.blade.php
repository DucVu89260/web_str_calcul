@extends('admins.layouts.master')

@section('content')
<div class="container my-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">
                <i class="fas fa-drafting-compass"></i> Create Preliminary for Project: {{ $project->name }}
            </h4>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('preliminary.store', $project) }}">
                @csrf
                <div id="preliminaryAccordion">

                    <!-- Section 1: Kích thước hình học -->
                    <div class="card mb-2">
                        <div class="card-header" id="geometryHeading">
                            <h5 class="mb-0">
                                <button class="btn btn-link" data-toggle="collapse" data-target="#geometryCollapse" aria-expanded="true" aria-controls="geometryCollapse">
                                    <i class="fas fa-ruler-combined"></i> Kích thước hình học
                                </button>
                            </h5>
                        </div>
                        <div id="geometryCollapse" class="collapse show" aria-labelledby="geometryHeading">
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="max_span">Max Span (m)</label>
                                        <input type="number" step="0.01" name="max_span" class="form-control @error('max_span') is-invalid @enderror"
                                            value="{{ old('max_span', $params->max_span ?? 0) }}" required>
                                        @error('max_span') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="total_spans">Tổng số nhịp</label>
                                        <input type="number" name="total_spans" class="form-control @error('total_spans') is-invalid @enderror"
                                            value="{{ old('total_spans', $params->total_spans ?? 1) }}" required>
                                        @error('total_spans') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="total_frame_width">Tổng kích thước khung (m)</label>
                                        <input type="number" step="0.01" name="extra_params[total_frame_width]" class="form-control @error('extra_params.total_frame_width') is-invalid @enderror"
                                            value="{{ old('extra_params.total_frame_width', $params->extra_params['total_frame_width'] ?? 0) }}">
                                        @error('extra_params.total_frame_width') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="eave_height">Chiều cao mép mái (m)</label>
                                        <input type="number" step="0.01" name="eave_height" class="form-control @error('eave_height') is-invalid @enderror"
                                            value="{{ old('eave_height', $params->eave_height ?? 0) }}" required>
                                        @error('eave_height') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Địa điểm công trình -->
                    <div class="card mb-2">
                        <div class="card-header" id="locationHeading">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#locationCollapse" aria-expanded="false" aria-controls="locationCollapse">
                                    <i class="fas fa-map-marker-alt"></i> Địa điểm công trình
                                </button>
                            </h5>
                        </div>
                        <div id="locationCollapse" class="collapse show" aria-labelledby="locationHeading">
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="location">Tỉnh/Thành phố</label>
                                        <select name="location" class="form-control @error('location') is-invalid @enderror" required>
                                            <option value="">Chọn địa điểm</option>
                                            @foreach([
                                                'Hà Nội' => ['wind_speed' => 120, 'seismic_zone' => 'Zone 2'],
                                                'TP Hồ Chí Minh' => ['wind_speed' => 115, 'seismic_zone' => 'Zone 1'],
                                                'Đà Nẵng' => ['wind_speed' => 130, 'seismic_zone' => 'Zone 3'],
                                                'Cần Thơ' => ['wind_speed' => 110, 'seismic_zone' => 'Zone 1'],
                                            ] as $city => $data)
                                                <option value="{{ $city }}" data-wind-speed="{{ $data['wind_speed'] }}" data-seismic-zone="{{ $data['seismic_zone'] }}"
                                                    {{ old('location', $params->location ?? '') == $city ? 'selected' : '' }}>
                                                    {{ $city }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Wind Speed (km/h)</label>
                                        <input type="number" step="0.1" name="extra_params[wind_speed]" class="form-control" 
                                            value="{{ old('extra_params.wind_speed', $params->extra_params['wind_speed'] ?? '') }}" readonly>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Seismic Zone</label>
                                        <input type="text" name="extra_params[seismic_zone]" class="form-control"
                                            value="{{ old('extra_params.seismic_zone', $params->extra_params['seismic_zone'] ?? '') }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Tải trọng cơ bản -->
                    <div class="card mb-2">
                        <div class="card-header" id="loadsHeading">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#loadsCollapse" aria-expanded="false" aria-controls="loadsCollapse">
                                    <i class="fas fa-weight-hanging"></i> Tải trọng cơ bản
                                </button>
                            </h5>
                        </div>
                        <div id="loadsCollapse" class="collapse show" aria-labelledby="loadsHeading">
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="dead_load_roof">Tĩnh tải mái (D, kN/m²)</label>
                                        <input type="number" step="0.01" name="dead_load_roof" class="form-control @error('dead_load_roof') is-invalid @enderror"
                                            value="{{ old('dead_load_roof', $params->dead_load_roof ?? 0) }}" required>
                                        @error('dead_load_roof') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="col_load">Tải treo mái (COL, kN/m²)</label>
                                        <input type="number" step="0.01" name="extra_params[col_load]" class="form-control"
                                            value="{{ old('extra_params.col_load', $params->extra_params['col_load'] ?? 0) }}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="live_load_roof">Hoạt tải sửa chữa mái (LR, kN/m²)</label>
                                        <input type="number" step="0.01" name="live_load_roof" class="form-control @error('live_load_roof') is-invalid @enderror"
                                            value="{{ old('live_load_roof', $params->live_load_roof ?? 0) }}" required>
                                        @error('live_load_roof') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Tải trọng cầu trục -->
                    <div class="card mb-2">
                        <div class="card-header" id="craneHeading">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#craneCollapse" aria-expanded="false" aria-controls="craneCollapse">
                                    <i class="fas fa-truck-loading"></i> Tải trọng cầu trục
                                </button>
                            </h5>
                        </div>
                        <div id="craneCollapse" class="collapse" aria-labelledby="craneHeading">
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input type="checkbox" name="has_crane" id="has_crane" class="form-check-input"
                                        {{ old('has_crane', $params->has_crane ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_crane">Có cầu trục?</label>
                                </div>
                                <div id="crane_details" style="display: {{ old('has_crane', $params->has_crane ?? false) ? 'block' : 'none' }};">
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <label for="crane_weight">Crane Weight (tons)</label>
                                            <input type="number" step="0.01" name="crane_details[crane_weight]" class="form-control"
                                                value="{{ old('crane_details.crane_weight', $params->crane_details['crane_weight'] ?? '') }}">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="hoist_weight">Hoist & Trolley Weight (tons)</label>
                                            <input type="number" step="0.01" name="crane_details[hoist_weight]" class="form-control"
                                                value="{{ old('crane_details.hoist_weight', $params->crane_details['hoist_weight'] ?? '') }}">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="mode">Mode</label>
                                            <select name="crane_details[mode]" class="form-control">
                                                <option value="">Chọn chế độ</option>
                                                @foreach(['A1','A2','A3','A4','A5','A6','A7','A8'] as $mode)
                                                    <option value="{{ $mode }}" {{ old('crane_details.mode', $params->crane_details['mode'] ?? '') == $mode ? 'selected' : '' }}>
                                                        {{ $mode }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="count">Number of Cranes</label>
                                            <input type="number" name="crane_details[count]" class="form-control"
                                                value="{{ old('crane_details.count', $params->crane_details['count'] ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div> <!-- accordion -->

                <div class="text-right mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-magic"></i> Generate Suggestions
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Toggle crane details
    $('#has_crane').on('change', function() {
        $('#crane_details').toggle(this.checked);
    });

    // Auto-fill wind speed & seismic zone
    $('select[name="location"]').on('change', function() {
        const selected = $(this).find(':selected');
        $('input[name="extra_params[wind_speed]"]').val(selected.data('wind-speed') || '');
        $('input[name="extra_params[seismic_zone]"]').val(selected.data('seismic-zone') || '');
    });
</script>
@endpush
@endsection