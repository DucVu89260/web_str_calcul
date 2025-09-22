@extends('admins.layouts.master')

@section('content')
<div class="container my-4">
    <h2>Wind Parameters</h2>

    <!-- Search form -->
    <form method="GET" action="{{ route('wind.index') }}" class="form-inline mb-3">
        <input type="text" name="search" class="form-control mr-2"
               placeholder="Search by City/Province..."
               value="{{ $search ?? '' }}">
        <button type="submit" class="btn btn-outline-primary">Search</button>
        <a href="{{ route('wind.index') }}" class="btn btn-outline-secondary ml-2">Reset</a>
    </form>

    <a href="{{ route('wind.create') }}" class="btn btn-primary mb-3">+ Add Wind Parameter</a>

    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Site</th>
                <th>Standard</th>
                <th>
                    Basic Wind Speed (m/s) 
                    <small class="text-muted d-block">
                        (3s gust, 50-year return, per Standard)
                    </small>
                </th>
                <th>ASCE7-10 Eq. (m/s)</th>
                <th>Region</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($winds as $w)
            <tr>
                <td>{{ $loop->iteration + ($winds->currentPage() - 1) * $winds->perPage() }}</td>
                <td>{{ $w->site->name ?? '-' }}</td>
                <td>{{ $w->standard_code }}</td>
                <td>
                    {{ $w->basic_wind_speed ?? '-' }}
                    @if($w->standard_code === 'TCVN2737-2023')
                        <small class="text-muted">(V₃s,50)</small>
                    @elseif($w->standard_code === 'ASCE7-10')
                        <small class="text-muted">(3s gust, 50 yr)</small>
                    @endif
                </td>
                <td>
                    @php
                        $asce = null;
                        if($w->conversion_to_other){
                            $conv = json_decode($w->conversion_to_other, true);
                            if(isset($conv['ASCE7-10']['factor_speed'])){
                                $asce = $w->basic_wind_speed * $conv['ASCE7-10']['factor_speed'];
                            }
                        }
                    @endphp
                    {{ $asce ? number_format($asce,1) : '-' }}
                </td>
                <td>{{ $w->map_region ?? '-' }}</td>
                <td>
                    <a href="{{ route('wind.show',$w->id) }}" class="btn btn-sm btn-info">View</a>
                    <a href="{{ route('wind.edit',$w->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('wind.destroy',$w->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this?')">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">No records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $winds->appends(['search' => $search])->links() }}
    </div>
</div>
@endsection