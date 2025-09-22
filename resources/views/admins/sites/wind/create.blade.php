@extends('admins.layouts.master')

@section('content')
<div class="container my-4">
    <h2>Add Wind Parameter</h2>

    <form action="{{ route('wind.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Site</label>
            <select name="site_parameter_id" class="form-control" required>
                @foreach($sites as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Standard Code</label>
            <input type="text" name="standard_code" class="form-control" placeholder="TCVN2737-2023 / ASCE7-10" required>
            <small class="text-muted">Ví dụ: TCVN2737-2023, ASCE7-10</small>
        </div>

        <div class="form-group">
            <label>Basic Wind Speed (m/s)</label>
            <input type="number" step="0.01" name="basic_wind_speed" class="form-control">
        </div>

        <div class="form-group">
            <label>Region</label>
            <input type="text" name="map_region" class="form-control">
        </div>

        <div class="form-group">
            <label>Terrain Factors (JSON)</label>
            <textarea name="terrain_factors" class="form-control" rows="2" placeholder='{"I":1.1,"II":1.0}'></textarea>
        </div>

        <div class="form-group">
            <label>Directionality Factor (Kd)</label>
            <input type="number" step="0.01" name="directionality_factor" class="form-control">
        </div>

        <div class="form-group">
            <label>Gust Effect Factor (G)</label>
            <input type="number" step="0.01" name="gust_effect_factor" class="form-control">
        </div>

        <div class="form-group">
            <label>Conversion to Other Standard (JSON)</label>
            <textarea name="conversion_to_other" class="form-control" rows="2" placeholder='{"to":"ASCE7-10","factor_speed":1.05}'></textarea>
        </div>

        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" class="form-control" rows="3"></textarea>
        </div>

        <button class="btn btn-success">Save</button>
        <a href="{{ route('wind.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection