@extends('admins.layouts.master')

@section('content')
<div class="container">
    <h2>Generated Load Combinations</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Combination</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($combinations as $i => $combo)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $combo }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('load_combinations.export') }}" class="btn btn-success">Export CSV</a>
    <a href="{{ route('load_combinations.index') }}" class="btn btn-secondary">Back</a>
</div>
@endsection