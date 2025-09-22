@extends('admins.layouts.master')

@section('content')
    <h1>Edit Preliminary for Project: {{ $project->name }}</h1>
    <form method="POST" action="{{ route('preliminary.update', $project) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="location">Location</label>
            <input type="text" name="location" class="form-control" value="{{ old('location', $params->location ?? '') }}" required>
        </div>
        <div class="form-group">
            <label for="dead_load_roof">Dead Load Roof (kN/m²)</label>
            <input type="number" step="0.01" name="dead_load_roof" class="form-control" value="{{ old('dead_load_roof', $params->dead_load_roof ?? 0) }}" required>
        </div>
        <div class="form-group">
            <label for="live_load_roof">Live Load Roof (kN/m²)</label>
            <input type="number" step="0.01" name="live_load_roof" class="form-control" value="{{ old('live_load_roof', $params->live_load_roof ?? 0) }}" required>
        </div>
        <div class="form-group">
            <label for="eave_height">Eave Height (m)</label>
            <input type="number" step="0.01" name="eave_height" class="form-control" value="{{ old('eave_height', $params->eave_height ?? 0) }}" required>
        </div>
        <div class="form-group">
            <label for="total_spans">Total Spans</label>
            <input type="number" name="total_spans" class="form-control" value="{{ old('total_spans', $params->total_spans ?? 1) }}" required>
        </div>
        <div class="form-group">
            <label for="max_span">Max Span (m)</label>
            <input type="number" step="0.01" name="max_span" class="form-control" value="{{ old('max_span', $params->max_span ?? 0) }}" required>
        </div>
        <div class="form-group">
            <label for="has_crane">Has Crane?</label>
            <input type="checkbox" name="has_crane" id="has_crane" {{ old('has_crane', $params->has_crane ?? false) ? 'checked' : '' }}>
        </div>
        <div id="crane_details" style="display: {{ old('has_crane', $params->has_crane ?? false) ? 'block' : 'none' }};">
            <label>Crane Details</label>
            <input type="number" step="0.01" name="crane_details[crane_weight]" placeholder="Crane Weight (tons)" value="{{ old('crane_details.crane_weight', $params->crane_details['crane_weight'] ?? '') }}">
            <input type="number" step="0.01" name="crane_details[hoist_weight]" placeholder="Hoist Weight (tons)" value="{{ old('crane_details.hoist_weight', $params->crane_details['hoist_weight'] ?? '') }}">
            <input type="text" name="crane_details[mode]" placeholder="Mode (e.g., A3)" value="{{ old('crane_details.mode', $params->crane_details['mode'] ?? '') }}">
            <input type="number" name="crane_details[count]" placeholder="Count" value="{{ old('crane_details.count', $params->crane_details['count'] ?? '') }}">
        </div>
        <button type="submit" class="btn btn-primary">Update Parameters</button>
    </form>

    <script>
        document.getElementById('has_crane').addEventListener('change', function() {
            document.getElementById('crane_details').style.display = this.checked ? 'block' : 'none';
        });
    </script>
@endsection