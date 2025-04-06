<div class="btn-group">
    <button class="btn btn-sm btn-{{ $user->is_active ? 'danger' : 'success' }}" 
            onclick="updateUserStatus({{ $user->id }})">
        {{ $user->is_active ? 'Deactivate' : 'Activate' }}
    </button>
    
    <div class="btn-group">
        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown">
            Change Role
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#" 
                   onclick="updateUserRole({{ $user->id }}, 'user')">User</a></li>
            <li><a class="dropdown-item" href="#" 
                   onclick="updateUserRole({{ $user->id }}, 'admin')">Admin</a></li>
        </ul>
    </div>
</div>