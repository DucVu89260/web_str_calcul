@extends('admins.layouts.master')

@section('content')
<div class="container my-4">
    <h2>Edit Wind Parameter</h2>

    <form action="{{ route('wind.update', $wind->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="form-group">
            <label>Site</label>
            <select name="site_parameter_id" class="form-control" required>
                @foreach($sites as $s)
                    <option value="{{ $s->id }}" {{ $wind->site_parameter_id == $s->id ? 'selected' : '' }}>
                        {{ $s->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Standard Code</label>
            <input type="text" name="standard_code" class="form-control" value="{{ $wind->standard_code }}" required>
        </div>

        <div class="form-group">
            <label>Basic Wind Speed (m/s)</label>
            <input type="number" step="0.01" name="basic_wind_speed" class="form-control" value="{{ $wind->basic_wind_speed }}">
        </div>

        <div class="form-group">
            <label>Region</label>
            <input type="text" name="map_region" class="form-control" value="{{ $wind->map_region }}">
        </div>

        <div class="form-group">
            <label>Terrain Factors (JSON)</label>
            <textarea name="terrain_factors" class="form-control" rows="2">{{ json_encode($wind->terrain_factors) }}</textarea>
        </div>

        <div class="form-group">
            <label>Directionality Factor (Kd)</label>
            <input type="number" step="0.01" name="directionality_factor" class="form-control" value="{{ $wind->directionality_factor }}">
        </div>

        <div class="form-group">
            <label>Gust Effect Factor (G)</label>
            <input type="number" step="0.01" name="gust_effect_factor" class="form-control" value="{{ $wind->gust_effect_factor }}">
        </div>

        <div class="form-group">
            <label>Conversion to Other Standard (JSON)</label>
            <textarea name="conversion_to_other" class="form-control" rows="2">{{ json_encode($wind->conversion_to_other) }}</textarea>
        </div>

        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" class="form-control" rows="3">{{ $wind->notes }}</textarea>
        </div>

        <button class="btn btn-success">Update</button>
        <a href="{{ route('wind.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection