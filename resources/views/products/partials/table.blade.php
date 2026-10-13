@if($products->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="productsTable">
            <thead class="table-light border-bottom">
                <tr>
                    <th width="40" class="text-center">
                        <input type="checkbox" id="selectAllCheckbox" class="form-check-input product-checkbox-head">
                    </th>
                    <th width="80" class="text-center">
                        <span class="d-inline-flex align-items-center gap-1">
                            <i class="fas fa-arrow-down-1-9 text-muted small"></i> Order
                        </span>
                    </th>
                    @if(!$viewTrash)
                        <th width="60" class="text-center">Sort</th>
                    @endif
                    <th width="70">Item</th>
                    <th>Product &amp; SKU</th>
                    <th width="140">Price / Offer</th>
                    <th width="130">Inventory</th>
                    <th width="110">Status</th>
                    <th width="140" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="sortable-list" class="{{ $viewTrash ? 'trash-list' : '' }}">
                @foreach($products as $product)
                    <tr data-id="{{ $product->id }}" class="product-row {{ $viewTrash ? 'table-light opacity-75' : '' }}">
                        <!-- Checkbox -->
                        <td class="text-center">
                            <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" class="form-check-input product-checkbox">
                        </td>

                        <!-- Sort Order Badge -->
                        <td class="text-center font-mono">
                            <span class="badge bg-body-secondary text-body border order-badge px-2 py-1">
                                #{{ $product->sort_order }}
                            </span>
                        </td>

                        <!-- Visual Drag Handle (Only active in Catalog mode) -->
                        @if(!$viewTrash)
                            <td class="text-center">
                                <div class="drag-handle" title="Hover &amp; Drag to Reorder">
                                    <i class="fas fa-grip-vertical"></i>
                                </div>
                            </td>
                        @endif

                        <!-- Product Thumbnail -->
                        <td>
                            <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="product-thumbnail-img shadow-sm" loading="lazy">
                        </td>

                        <!-- Name, SKU & Barcode with Inline Quick Edit for Name -->
                        <td>
                            <div class="d-flex flex-column">
                                @if(!$viewTrash)
                                    <div class="editable-cell fw-bold text-body" 
                                         data-id="{{ $product->id }}" 
                                         data-field="name" 
                                         data-value="{{ $product->name }}"
                                         title="Double-click to Quick Edit Name">
                                        <span class="cell-text">{{ $product->name }}</span>
                                        <i class="fas fa-pencil edit-hint"></i>
                                    </div>
                                @else
                                    <span class="fw-bold text-decoration-line-through">{{ $product->name }}</span>
                                @endif

                                <div class="d-flex align-items-center gap-2 mt-1">
                                    @if($product->sku)
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis font-mono" style="font-size:10.5px;">
                                            <i class="fas fa-barcode me-1"></i>{{ $product->sku }}
                                        </span>
                                    @endif
                                    @if($product->barcode)
                                        <span class="text-muted font-mono" style="font-size:10px;" title="Barcode: {{ $product->barcode }}">
                                            #{{ $product->barcode }}
                                        </span>
                                    @endif
                                </div>

                                @if(!empty($product->variants) && count($product->variants) > 0)
                                    <div class="small text-muted mt-1" style="font-size:11px;">
                                        <i class="fas fa-layer-group text-primary me-1"></i>{{ count($product->variants) }} Variants
                                    </div>
                                @endif
                            </div>
                        </td>

                        <!-- Price & Discount with Inline Quick Edit -->
                        <td>
                            @if(!$viewTrash)
                                <div class="editable-cell" 
                                     data-id="{{ $product->id }}" 
                                     data-field="price" 
                                     data-value="{{ $product->price }}"
                                     title="Double-click to Quick Edit Price">
                                    <div class="d-flex flex-column">
                                        @if($product->has_discount)
                                            <span class="fw-bold text-danger">
                                                ${{ number_format($product->discount_price, 2) }}
                                            </span>
                                            <span class="text-muted text-decoration-line-through small" style="font-size:11px;">
                                                ${{ number_format($product->price, 2) }}
                                            </span>
                                        @else
                                            <span class="fw-bold cell-text">
                                                ${{ number_format($product->price, 2) }}
                                            </span>
                                        @endif
                                    </div>
                                    <i class="fas fa-pencil edit-hint"></i>
                                </div>
                            @else
                                <span class="fw-bold">${{ number_format($product->price, 2) }}</span>
                            @endif
                        </td>

                        <!-- Stock / Inventory with Quick Edit -->
                        <td>
                            @if(!$viewTrash)
                                <div class="editable-cell" 
                                     data-id="{{ $product->id }}" 
                                     data-field="stock_quantity" 
                                     data-value="{{ $product->stock_quantity }}"
                                     title="Double-click to Quick Edit Stock Quantity">
                                    <div class="d-flex align-items-center gap-1">
                                        @if($product->stock_status === 'in_stock')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                <i class="fas fa-check-circle me-1"></i>{{ $product->stock_quantity }} In Stock
                                            </span>
                                        @elseif($product->stock_status === 'low_stock')
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                                <i class="fas fa-triangle-exclamation me-1"></i>Low: {{ $product->stock_quantity }}
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                                <i class="fas fa-circle-xmark me-1"></i>Out of Stock
                                            </span>
                                        @endif
                                    </div>
                                    <i class="fas fa-pencil edit-hint"></i>
                                </div>
                            @else
                                <span class="badge bg-secondary">Qty: {{ $product->stock_quantity }}</span>
                            @endif
                        </td>

                        <!-- Status Badge / Toggle Switch -->
                        <td>
                            @if(!$viewTrash)
                                <div class="form-check form-switch cursor-pointer" title="Click to Toggle Active/Inactive">
                                    <input class="form-check-input status-toggle-switch" 
                                           type="checkbox" 
                                           role="switch" 
                                           data-id="{{ $product->id }}" 
                                           {{ $product->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-semibold text-{{ $product->is_active ? 'success' : 'muted' }}">
                                        {{ $product->is_active ? 'Active' : 'Draft' }}
                                    </label>
                                </div>
                            @else
                                <span class="badge bg-secondary">Deleted</span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="text-end">
                            @if(!$viewTrash)
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-outline-secondary" title="Full Edit">
                                        <i class="fas fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger single-delete-btn" data-id="{{ $product->id }}" data-name="{{ $product->name }}" title="Move to Recycle Bin">
                                        <i class="fas fa-trash-can"></i>
                                    </button>
                                </div>
                            @else
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-success single-restore-btn" data-id="{{ $product->id }}" data-name="{{ $product->name }}" title="Restore Product">
                                        <i class="fas fa-trash-can-arrow-up me-1"></i> Restore
                                    </button>
                                    <button type="button" class="btn btn-danger single-force-delete-btn" data-id="{{ $product->id }}" data-name="{{ $product->name }}" title="Permanent Purge">
                                        <i class="fas fa-fire"></i>
                                    </button>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="p-5 text-center">
        <div class="mb-3 text-muted">
            <i class="fas fa-box-open fa-3x opacity-50"></i>
        </div>
        <h5 class="fw-bold text-muted">No Products Found</h5>
        <p class="text-muted small">
            @if($viewTrash)
                The Recycle Bin is empty. No deleted products found.
            @else
                No products match the selected search keywords or filter criteria.
            @endif
        </p>
        @if(!$viewTrash)
            <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i> Add First Product
            </a>
        @endif
    </div>
@endif
