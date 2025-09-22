@extends('admins.layouts.master')

@section('content')
<div class="container my-4">
    <h2>Seismic Parameters</h2>
    <a href="{{ route('seismic.create') }}" class="btn btn-primary mb-3">+ Add Seismic Parameter</a>

    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Site</th>
                <th>Standard</th>
                <th>agR</th>
                <th>Site Class</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($seismics as $s)
            <tr>
                <td>{{ $s->site->name ?? '-' }}</td>
                <td>{{ $s->standard_code }}</td>
                <td>{{ $s->agR ?? '-' }}</td>
                <td>{{ $s->site_class ?? '-' }}</td>
                <td>
                    <a href="{{ route('seismic.show',$s->id) }}" class="btn btn-sm btn-info">View</a>
                    <a href="{{ route('seismic.edit',$s->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('seismic.destroy',$s->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection