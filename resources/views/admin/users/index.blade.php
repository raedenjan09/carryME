@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">User Management</h2>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered" id="usersTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
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
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'role', name: 'role' },
            { data: 'status', name: 'status' },
            { 
                data: 'created_at',
                name: 'created_at',
                render: function(data) {
                    return new Date(data).toLocaleDateString();
                }
            },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });
});

function updateUserStatus(userId) {
    $.ajax({
        url: `/admin/users/${userId}/status`,
        type: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                $('#usersTable').DataTable().ajax.reload();
            } else {
                toastr.error(response.message);
            }
        },
        error: function() {
            toastr.error('Failed to update user status');
        }
    });
}

function updateUserRole(userId, role) {
    $.ajax({
        url: `/admin/users/${userId}/role`,
        type: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        data: { role: role },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                $('#usersTable').DataTable().ajax.reload();
            } else {
                toastr.error(response.message);
            }
        },
        error: function() {
            toastr.error('Failed to update user role');
        }
    });
}
</script>
@endpush
@endsection