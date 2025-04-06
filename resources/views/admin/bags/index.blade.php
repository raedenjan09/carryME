@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Manage Bags</h1>
        <div>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-file-excel"></i> Import Excel
            </button>
            <a href="{{ route('bags.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Add New Bag
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered" id="bagsTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('bags.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Import Bags</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Excel File</label>
                        <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls" required>
                        <div class="form-text">
                            Download the <a href="{{ asset('templates/bags_import_template.xlsx') }}" class="text-primary">template file</a> first
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <h6>Instructions:</h6>
                        <ol class="mb-0">
                            <li>Download the template file</li>
                            <li>Fill in your bag data</li>
                            <li>Upload the filled template</li>
                            <li>Click Import</li>
                        </ol>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#bagsTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('bags.index') }}", // Ensure this route exists
    columns: [
        { data: 'name', name: 'name' },
        { data: 'description', name: 'description' },
        { data: 'price', name: 'price' },
        { data: 'category', name: 'category' },
        { data: 'image', name: 'image', orderable: false, searchable: false },
        { data: 'action', name: 'action', orderable: false, searchable: false }
    ]
});
});

function deleteBag(id) {
    if (confirm('Are you sure you want to delete this bag?')) {
        $.ajax({
            url: `/admin/bags/${id}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    $('#bagsTable').DataTable().ajax.reload();
                }
            }
        });
    }
}

function restoreBag(id) {
    $.ajax({
        url: `/admin/bags/${id}/restore`,
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(response) {
            if(response.success) {
                $('#bagsTable').DataTable().ajax.reload();
            }
        }
    });
}
</script>
@endpush

@push('styles')
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush
@endsection