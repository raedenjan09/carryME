@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Order Management</h2>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="ordersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Order Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody">
                        <!-- Table content will be loaded here -->
                    </tbody>
                </table>
                <div id="loading" class="text-center d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateStatusForm">
                    <input type="hidden" id="orderId">
                    <div class="mb-3">
                        <label for="orderStatus" class="form-label">Status</label>
                        <select class="form-select" id="orderStatus">
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmUpdateStatus()">Update Status</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Notification</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastBody"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentPage = 1;
    let ordersTable;
    let statusModal;
    let loading = document.getElementById('loading');

    document.addEventListener('DOMContentLoaded', function() {
        ordersTable = document.getElementById('ordersTable');
        statusModal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
        loadOrders();
    });

    async function loadOrders() {
        try {
            showLoading();
            const response = await fetch(`/admin/orders?page=${currentPage}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to load orders');
            }

            const data = await response.json();
            renderOrders(data.data);
            hideLoading();
        } catch (error) {
            hideLoading();
            showToast('error', 'Failed to load orders');
            console.error('Error:', error);
        }
    }

    function showLoading() {
        if (loading) loading.classList.remove('d-none');
    }

    function hideLoading() {
        if (loading) loading.classList.add('d-none');
    }

    function renderOrders(orders) {
        const tbody = document.getElementById('ordersTableBody');
        if (!tbody) return;

        tbody.innerHTML = '';
        if (!orders.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center">No orders found</td></tr>';
            return;
        }

        orders.forEach(order => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>#${order.id}</td>
                <td>${order.customer}</td>
                <td>$${parseFloat(order.total).toFixed(2)}</td>
                <td>
                    <span class="badge bg-${getStatusColor(order.status)}">
                        ${order.status.toUpperCase()}
                    </span>
                </td>
                <td>${new Date(order.created_at).toLocaleDateString()}</td>
                <td>
                    <button class="btn btn-primary btn-sm" 
                            onclick="showUpdateStatus(${order.id}, '${order.status}')">
                        <i class="bi bi-pencil"></i> Update Status
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    function getStatusColor(status) {
        const colors = {
            'pending': 'warning',
            'processing': 'info',
            'completed': 'success',
            'shipped': 'primary',
            'delivered': 'success',
            'cancelled': 'danger'
        };
        return colors[status] || 'secondary';
    }

    function showUpdateStatus(orderId, currentStatus) {
        document.getElementById('orderId').value = orderId;
        document.getElementById('orderStatus').value = currentStatus;
        statusModal.show();
    }

    async function confirmUpdateStatus() {
        const orderId = document.getElementById('orderId').value;
        const status = document.getElementById('orderStatus').value;

        try {
            const response = await fetch(`/admin/orders/${orderId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: status,
                    _method: 'PATCH'
                })
            });

            if (!response.ok) throw new Error('Failed to update status');

            statusModal.hide();
            await loadOrders();
            showToast('success', 'Order status updated successfully');
        } catch (error) {
            showToast('error', 'Failed to update order status');
            console.error('Error:', error);
        }
    }

    function showToast(type, message) {
        const toastEl = document.getElementById('toast');
        const toastBody = document.getElementById('toastBody');
        if (!toastEl || !toastBody) return;

        toastBody.textContent = message;
        toastEl.classList.remove('bg-success', 'bg-danger');
        toastEl.classList.add(`bg-${type === 'success' ? 'success' : 'danger'}`);
        
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
    }


    function renderOrders(orders) {
    const tbody = document.getElementById('ordersTableBody');
    if (!tbody) return;

    tbody.innerHTML = '';
    if (!orders.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No orders found</td></tr>';
        return;
    }

    orders.forEach(order => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>#${order.id}</td>
            <td>${order.customer}</td>
            <td>$${parseFloat(order.total).toFixed(2)}</td>
            <td>
                <span class="badge bg-${getStatusColor(order.status)}">
                    ${order.status.toUpperCase()}
                </span>
            </td>
            <td>${new Date(order.created_at).toLocaleDateString()}</td>
            <td>
                <div class="btn-group" role="group">
                    <a href="/admin/orders/${order.id}" class="btn btn-info btn-sm">
                        <i class="bi bi-eye"></i> Details
                    </a>
                    <button class="btn btn-primary btn-sm" 
                            onclick="showUpdateStatus(${order.id}, '${order.status}')">
                        <i class="bi bi-pencil"></i> Status
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
}
</script>
@endpush

@push('styles')
<style>
    .table td { 
        vertical-align: middle; 
    }
    .badge { 
        font-size: 0.875em; 
    }
    .toast {
        background-color: white;
    }
    .toast-body {
        color: white;
    }
</style>
@endpush
```