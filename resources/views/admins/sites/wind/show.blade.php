@extends('admins.layouts.master')

@section('content')
<div class="container my-4">
    <h2>Wind Parameter Detail</h2>
    <div class="card">
        <div class="card-body">
            <p><strong>Site:</strong> {{ $wind->site->name ?? '-' }}</p>
            <p><strong>Standard:</strong> {{ $wind->standard_code }}</p>
            <p><strong>Basic Wind Speed:</strong> {{ $wind->basic_wind_speed }} m/s</p>
            <p><strong>Region:</strong> {{ $wind->map_region }}</p>
            <p><strong>Terrain Factors:</strong> {{ json_encode($wind->terrain_factors) }}</p>
            <p><strong>Directionality Factor:</strong> {{ $wind->directionality_factor }}</p>
            <p><strong>Gust Effect Factor:</strong> {{ $wind->gust_effect_factor }}</p>
            <p><strong>Conversion:</strong> {{ json_encode($wind->conversion_to_other) }}</p>
            <p><strong>Notes:</strong> {{ $wind->notes }}</p>
        </div>
    </div>
    <a href="{{ route('wind.index') }}" class="btn btn-secondary mt-3">Back</a>
</div>
@endsection