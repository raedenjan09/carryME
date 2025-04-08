```blade
@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">User Management</h2>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="usersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Registered Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.users.index') }}",
            columns: [
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'role', name: 'role' },
                { 
                    data: 'created_at', 
                    name: 'created_at',
                    render: function(data) { 
                        return new Date(data).toLocaleDateString(); 
                    } 
                },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });

    function updateUserStatus(userId) {
        if (confirm('Are you sure you want to change this user\'s status?')) {
            $.ajax({
                url: `/admin/users/${userId}/status`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'PATCH'
                },
                success: function(response) {
                    $('#usersTable').DataTable().ajax.reload();
                    toastr.success(response.message);
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON.message || 'An error occurred');
                }
            });
        }
    }

    function updateUserRole(userId, role) {
        if (confirm(`Are you sure you want to change this user's role to ${role}?`)) {
            $.ajax({
                url: `/admin/users/${userId}/role`,
                type: 'POST',
                data: {
                    role: role,
                    _token: '{{ csrf_token() }}',
                    _method: 'PATCH'
                },
                success: function(response) {
                    $('#usersTable').DataTable().ajax.reload();
                    toastr.success(response.message);
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON.message || 'An error occurred');
                }
            });
        }
    }
</script>
@endpush
