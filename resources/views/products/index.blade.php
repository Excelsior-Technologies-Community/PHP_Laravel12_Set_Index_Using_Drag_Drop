@extends('layouts.app')

@section('content')
<div class="row g-3 mb-4">
    <!-- Stat 1: Total Products -->
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Total Catalog</div>
                    <div class="h3 fw-bold mb-0 text-primary mt-1" id="statTotal">{{ $totalProducts }}</div>
                </div>
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
            </div>
            <div class="small text-muted mt-2">
                <i class="fas fa-calendar-day me-1"></i>Today: <span class="fw-semibold text-body" id="statToday">+{{ $todayProducts }}</span>
            </div>
        </div>
    </div>

    <!-- Stat 2: Active Products -->
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Live Active</div>
                    <div class="h3 fw-bold mb-0 text-success mt-1" id="statActive">{{ $activeProducts }}</div>
                </div>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="fas fa-circle-check"></i>
                </div>
            </div>
            <div class="small text-muted mt-2">
                Visible to customers
            </div>
        </div>
    </div>

    <!-- Stat 3: Inactive / Draft Products -->
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Drafts / Inactive</div>
                    <div class="h3 fw-bold mb-0 text-secondary mt-1" id="statInactive">{{ $inactiveProducts }}</div>
                </div>
                <div class="stat-icon bg-secondary-subtle text-secondary">
                    <i class="fas fa-circle-pause"></i>
                </div>
            </div>
            <div class="small text-muted mt-2">
                Hidden from catalog
            </div>
        </div>
    </div>

    <!-- Stat 4: Low Stock Alert -->
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Low / Out of Stock</div>
                    <div class="h3 fw-bold mb-0 text-warning mt-1" id="statLowStock">{{ $lowStockCount }}</div>
                </div>
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="small text-muted mt-2">
                Threshold: &le; 5 units
            </div>
        </div>
    </div>
</div>

