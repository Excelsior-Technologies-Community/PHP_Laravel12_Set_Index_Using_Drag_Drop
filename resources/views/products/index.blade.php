@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Products List (Drag to Reorder)</h4>
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Product
        </a>
    </div>
    
    <div class="card-body">
        @if($products->isEmpty())
            <div class="alert alert-info">
                No products found. <a href="{{ route('products.create') }}">Create your first product</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover" id="sortable-table">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 50px;">Sort</th>
                            <th>Product Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-list">
                        @foreach($products as $product)
                        <tr data-id="{{ $product->id }}" class="product-item">
                            <td class="align-middle">
                                <span class="badge bg-secondary">{{ $product->sort_order }}</span>
                            </td>
                            <td class="align-middle handle">
                                <i class="fas fa-bars fa-lg"></i>
                            </td>
                            <td class="align-middle">{{ $product->name }}</td>
                            <td class="align-middle">{{ Str::limit($product->description, 50) }}</td>
                            <td class="align-middle">${{ number_format($product->price, 2) }}</td>
                            <td class="align-middle">
                                <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="align-middle">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('products.edit', $product->id) }}" 
                                       class="btn btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('products.destroy', $product->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this product?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> Drag and drop rows to reorder. Order is automatically saved.
                </small>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sortableList = document.getElementById('sortable-list');
    
    // Initialize SortableJS
    const sortable = new Sortable(sortableList, {
        handle: '.handle',
        animation: 150,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',
        
        onEnd: function(evt) {
            updateOrder();
        }
    });
    
    // Function to update order via AJAX
    function updateOrder() {
        const itemIds = [];
        const rows = document.querySelectorAll('#sortable-list tr');
        
        rows.forEach((row, index) => {
            const id = row.getAttribute('data-id');
            itemIds.push(id);
            
            // Update the order number display
            const orderBadge = row.querySelector('.badge.bg-secondary');
            if (orderBadge) {
                orderBadge.textContent = index + 1;
            }
        });
        
        // Send AJAX request
        $.ajax({
            url: '{{ route("products.update-order") }}',
            type: 'POST',
            data: {
                items: itemIds,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showToast('Order updated successfully!', 'success');
                }
            },
            error: function(xhr) {
                showToast('Error updating order. Please try again.', 'error');
                console.error('Update error:', xhr.responseText);
            }
        });
    }
    
    // Toast notification function
    function showToast(message, type = 'info') {
        // Remove existing toast
        $('.custom-toast').remove();
        
        const toastClass = type === 'success' ? 'bg-success' : 
                          type === 'error' ? 'bg-danger' : 'bg-info';
        
        const toast = $(`
            <div class="custom-toast position-fixed bottom-0 end-0 p-3">
                <div class="toast show" role="alert">
                    <div class="toast-header ${toastClass} text-white">
                        <strong class="me-auto">
                            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                            ${type.charAt(0).toUpperCase() + type.slice(1)}
                        </strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            </div>
        `);
        
        $('body').append(toast);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
});
</script>
@endpush