@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Order Management</h2>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered" id="ordersTable">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Payment Method</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="updateStatusForm">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Update Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const table = $('#ordersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.orders.index') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'customer', name: 'customer' },
            { 
                data: 'total', 
                name: 'total',
                render: function(data) {
                    // Convert to number and format with 2 decimal places
                    const amount = Number(data) || 0;
                    return '$' + amount.toFixed(2);
                }
            },
            { 
                data: 'status',
                render: function(data) {
                    const badges = {
                        'pending': 'bg-warning',
                        'processing': 'bg-info',
                        'shipped': 'bg-primary',
                        'delivered': 'bg-success',
                        'cancelled': 'bg-danger'
                    };
                    return `<span class="badge ${badges[data]}">${data}</span>`;
                }
            },
            { data: 'payment_method', name: 'payment_method' },
            { 
                data: 'created_at',
                render: function(data) {
                    return new Date(data).toLocaleDateString();
                }
            },
            {
                data: 'action',
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-primary" onclick="viewOrder(${row.id})">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-success" onclick="updateStatus(${row.id})">
                            <i class="bi bi-pencil"></i>
                        </button>
                    `;
                }
            }
        ]
    });
});

function updateStatus(orderId) {
    $('#updateStatusModal').modal('show');
    
    $('#updateStatusForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: `/admin/orders/${orderId}/status`,
            type: 'PATCH',
            data: {
                status: $(this).find('[name="status"]').val(),
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $('#updateStatusModal').modal('hide');
                    $('#ordersTable').DataTable().ajax.reload();
                    toastr.success('Order status updated successfully');
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Failed to update order status');
            }
        });
    });
}

function viewOrder(orderId) {
    window.location.href = `/admin/orders/${orderId}`;
}
</script>
@endpush