@extends('admins.layouts.master')

@section('content')
<div class="container my-4">
    <h2>Site Parameters</h2>
    <a href="{{ route('parameters.create') }}" class="btn btn-primary mb-2">+ Add Site</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th><th>Country</th><th>Terrain</th><th>Exposure</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sites as $s)
            <tr>
                <td>{{ $s->name }}</td>
                <td>{{ $s->country }}</td>
                <td>{{ $s->terrain_category }}</td>
                <td>{{ $s->exposure_category }}</td>
                <td>
                    <a href="{{ route('parameters.show',$s->id) }}" class="btn btn-sm btn-info">View</a>
                    <a href="{{ route('parameters.edit',$s->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('parameters.destroy',$s->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection