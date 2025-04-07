<div class="card">
    <div class="list-group list-group-flush">
        <a href="{{ route('user.account') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('user.account') ? 'active' : '' }}">
            <i class="bi bi-person"></i> Profile
        </a>
        <a href="{{ route('user.account.orders') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('user.account.orders') ? 'active' : '' }}">
            <i class="bi bi-box"></i> My Orders
        </a>
        <a href="{{ route('user.account.reviews') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('user.account.reviews') ? 'active' : '' }}">
            <i class="bi bi-star"></i> My Reviews
        </a>
    </div>
</div>