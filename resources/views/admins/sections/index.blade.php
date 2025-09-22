@extends('admins.layouts.master')

@section('content')
<div class="container my-4">
    <h2>Tra cứu thông số thép Hòa Phát</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-sm" id="sectionsTable">
            <thead class="thead-light">
                <tr>
                    <th>Name</th>
                    <th>Type <input type="text" class="form-control form-control-sm column-filter" data-column="1" placeholder="Lọc loại"></th>
                    <th>Diameter (mm)</th>
                    <th>Thickness (mm)</th>
                    <th>Weight/m (kg/m)</th>
                    <th>Price with VAT (VND/kG)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sections as $section)
                <tr>
                    <td>{{ $section->name }}</td>
                    <td>{{ $section->type }}</td>
                    <td>{{ $section->diameter }}</td>
                    <td>{{ $section->thickness }}</td>
                    <td>{{ $section->weight_per_m }}</td>
                    <td>{{ $section->standard_ref }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    const table = $('#sectionsTable').DataTable({
        "order": [[0, "asc"]],       // sắp xếp mặc định theo Name
        "pageLength": 50,
        "columnDefs": [
            { "orderable": false, "targets": [1] } // cột Type sẽ có input lọc, không click sắp xếp
        ]
    });

    // Chỉ lọc cột Type
    $('.column-filter').on('keyup change', function() {
        table.column($(this).data('column')).search(this.value).draw();
    });
});
</script>
@endpush