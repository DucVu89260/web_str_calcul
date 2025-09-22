@extends('admins.layouts.master')

@section('content')
    <h1>Preliminary Suggestions for Project: {{ $project->name }}</h1>

    <!-- Table Gợi Ý -->
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

    <!-- Filter Form cho Lọc Nhanh -->
    <form method="GET" action="{{ route('preliminary.show', $project) }}">
        <input type="number" name="min_score" placeholder="Min Score" value="{{ request('min_score') }}">
        <input type="text" name="section_type" placeholder="Section Type" value="{{ request('section_type') }}">
        <button type="submit" class="btn btn-secondary">Filter</button>
    </form>

    <!-- Chart Diễn Họa Similarity Scores (sử dụng Chart.js) -->
    <canvas id="similarityChart" width="400" height="200"></canvas>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('similarityChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [@foreach($suggestions as $sug) '{{ $sug->section->name }}', @endforeach],
                datasets: [{
                    label: 'Similarity Score',
                    data: [@foreach($suggestions as $sug) {{ $sug->similarity_score }}, @endforeach],
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: { y: { beginAtZero: true, max: 100 } }
            }
        });
    </script>

    <!-- Diagram Sơ Bộ (SVG đơn giản cho khung thép) -->
    <h2>Structural Frame Visualization</h2>
    <svg width="600" height="300" style="border:1px solid black;">
        <!-- Mái (roof) -->
        <line x1="50" y1="50" x2="550" y2="50" stroke="black" stroke-width="2" />
        <!-- Cột (columns) dựa trên total_spans -->
        @for($i = 0; $i <= $params->total_spans; $i++)
            <line x1="{{ 50 + $i * (500 / $params->total_spans) }}" y1="50" x2="{{ 50 + $i * (500 / $params->total_spans) }}" y2="{{ 50 + $params->eave_height * 20 }}" stroke="black" stroke-width="2" /> <!-- Scale height x20 for viz -->
        @endfor
        <!-- Crane nếu có -->
        @if($params->has_crane)
            <rect x="100" y="70" width="50" height="20" fill="blue" /> <!-- Crane symbol -->
            <text x="100" y="100">Crane: {{ $params->crane_details['count'] ?? 0 }} units</text>
        @endif
        <text x="50" y="280">Max Span: {{ $params->max_span }}m | Eave Height: {{ $params->eave_height }}m</text>
    </svg>
@endsection