<!-- Main Products Card -->
<div class="card shadow-sm border-0 mb-4">
    <!-- Card Header -->
    <div class="card-header bg-transparent border-bottom py-3 px-3 px-md-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-2">
                <h4 class="mb-0 fw-bold d-flex align-items-center gap-2">
                    @if($viewTrash)
                        <i class="fas fa-trash-can text-danger"></i>
                        <span>Recycle Bin</span>
                        <span class="badge bg-danger-subtle text-danger fs-6 fw-semibold">Trashed Items</span>
                    @else
                        <i class="fas fa-sort text-primary"></i>
                        <span>Product Sorter &amp; Manager</span>
                    @endif
                </h4>
            </div>

            <!-- Header Quick Actions -->
            <div class="d-flex align-items-center gap-2">
                @if(!$viewTrash)
                    <button type="button" id="btnResetOrder" class="btn btn-sm btn-outline-warning d-inline-flex align-items-center gap-1">
                        <i class="fas fa-arrow-rotate-left"></i> Reset Order
                    </button>
                    <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 fw-semibold">
                        <i class="fas fa-plus"></i> New Product
                    </a>
                @else
                    <form action="{{ route('products.empty-trash') }}" method="POST" id="emptyTrashForm">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger d-inline-flex align-items-center gap-1">
                            <i class="fas fa-fire"></i> Empty Recycle Bin
                        </button>
                    </form>
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-secondary d-inline-flex align-items-center gap-1">
                        <i class="fas fa-arrow-left"></i> Back to Catalog
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Live Filters Section -->
    <div class="card-body bg-body-tertiary border-bottom py-3 px-3 px-md-4">
        <form id="filterForm" class="row g-2 align-items-end" onsubmit="return false;">
            <!-- Instant Live Search Bar (Debounced) -->
            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold text-muted mb-1">
                    <i class="fas fa-magnifying-glass me-1"></i> Live Instant Search
                </label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="liveSearchInput" name="search" class="form-control" 
                           placeholder="Type to search name, SKU, barcode..." 
                           value="{{ request('search') }}" autocomplete="off">
                    <button type="button" class="btn btn-outline-secondary" id="clearSearchBtn" title="Clear">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Status Filter -->
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">Status</label>
                <select name="status" id="filterStatus" class="form-select form-select-sm filter-input">
                    <option value="">All Statuses</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active Only</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Draft / Inactive</option>
                </select>
            </div>

            <!-- Stock Status Filter -->
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">Stock Level</label>
                <select name="stock_status" id="filterStock" class="form-select form-select-sm filter-input">
                    <option value="">All Inventory</option>
                    <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>In Stock (>5)</option>
                    <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Low Stock (&le;5)</option>
                    <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>

            <!-- Price Range (Min & Max) -->
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">Min Price ($)</label>
                <input type="number" step="0.01" min="0" name="min_price" id="filterMinPrice" 
                       class="form-control form-control-sm filter-input" placeholder="Min $" value="{{ request('min_price') }}">
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">Max Price ($)</label>
                <input type="number" step="0.01" min="0" name="max_price" id="filterMaxPrice" 
                       class="form-control form-control-sm filter-input" placeholder="Max $" value="{{ request('max_price') }}">
            </div>
        </form>
    </div>

    <!-- Bulk Actions Toolbar -->
    <div class="card-body py-2 px-3 px-md-4 border-bottom bg-body d-flex flex-wrap align-items-center justify-content-between gap-2">
        <form method="POST" action="{{ route('products.bulk-action') }}" id="bulkForm" class="d-flex align-items-center gap-2 flex-wrap">
            @csrf
            <div class="input-group input-group-sm" style="width: auto;">
                <label class="input-group-text bg-body-tertiary fw-semibold">
                    <span id="selectedCountBadge" class="badge bg-primary me-1">0</span> Selected
                </label>
                <select name="action" id="bulkActionSelect" class="form-select form-select-sm" required style="width:170px;">
                    <option value="">-- Bulk Actions --</option>
                    @if(!$viewTrash)
                        <option value="activate">Activate Selected</option>
                        <option value="deactivate">Deactivate Selected</option>
                        <option value="delete">Move to Recycle Bin</option>
                    @else
                        <option value="restore">Restore Selected</option>
                        <option value="force_delete">Permanently Delete</option>
                    @endif
                </select>
                <button type="submit" class="btn btn-dark" id="btnApplyBulk">
                    <i class="fas fa-play me-1"></i> Apply
                </button>
            </div>
        </form>

        <div class="d-flex align-items-center gap-2 small text-muted">
            @if(!$viewTrash)
                <span class="d-inline-flex align-items-center gap-1">
                    <i class="fas fa-hand-pointer text-primary"></i> <strong>Tip:</strong> Drag <i class="fas fa-grip-vertical text-muted"></i> to reorder. Double-click cells to Quick-Edit.
                </span>
            @endif
        </div>
    </div>

    <!-- Table Container (Updated via AJAX) -->
    <div id="tableContainer">
        @include('products.partials.table', ['products' => $products, 'viewTrash' => $viewTrash])
    </div>

    <!-- Card Footer / Pagination (Updated via AJAX) -->
    <div class="card-footer bg-transparent py-3 px-3 px-md-4 border-top" id="paginationContainer">
        @include('products.partials.pagination', ['products' => $products])
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isTrashView = {{ $viewTrash ? 'true' : 'false' }};

        // ----------------------------------------------------
        // 1. SortableJS Drag & Drop with Visual Handle
        // ----------------------------------------------------
        let sortableInstance = null;

        function initSortable() {
            const listEl = document.getElementById('sortable-list');
            if (!listEl || isTrashView) return;

            if (sortableInstance) {
                sortableInstance.destroy();
            }

            sortableInstance = new Sortable(listEl, {
                handle: '.drag-handle',
                animation: 180,
                ghostClass: 'sortable-ghost',
                chosenClass: 'table-active',
                onEnd: function() {
                    updateProductOrder();
                }
            });
        }

        initSortable();

        function updateProductOrder() {
            const items = [];
            document.querySelectorAll('#sortable-list tr.product-row').forEach(function(row, index) {
                items.push(row.dataset.id);
                const badge = row.querySelector('.order-badge');
                if (badge) badge.textContent = '#' + (index + 1);
            });

            $.ajax({
                url: "{{ route('products.update-order') }}",
                method: "POST",
                data: { items: items },
                success: function(res) {
                    showToast(res.message || 'Order updated successfully.');
                },
                error: function() {
                    Swal.fire('Error', 'Unable to persist product order.', 'error');
                }
            });
        }

        // ----------------------------------------------------
        // 2. Live Debounced Search & Multi-Filters (No Reload)
        // ----------------------------------------------------
        let filterTimer = null;

        function fetchFilteredProducts(page = 1) {
            const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
            params.set('page', page);
            if (isTrashView) params.set('trash', '1');

            $.ajax({
                url: "{{ route('products.index') }}?" + params.toString(),
                method: "GET",
                dataType: "json",
                beforeSend: function() {
                    $('#tableContainer').css('opacity', '0.5');
                },
                success: function(res) {
                    $('#tableContainer').html(res.html).css('opacity', '1');
                    $('#paginationContainer').html(res.pagination);

                    // Update Counters
                    if (res.total !== undefined) $('#statTotal').text(res.total);
                    if (res.active !== undefined) $('#statActive').text(res.active);
                    if (res.inactive !== undefined) $('#statInactive').text(res.inactive);
                    if (res.low_stock !== undefined) $('#statLowStock').text(res.low_stock);

                    // Re-bind plugins
                    initSortable();
                    updateSelectedCount();
                },
                error: function() {
                    $('#tableContainer').css('opacity', '1');
                }
            });
        }

        // Debounced search input
        $('#liveSearchInput').on('input', function() {
            clearTimeout(filterTimer);
            filterTimer = setTimeout(() => {
                fetchFilteredProducts(1);
            }, 300);
        });

        // Filter dropdowns and price inputs
        $('.filter-input').on('change input', function() {
            clearTimeout(filterTimer);
            filterTimer = setTimeout(() => {
                fetchFilteredProducts(1);
            }, 350);
        });

        // Clear search button
        $('#clearSearchBtn').on('click', function() {
            $('#liveSearchInput').val('');
            fetchFilteredProducts(1);
        });

        // AJAX Pagination click handler
        $(document).on('click', '.pagination-ajax-link', function(e) {
            e.preventDefault();
            const targetPage = $(this).data('page') || 1;
            fetchFilteredProducts(targetPage);
        });

        // ----------------------------------------------------
        // 3. Inline Quick Edit (Double Click to edit Name, Price, Stock)
        // ----------------------------------------------------
        $(document).on('dblclick', '.editable-cell', function() {
            const cell = $(this);
            if (cell.find('input').length > 0) return; // Already editing

            const id = cell.data('id');
            const field = cell.data('field');
            const currentVal = cell.attr('data-value') ?? cell.text().trim();

            const inputType = (field === 'price' || field === 'stock_quantity') ? 'number' : 'text';
            const stepAttr = field === 'price' ? 'step="0.01"' : '';

            const input = $(`<input type="${inputType}" ${stepAttr} class="form-control form-control-sm" style="max-width:140px; display:inline-block;" value="${currentVal}">`);

            cell.html(input);
            input.focus();

            function commitEdit() {
                const newVal = input.val();
                if (newVal === currentVal) {
                    fetchFilteredProducts();
                    return;
                }

                $.ajax({
                    url: `/products/${id}/quick-update`,
                    method: 'POST',
                    data: { field: field, value: newVal },
                    success: function(res) {
                        showToast(res.message, 'success');
                        fetchFilteredProducts();
                    },
                    error: function(xhr) {
                        const err = xhr.responseJSON?.message || 'Quick edit failed.';
                        showToast(err, 'error');
                        fetchFilteredProducts();
                    }
                });
            }

            input.on('blur', commitEdit);
            input.on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    commitEdit();
                } else if (e.key === 'Escape') {
                    fetchFilteredProducts();
                }
            });
        });

        // ----------------------------------------------------
        // 4. Live Status Switch Toggle (Single Click)
        // ----------------------------------------------------
        $(document).on('change', '.status-toggle-switch', function() {
            const id = $(this).data('id');
            const checked = this.checked;

            $.ajax({
                url: `/products/${id}/quick-update`,
                method: 'POST',
                data: { field: 'is_active', value: checked ? 1 : 0 },
                success: function(res) {
                    showToast(res.message, 'success');
                    fetchFilteredProducts();
                },
                error: function() {
                    showToast('Failed to toggle status.', 'error');
                }
            });
        });

        // ----------------------------------------------------
        // 5. Select All & Bulk Selection Tracking
        // ----------------------------------------------------
        function updateSelectedCount() {
            const count = $('.product-checkbox:checked').length;
            $('#selectedCountBadge').text(count);
        }

        $(document).on('click', '#selectAllCheckbox', function() {
            $('.product-checkbox').prop('checked', this.checked);
            updateSelectedCount();
        });

        $(document).on('change', '.product-checkbox', function() {
            updateSelectedCount();
            const total = $('.product-checkbox').length;
            const checked = $('.product-checkbox:checked').length;
            $('#selectAllCheckbox').prop('checked', total > 0 && total === checked);
        });

        // Bulk Form Submit Check
        $('#bulkForm').on('submit', function(e) {
            const checked = $('.product-checkbox:checked').length;
            const action = $('#bulkActionSelect').val();

            if (checked === 0) {
                e.preventDefault();
                Swal.fire('No Selection', 'Please select at least one product using the checkboxes.', 'warning');
                return;
            }

            if (action === 'delete') {
                e.preventDefault();
                Swal.fire({
                    title: 'Move to Recycle Bin?',
                    text: `${checked} products will be moved to the Recycle Bin.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Move to Trash',
                    confirmButtonColor: '#EF4444'
                }).then(result => {
                    if (result.isConfirmed) {
                        e.target.submit();
                    }
                });
            } else if (action === 'force_delete') {
                e.preventDefault();
                Swal.fire({
                    title: 'Permanently Purge?',
                    text: `${checked} products will be permanently destroyed and cannot be recovered!`,
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Purge Forever',
                    confirmButtonColor: '#DC2626'
                }).then(result => {
                    if (result.isConfirmed) {
                        e.target.submit();
                    }
                });
            }
        });

        // ----------------------------------------------------
        // 6. Reset Order
        // ----------------------------------------------------
        $('#btnResetOrder').on('click', function() {
            Swal.fire({
                title: 'Reset Product Order?',
                text: 'Products will be reset back to their natural database creation order.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Reset',
                confirmButtonColor: '#F59E0B'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('products.reset-order') }}",
                        method: 'POST',
                        success: function(res) {
                            showToast(res.message, 'success');
                            fetchFilteredProducts();
                        }
                    });
                }
            });
        });

        // ----------------------------------------------------
        // 7. Single Delete, Restore & Force Delete Handlers
        // ----------------------------------------------------
        $(document).on('click', '.single-delete-btn', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');

            Swal.fire({
                title: 'Move to Recycle Bin?',
                html: `Product <strong>${name}</strong> will be moved to Recycle Bin. You can restore it anytime.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Move to Trash',
                confirmButtonColor: '#EF4444'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/products/${id}`,
                        method: 'DELETE',
                        success: function(res) {
                            showToast(res.message, 'success');
                            fetchFilteredProducts();
                        }
                    });
                }
            });
        });

        $(document).on('click', '.single-restore-btn', function() {
            const id = $(this).data('id');
            $.ajax({
                url: `/products/${id}/restore`,
                method: 'POST',
                success: function(res) {
                    showToast(res.message, 'success');
                    fetchFilteredProducts();
                }
            });
        });

        $(document).on('click', '.single-force-delete-btn', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');

            Swal.fire({
                title: 'Delete Permanently?',
                html: `Are you sure you want to permanently purge <strong>${name}</strong>? This cannot be undone!`,
                icon: 'error',
                showCancelButton: true,
                confirmButtonText: 'Permanently Purge',
                confirmButtonColor: '#DC2626'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/products/${id}/force-delete`,
                        method: 'DELETE',
                        success: function(res) {
                            showToast(res.message, 'success');
                            fetchFilteredProducts();
                        }
                    });
                }
            });
        });

        // Empty Trash Form
        $('#emptyTrashForm').on('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Empty Recycle Bin?',
                text: 'All trashed products and their uploaded images will be permanently purged!',
                icon: 'error',
                showCancelButton: true,
                confirmButtonText: 'Yes, Empty Trash',
                confirmButtonColor: '#DC2626'
            }).then(result => {
                if (result.isConfirmed) {
                    e.target.submit();
                }
            });
        });
    });
</script>
@endpush