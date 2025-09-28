@extends('admins.layouts.master')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Danh sách các liên kết</h3>
        <a href="{{ route('connections.create', ['type' => 'moment', 'standard' => 'aisc360-10']) }}" class="btn btn-primary">
            + Tạo liên kết Moment (AISC 360-10)
        </a>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Loại liên kết</h5>
            <ul class="list-group">
                <li class="list-group-item">
                    Moment — <a href="{{ route('connections.create', ['type' => 'moment', 'standard' => 'aisc360-10']) }}">AISC 360-10</a>
                </li>
                <li class="list-group-item">Shear — (sẽ thêm)</li>
                <li class="list-group-item">Baseplate — (sẽ thêm)</li>
            </ul>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">Tiêu chuẩn hỗ trợ</h5>
            <ul class="list-group">
                <li class="list-group-item">AISC 360-10</li>
                <li class="list-group-item">Eurocode (sẽ thêm)</li>
                <li class="list-group-item">TCVN (sẽ thêm)</li>
            </ul>
        </div>
    </div>
</div>
@endsection