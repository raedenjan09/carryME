@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Manage Users</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="usersTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.users.index') }}",
        columns: [
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'role', name: 'role'},
            {data: 'status', name: 'status'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        order: [[0, 'asc']],
        drawCallback: function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
});

function updateUserStatus(userId) {
    if (confirm('Are you sure you want to change this user\'s status?')) {
        $.ajax({
            url: `{{ url('admin/users') }}/${userId}/status`,
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    $('#usersTable').DataTable().ajax.reload();
                    toastr.success(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Error updating user status');
            }
        });
    }
}

function updateUserRole(userId, role) {
    if (confirm('Are you sure you want to change this user\'s role?')) {
        $.ajax({
            url: `{{ url('admin/users') }}/${userId}/role`,
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: { role: role },
            success: function(response) {
                if(response.success) {
                    $('#usersTable').DataTable().ajax.reload();
                    toastr.success(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Error updating user role');
            }
        });
    }
}
</script>
@endpush
@endsection