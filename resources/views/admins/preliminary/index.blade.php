@extends('admins.layouts.master')

@section('content')
    <h1>Preliminary Data for Project: {{ $project->name }}</h1>

    <!-- Link to Create New -->
    <a href="{{ route('preliminary.create', $project) }}" class="btn btn-primary mb-3">Create New Preliminary</a>

    <!-- Parameters (nếu có) -->
    @if($params)
        <h2>Parameters</h2>
        <table class="table table-bordered">
            <tr>
                <th>Location</th>
                <th>Dead Load Roof (kN/m²)</th>
                <th>Live Load Roof (kN/m²)</th>
                <th>Eave Height (m)</th>
                <th>Total Spans</th>
                <th>Max Span (m)</th>
                <th>Crane</th>
                <th>Actions</th>
            </tr>
            <tr>
                <td>{{ $params->location }}</td>
                <td>{{ number_format($params->dead_load_roof, 2) }}</td>
                <td>{{ number_format($params->live_load_roof, 2) }}</td>
                <td>{{ number_format($params->eave_height, 2) }}</td>
                <td>{{ $params->total_spans }}</td>
                <td>{{ number_format($params->max_span, 2) }}</td>
                <td>
                    @if($params->has_crane)
                        {{ $params->crane_details['count'] ?? 0 }} crane(s), {{ $params->crane_details['crane_weight'] ?? 0 }} tons
                    @else
                        No Crane
                    @endif
                </td>
                <td>
                    <a href="{{ route('preliminary.edit', $project) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('preliminary.destroy', $project) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
        </table>
    @else
        <p>No preliminary parameters defined yet.</p>
    @endif

    <!-- Suggestions (nếu có) -->
    @if($suggestions->count() > 0)
        <h2>Suggestions</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Section</th>
                    <th>Similarity Score (%)</th>
                    <th>From Project</th>
                    <th>Reason / Formula</th>
                </tr>
            </thead>
            <tbody>
                @foreach($suggestions as $sug)
                    <tr>
                        <td>{{ $sug->section->name }} ({{ $sug->section->type }})</td>
                        <td>{{ number_format($sug->similarity_score, 2) }}</td>
                        <td>{{ $sug->meta['from_project'] ?? 'N/A' }}</td>
                        <td>{{ $sug->meta['reason'] ?? 'N/A' }} <br> Formula: {{ $sug->meta['manual_formula'] ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <a href="{{ route('preliminary.show', $project) }}" class="btn btn-info">View Detailed Suggestions</a>
    @else
        <p>No suggestions available. Create parameters to generate suggestions.</p>
    @endif
@endsection