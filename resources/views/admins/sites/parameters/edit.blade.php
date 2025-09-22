@extends('admins.layouts.master')

@section('content')
<div class="container my-4">
    <h2>Edit Site Parameter</h2>

    <form action="{{ route('parameters.update', $parameter->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group mb-2">
            <label>Name</label>
            <input type="text" name="name" value="{{ $parameter->name }}" class="form-control" required>
        </div>

        <div class="form-group mb-2">
            <label>Country</label>
            <input type="text" name="country" value="{{ $parameter->country }}" class="form-control">
        </div>

        <div class="form-group mb-2">
            <label>Latitude</label>
            <input type="number" step="0.000001" name="latitude" value="{{ $parameter->latitude }}" class="form-control">
        </div>

        <div class="form-group mb-2">
            <label>Longitude</label>
            <input type="number" step="0.000001" name="longitude" value="{{ $parameter->longitude }}" class="form-control">
        </div>

        <div class="form-group mb-2">
            <label>Elevation (m)</label>
            <input type="number" step="0.01" name="elevation" value="{{ $parameter->elevation }}" class="form-control">
        </div>

        <div class="form-group mb-2">
            <label>Terrain Category</label>
            <input type="text" name="terrain_category" value="{{ $parameter->terrain_category }}" class="form-control">
        </div>

        <div class="form-group mb-2">
            <label>Exposure Category</label>
            <input type="text" name="exposure_category" value="{{ $parameter->exposure_category }}" class="form-control">
        </div>

        <div class="form-group mb-2">
            <label>Topography Factor</label>
            <input type="number" step="0.01" name="topography_factor" value="{{ $parameter->topography_factor }}" class="form-control">
        </div>

        <div class="form-group mb-2">
            <label>Importance Category</label>
            <input type="text" name="importance_category" value="{{ $parameter->importance_category }}" class="form-control">
        </div>

        <button class="btn btn-success mt-2">Update</button>
        <a href="{{ route('parameters.index') }}" class="btn btn-secondary mt-2">Cancel</a>
    </form>
</div>
@endsection