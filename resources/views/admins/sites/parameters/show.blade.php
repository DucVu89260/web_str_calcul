@extends('admins.layouts.master')

@section('content')
<div class="container my-4">
    <h2>Site Detail</h2>

    <table class="table table-bordered">
        <tr><th>Name</th><td>{{ $parameter->name }}</td></tr>
        <tr><th>Country</th><td>{{ $parameter->country }}</td></tr>
        <tr><th>Latitude</th><td>{{ $parameter->latitude }}</td></tr>
        <tr><th>Longitude</th><td>{{ $parameter->longitude }}</td></tr>
        <tr><th>Elevation</th><td>{{ $parameter->elevation }}</td></tr>
        <tr><th>Terrain Category</th><td>{{ $parameter->terrain_category }}</td></tr>
        <tr><th>Exposure Category</th><td>{{ $parameter->exposure_category }}</td></tr>
        <tr><th>Topography Factor</th><td>{{ $parameter->topography_factor }}</td></tr>
        <tr><th>Importance Category</th><td>{{ $parameter->importance_category }}</td></tr>
    </table>

    <a href="{{ route('parameters.edit', $parameter->id) }}" class="btn btn-warning">Edit</a>
    <a href="{{ route('parameters.index') }}" class="btn btn-secondary">Back</a>
</div>
@endsection