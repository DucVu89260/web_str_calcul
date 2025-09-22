@extends('admins.layouts.master')

@section('content')
<div class="container my-4">
    <h2>Seismic Parameter Detail</h2>
    <div class="card">
        <div class="card-body">
            <p><strong>Site:</strong> {{ $seismic->site->name ?? '-' }}</p>
            <p><strong>Standard:</strong> {{ $seismic->standard_code }}</p>
            <p><strong>agR:</strong> {{ $seismic->agR }}</p>
            <p><strong>Site Class:</strong> {{ $seismic->site_class }}</p>
            <p><strong>Importance Factor:</strong> {{ $seismic->importance_factor }}</p>
            <p><strong>Soil Factor:</strong> {{ $seismic->soil_factor }}</p>
            <p><strong>Ss:</strong> {{ $seismic->Ss }}</p>
            <p><strong>S1:</strong> {{ $seismic->S1 }}</p>
            <p><strong>Notes:</strong> {{ $seismic->notes }}</p>
        </div>
    </div>
    <a href="{{ route('seismic.index') }}" class="btn btn-secondary mt-3">Back</a>
</div>
@endsection