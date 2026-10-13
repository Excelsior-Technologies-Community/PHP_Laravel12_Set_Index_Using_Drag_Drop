@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold d-flex align-items-center gap-2">
                    <i class="fas fa-pen-to-square text-primary"></i>
                    <span>Edit Product: {{ $product->name }}</span>
                </h4>
                <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Catalog
                </a>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data" id="productEditForm">
                    @csrf
                    @method('PUT')

                    <!-- Section 1: Basic Information -->
                    <h5 class="fw-bold mb-3 text-primary d-flex align-items-center gap-2">
                        <i class="fas fa-circle-info"></i> 1. Product Identification
                    </h5>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Product Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $product->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="sku" class="form-label fw-semibold">SKU</label>
                            <input type="text" class="form-control font-mono @error('sku') is-invalid @enderror" 
                                   id="sku" name="sku" value="{{ old('sku', $product->sku) }}">
                            @error('sku')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="barcode" class="form-label fw-semibold">Barcode / EAN</label>
                            <input type="text" class="form-control font-mono @error('barcode') is-invalid @enderror" 
                                   id="barcode" name="barcode" value="{{ old('barcode', $product->barcode) }}">
                            @error('barcode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label fw-semibold">Product Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
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
                                       id="price" name="price" value="{{ old('price', $product->price) }}" required>
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
                                       id="discount_price" name="discount_price" value="{{ old('discount_price', $product->discount_price) }}" placeholder="e.g. 79.99">
                            </div>
                            @error('discount_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="discount_end_date" class="form-label fw-semibold">Offer Valid Until</label>
                            <input type="date" class="form-control @error('discount_end_date') is-invalid @enderror" 
                                   id="discount_end_date" name="discount_end_date" 
                                   value="{{ old('discount_end_date', $product->discount_end_date ? $product->discount_end_date->format('Y-m-d') : '') }}">
                            @error('discount_end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="stock_quantity" class="form-label fw-semibold">Stock Quantity</label>
                            <input type="number" min="0" class="form-control @error('stock_quantity') is-invalid @enderror" 
                                   id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}">
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
                            <label for="thumbnail" class="form-label fw-semibold">Change Thumbnail</label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <img src="{{ $product->thumbnail_url }}" alt="Current" class="product-thumbnail-img shadow-sm" style="width:60px; height:60px;">
                                <div class="small text-muted">Current preview image</div>
                            </div>
                            <input type="file" class="form-control @error('thumbnail') is-invalid @enderror" 
                                   id="thumbnail" name="thumbnail" accept="image/*">
                            @error('thumbnail')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="gallery_images" class="form-label fw-semibold">Append Gallery Images (Multiple)</label>
                            @if(!empty($product->gallery_images))
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    @foreach($product->gallery_images as $gImg)
                                        <img src="{{ asset('storage/' . $gImg) }}" alt="Gallery" class="rounded border" style="width:40px; height:40px; object-fit:cover;">
                                    @endforeach
                                </div>
                            @endif
                            <input type="file" class="form-control @error('gallery_images') is-invalid @enderror" 
                                   id="gallery_images" name="gallery_images[]" accept="image/*" multiple>
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
                        <!-- Variants populated by script -->
                    </div>
                    <input type="hidden" name="variants_json" id="variantsJson">

                    <!-- Section 5: Status -->
                    <div class="mb-4 form-check form-switch p-3 bg-body-tertiary rounded-3">
                        <input type="checkbox" class="form-check-input ms-0 me-2" id="is_active" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="is_active">
                            Active Product (Visible in catalog)
                        </label>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2">
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="fas fa-save me-1"></i> Save Changes
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
        const productEditForm = document.getElementById('productEditForm');

        const existingVariants = @json($product->variants ?? []);

        function createVariantRow(name = '', sku = '', price = '', stock = '') {
            const div = document.createElement('div');
            div.className = 'variant-row row g-2 align-items-center bg-body p-2 border rounded';
            div.innerHTML = `
                <div class="col-md-4">
                    <input type="text" class="form-control form-control-sm variant-name" placeholder="Variant Name (e.g. Blue / 256GB)" value="${name || ''}" required>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control form-control-sm font-mono variant-sku" placeholder="Variant SKU" value="${sku || ''}">
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" class="form-control form-control-sm variant-price" placeholder="Price ($)" value="${price || ''}">
                </div>
                <div class="col-md-2">
                    <input type="number" min="0" class="form-control form-control-sm variant-stock" placeholder="Stock Qty" value="${stock !== undefined && stock !== null ? stock : ''}">
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

        // Populate existing variants
        if (Array.isArray(existingVariants) && existingVariants.length > 0) {
            existingVariants.forEach(v => {
                createVariantRow(v.name, v.sku, v.price, v.stock);
            });
        }

        btnAddVariant.addEventListener('click', function() {
            createVariantRow();
        });

        productEditForm.addEventListener('submit', function() {
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