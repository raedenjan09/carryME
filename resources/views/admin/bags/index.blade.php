@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Manage Bags</h1>
        <div>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-file-excel"></i> Import Excel
            </button>
            <a href="{{ route('admin.bags.create') }}" class="btn btn-primary">
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
                        <th>Stock</th>
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
            <form action="{{ route('admin.bags.import') }}" method="POST" enctype="multipart/form-data">
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
<script>
    // JavaScript for DataTables and stock updates
</script>
@endpush
@endsection