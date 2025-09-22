@extends('admins.layouts.master')

@section('content')
<div class="container my-4">
    <h2>Add Seismic Parameter</h2>

    <form action="{{ route('seismic.store') }}" method="POST">
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
            <input type="text" name="standard_code" class="form-control" placeholder="TCVN9386-2012 / ASCE7-10" required>
        </div>

        <div class="form-group">
            <label>agR (Peak Ground Accel.)</label>
            <input type="number" step="0.01" name="agR" class="form-control">
        </div>

        <div class="form-group">
            <label>Site Class</label>
            <input type="text" name="site_class" class="form-control" placeholder="C / D / E">
        </div>

        <div class="form-group">
            <label>Importance Factor</label>
            <input type="number" step="0.01" name="importance_factor" class="form-control">
        </div>

        <div class="form-group">
            <label>Soil Factor (S)</label>
            <input type="number" step="0.01" name="soil_factor" class="form-control">
        </div>

        <div class="form-group">
            <label>Ss (Short Period Spectral Accel.)</label>
            <input type="number" step="0.01" name="Ss" class="form-control">
        </div>

        <div class="form-group">
            <label>S1 (1s Period Spectral Accel.)</label>
            <input type="number" step="0.01" name="S1" class="form-control">
        </div>

        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" class="form-control" rows="3"></textarea>
        </div>

        <button class="btn btn-success">Save</button>
        <a href="{{ route('seismic.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection