@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-star-fill text-warning"></i> Review Management
        </h1>
    </div>

    <!-- Review Stats Cards -->
    <div class="row mb-4">
        <!-- Total Reviews Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Reviews
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalReviews">
                                <span class="loading-placeholder">Loading...</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-chat-square-text fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Rating Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Average Rating
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="avgRating">
                                <span class="loading-placeholder">Loading...</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Review List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="reviewsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>User</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Posted Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Review Modal -->
<div class="modal fade" id="deleteReviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Review</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this review? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .loading-placeholder {
        color: #6c757d;
        font-style: italic;
    }
    .table td {
        vertical-align: middle !important;
    }
    .star-rating {
        color: #ffc107;
    }
    .review-text {
        max-width: 300px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endpush

@push('scripts')
<script>
    let reviewIdToDelete = null;
    
    $(document).ready(function() {
        const table = $('#reviewsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.reviews.index') }}",
            columns: [
                { 
                    data: 'bag.name', 
                    name: 'bag.name',
                    render: function(data) {
                        return data || 'N/A';
                    }
                },
                { 
                    data: 'reviewer_name',
                    name: 'reviewer_name',
                    render: function(data) {
                        return `<span class="font-weight-bold">${data || 'Anonymous'}</span>`;
                    }
                },
                { 
                    data: 'rating', 
                    name: 'rating',
                    render: function(data) {
                        return `<span class="star-rating">${'★'.repeat(data)}</span> <small>(${data}/5)</small>`;
                    }
                },
                { 
                    data: 'comment', 
                    name: 'comment',
                    render: function(data) {
                        return `<div class="review-text" title="${data}">${data}</div>`;
                    }
                },
                { 
                    data: 'created_at', 
                    name: 'created_at',
                    render: function(data) { 
                        return new Date(data).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    } 
                },
                { 
                    data: 'action', 
                    name: 'action', 
                    orderable: false, 
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <button onclick="showDeleteModal(${row.id})" 
                                    class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i>
                            </button>`;
                    }
                }
            ],
            order: [[4, 'desc']]
        });

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Update stats
        updateStats();
        setInterval(updateStats, 60000); // Update every minute
    });

    function showDeleteModal(reviewId) {
        reviewIdToDelete = reviewId;
        $('#deleteReviewModal').modal('show');
    }

    $('#confirmDelete').click(function() {
        if (reviewIdToDelete) {
            deleteReview(reviewIdToDelete);
            $('#deleteReviewModal').modal('hide');
        }
    });

    function deleteReview(reviewId) {
        $.ajax({
            url: `/admin/reviews/${reviewId}`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'DELETE'
            },
            success: function(response) {
                $('#reviewsTable').DataTable().ajax.reload();
                updateStats();
                toastr.success('Review deleted successfully');
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.message || 'An error occurred');
            }
        });
    }

   
function updateStats() {
    $.ajax({
        url: "{{ route('admin.reviews.stats') }}",
        type: 'GET',
        success: function(data) {
            $('#totalReviews').text(data.total);
            const avgRating = parseFloat(data.average).toFixed(1);
            const stars = '★'.repeat(Math.round(data.average));
            $('#avgRating').html(`${avgRating} <span class="text-warning">${stars}</span>`);
        },
        error: function() {
            $('#totalReviews').text('N/A');
            $('#avgRating').text('N/A');
        }
    });
}
</script>
@endpush
