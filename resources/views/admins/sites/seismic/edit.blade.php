@extends('admins.layouts.master')

@section('content')
<div class="container my-4">
    <h2>Edit Seismic Parameter</h2>

    <form action="{{ route('seismic.update', $seismic->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="form-group">
            <label>Site</label>
            <select name="site_parameter_id" class="form-control" required>
                @foreach($sites as $s)
                    <option value="{{ $s->id }}" {{ $seismic->site_parameter_id == $s->id ? 'selected' : '' }}>
                        {{ $s->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Standard Code</label>
            <input type="text" name="standard_code" class="form-control" value="{{ $seismic->standard_code }}" required>
        </div>

        <div class="form-group">
            <label>agR</label>
            <input type="number" step="0.01" name="agR" class="form-control" value="{{ $seismic->agR }}">
        </div>

        <div class="form-group">
            <label>Site Class</label>
            <input type="text" name="site_class" class="form-control" value="{{ $seismic->site_class }}">
        </div>

        <div class="form-group">
            <label>Importance Factor</label>
            <input type="number" step="0.01" name="importance_factor" class="form-control" value="{{ $seismic->importance_factor }}">
        </div>

        <div class="form-group">
            <label>Soil Factor</label>
            <input type="number" step="0.01" name="soil_factor" class="form-control" value="{{ $seismic->soil_factor }}">
        </div>

        <div class="form-group">
            <label>Ss</label>
            <input type="number" step="0.01" name="Ss" class="form-control" value="{{ $seismic->Ss }}">
        </div>

        <div class="form-group">
            <label>S1</label>
            <input type="number" step="0.01" name="S1" class="form-control" value="{{ $seismic->S1 }}">
        </div>

        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" class="form-control" rows="3">{{ $seismic->notes }}</textarea>
        </div>

        <button class="btn btn-success">Update</button>
        <a href="{{ route('seismic.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection