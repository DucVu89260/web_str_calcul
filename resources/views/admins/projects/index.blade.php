@extends('admins.layouts.master')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Danh sách Projects</h4>
    <a href="{{ route('projects.create') }}" class="btn btn-success">+ Thêm Project</a>
</div>

<table class="table table-bordered table-hover">
    <thead class="thead-dark">
        <tr>
            <th>#</th>
            <th>Tên</th>
            <th>Mô tả</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @forelse($projects as $project)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $project->name }}</td>
            <td>{{ $project->description }}</td>
            <td>
                <a href="{{ route('projects.show', $project) }}" class="btn btn-info btn-sm">Xem</a>
                <a href="{{ route('projects.edit', $project) }}" class="btn btn-primary btn-sm">Sửa</a>
                <form action="{{ route('projects.destroy', $project) }}" method="POST" class="d-inline" onsubmit="return confirm('Xoá?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm">Xoá</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="4" class="text-center text-muted">Chưa có Project nào</td></tr>
        @endforelse
    </tbody>
</table>

{{ $projects->links('pagination::bootstrap-4') }}
@endsection