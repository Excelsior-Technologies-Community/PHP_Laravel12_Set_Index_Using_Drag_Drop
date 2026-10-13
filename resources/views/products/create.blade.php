@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold d-flex align-items-center gap-2">
                    <i class="fas fa-boxes text-primary"></i>
                    <span>Create New Product</span>
                </h4>
                <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Catalog
                </a>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
                    @csrf

                    <!-- Section 1: Basic Information -->
                    <h5 class="fw-bold mb-3 text-primary d-flex align-items-center gap-2">
                        <i class="fas fa-circle-info"></i> 1. Product Identification
                    </h5>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Product Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Wireless Gaming Headset" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="sku" class="form-label fw-semibold">SKU (Auto-Generated if blank)</label>
                            <input type="text" class="form-control font-mono @error('sku') is-invalid @enderror" 
                                   id="sku" name="sku" value="{{ old('sku') }}" placeholder="e.g. PRD-HEAD-1024">
                            @error('sku')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="barcode" class="form-label fw-semibold">Barcode / EAN</label>
                            <input type="text" class="form-control font-mono @error('barcode') is-invalid @enderror" 
                                   id="barcode" name="barcode" value="{{ old('barcode') }}" placeholder="e.g. 890123456789">
                            @error('barcode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label fw-semibold">Product Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" placeholder="Enter key features, specs, and details...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Section 2: Pricing, Discounts & Inventory -->
                    <h5 class="fw-bold mb-3 text-primary d-flex align-items-center gap-2">
                        <i class="fas fa-tags"></i> 2. Pricing &amp; Stock Inventory
                    </h5>

                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label for="price" class="form-label fw-semibold">Original Price ($) *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" 
                                       class="form-control @error('price') is-invalid @enderror" 
                                       id="price" name="price" value="{{ old('price') }}" placeholder="0.00" required>
                            </div>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="discount_price" class="form-label fw-semibold">Promotional / Discount Price ($)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" 
                                       class="form-control @error('discount_price') is-invalid @enderror" 
                                       id="discount_price" name="discount_price" value="{{ old('discount_price') }}" placeholder="e.g. 79.99">
                            </div>
                            <span class="text-muted small" style="font-size:11px;">Must be less than regular price</span>
                            @error('discount_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="discount_end_date" class="form-label fw-semibold">Offer Valid Until</label>
                            <input type="date" class="form-control @error('discount_end_date') is-invalid @enderror" 
                                   id="discount_end_date" name="discount_end_date" value="{{ old('discount_end_date') }}">
                            @error('discount_end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="stock_quantity" class="form-label fw-semibold">Stock Quantity</label>
                            <input type="number" min="0" class="form-control @error('stock_quantity') is-invalid @enderror" 
                                   id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 10) }}">
                            @error('stock_quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Section 3: Media Uploads -->
                    <h5 class="fw-bold mb-3 text-primary d-flex align-items-center gap-2">
                        <i class="fas fa-images"></i> 3. Media &amp; Product Images
                    </h5>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="thumbnail" class="form-label fw-semibold">Primary Thumbnail Image</label>
                            <input type="file" class="form-control @error('thumbnail') is-invalid @enderror" 
                                   id="thumbnail" name="thumbnail" accept="image/*">
                            <div class="form-text small">Accepted: JPG, PNG, WEBP (Max 3MB). Displayed in catalog list.</div>
                            @error('thumbnail')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="gallery_images" class="form-label fw-semibold">Gallery Images (Multiple)</label>
                            <input type="file" class="form-control @error('gallery_images') is-invalid @enderror" 
                                   id="gallery_images" name="gallery_images[]" accept="image/*" multiple>
                            <div class="form-text small">Select multiple photos for product carousel preview.</div>
                            @error('gallery_images')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Section 4: Product Variants -->
                    <h5 class="fw-bold mb-3 text-primary d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-layer-group"></i> 4. Product Variants (Size, Color, Edition)</span>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddVariant">
                            <i class="fas fa-plus me-1"></i> Add Variant
                        </button>
                    </h5>

                    <div id="variantsContainer" class="d-flex flex-column gap-2 mb-4">
                        <!-- Variant rows injected dynamically via JS -->
                    </div>
                    <input type="hidden" name="variants_json" id="variantsJson">

                    <!-- Section 5: Status -->
                    <div class="mb-4 form-check form-switch p-3 bg-body-tertiary rounded-3">
                        <input type="checkbox" class="form-check-input ms-0 me-2" id="is_active" name="is_active" value="1" checked>
                        <label class="form-check-label fw-bold" for="is_active">
                            Active Product (Immediately visible in Catalog)
                        </label>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2">
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="fas fa-check me-1"></i> Create Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const variantsContainer = document.getElementById('variantsContainer');
        const btnAddVariant = document.getElementById('btnAddVariant');
        const variantsJsonInput = document.getElementById('variantsJson');
        const productForm = document.getElementById('productForm');

        function createVariantRow(name = '', sku = '', price = '', stock = '') {
            const div = document.createElement('div');
            div.className = 'variant-row row g-2 align-items-center bg-body p-2 border rounded';
            div.innerHTML = `
                <div class="col-md-4">
                    <input type="text" class="form-control form-control-sm variant-name" placeholder="Variant Name (e.g. Red / 128GB)" value="${name}" required>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control form-control-sm font-mono variant-sku" placeholder="Variant SKU" value="${sku}">
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" class="form-control form-control-sm variant-price" placeholder="Price ($)" value="${price}">
                </div>
                <div class="col-md-2">
                    <input type="number" min="0" class="form-control form-control-sm variant-stock" placeholder="Stock Qty" value="${stock}">
                </div>
                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-variant" title="Remove Variant">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;

            div.querySelector('.btn-remove-variant').addEventListener('click', function() {
                div.remove();
            });

            variantsContainer.appendChild(div);
        }

        btnAddVariant.addEventListener('click', function() {
            createVariantRow();
        });

        // Pack variants before submit
        productForm.addEventListener('submit', function() {
            const rows = document.querySelectorAll('.variant-row');
            const data = [];
            rows.forEach(r => {
                const name = r.querySelector('.variant-name').value.trim();
                const sku = r.querySelector('.variant-sku').value.trim();
                const price = r.querySelector('.variant-price').value;
                const stock = r.querySelector('.variant-stock').value;

                if (name) {
                    data.push({
                        name: name,
                        sku: sku,
                        price: price ? parseFloat(price) : null,
                        stock: stock ? parseInt(stock) : 0
                    });
                }
            });
            variantsJsonInput.value = JSON.stringify(data);
        });
    });
</script>
@endpush