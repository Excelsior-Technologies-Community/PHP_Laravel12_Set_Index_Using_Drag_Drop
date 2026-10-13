<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display products with Live Filter + Multi-Filter + Pagination + Stats
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $status = $request->status;
        $stockStatus = $request->stock_status;
        $minPrice = $request->min_price;
        $maxPrice = $request->max_price;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $viewTrash = $request->boolean('trash');

        // Query Builder
        $query = $viewTrash ? Product::onlyTrashed() : Product::query();

        // 1. Keyword search (Name, Description, SKU, Barcode, Price)
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('sku', 'LIKE', "%{$search}%")
                    ->orWhere('barcode', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('price', 'LIKE', "%{$search}%");
            });
        }

        // 2. Active/Inactive Status Filter
        if ($status !== null && $status !== '') {
            $query->where('is_active', $status == '1');
        }

        // 3. Stock Status Filter
        if ($stockStatus === 'in_stock') {
            $query->where('stock_quantity', '>', 5);
        } elseif ($stockStatus === 'low_stock') {
            $query->where('stock_quantity', '>', 0)
                  ->whereRaw('stock_quantity <= COALESCE(low_stock_threshold, 5)');
        } elseif ($stockStatus === 'out_of_stock') {
            $query->where('stock_quantity', '<=', 0);
        }

        // 4. Price Range Filter
        if ($minPrice !== null && $minPrice !== '') {
            $query->where('price', '>=', (float) $minPrice);
        }
        if ($maxPrice !== null && $maxPrice !== '') {
            $query->where('price', '<=', (float) $maxPrice);
        }

        // 5. Date Range Filter
        if (!empty($dateFrom)) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Dashboard Statistics (from non-trashed products)
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', 1)->count();
        $inactiveProducts = Product::where('is_active', 0)->count();
        $todayProducts = Product::whereDate('created_at', today())->count();
        $trashedCount = Product::onlyTrashed()->count();
        $lowStockCount = Product::where('stock_quantity', '<=', 5)->count();

        // Sort order
        if ($viewTrash) {
            $query->orderByDesc('deleted_at');
        } else {
            $query->orderBy('sort_order')->orderBy('id');
        }

        // Pagination
        $products = $query->paginate(10)->withQueryString();

        // If AJAX / JSON request (Live Search / Filter / Pagination), return JSON
        if ($request->ajax() || $request->wantsJson()) {
            $html = view('products.partials.table', compact('products', 'viewTrash'))->render();
            $pagination = view('products.partials.pagination', compact('products'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'pagination' => $pagination,
                'total' => $products->total(),
                'active' => $activeProducts,
                'inactive' => $inactiveProducts,
                'today' => $todayProducts,
                'trash' => $trashedCount,
                'low_stock' => $lowStockCount,
            ]);
        }

        return view('products.index', compact(
            'products',
            'search',
            'status',
            'stockStatus',
            'minPrice',
            'maxPrice',
            'dateFrom',
            'dateTo',
            'viewTrash',
            'totalProducts',
            'activeProducts',
            'inactiveProducts',
            'todayProducts',
            'trashedCount',
            'lowStockCount'
        ));
    }

    /**
     * Show Create Form
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store Product with Images, Variants, Discount & Stock
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'barcode' => 'nullable|string|max:100|unique:products,barcode',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'discount_end_date' => 'nullable|date',
            'stock_quantity' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:3072',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:3072',
            'variants_json' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        // Auto Next Sort Order
        $highestOrder = Product::max('sort_order');
        $validated['sort_order'] = $highestOrder ? $highestOrder + 1 : 1;
        $validated['is_active'] = $request->has('is_active');
        $validated['stock_quantity'] = $request->input('stock_quantity', 0);
        $validated['low_stock_threshold'] = $request->input('low_stock_threshold', 5);

        // Upload Thumbnail
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('products/thumbnails', 'public');
            $validated['thumbnail'] = $path;
        }

        // Upload Gallery Images
        if ($request->hasFile('gallery_images')) {
            $gallery = [];
            foreach ($request->file('gallery_images') as $file) {
                $gallery[] = $file->store('products/gallery', 'public');
            }
            $validated['gallery_images'] = $gallery;
        }

        // Variants JSON decode
        if ($request->filled('variants_json')) {
            $decoded = json_decode($request->input('variants_json'), true);
            if (is_array($decoded)) {
                $validated['variants'] = $decoded;
            }
        }

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product created successfully with catalog specs.');
    }

    /**
     * Show Edit Form
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Update Product
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:100|unique:products,barcode,' . $product->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'discount_end_date' => 'nullable|date',
            'stock_quantity' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:3072',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:3072',
            'variants_json' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['stock_quantity'] = $request->input('stock_quantity', 0);
        $validated['low_stock_threshold'] = $request->input('low_stock_threshold', 5);

        // Upload new thumbnail if present
        if ($request->hasFile('thumbnail')) {
            if ($product->thumbnail && Storage::disk('public')->exists($product->thumbnail)) {
                Storage::disk('public')->delete($product->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('products/thumbnails', 'public');
        }

        // Append or replace gallery images
        if ($request->hasFile('gallery_images')) {
            $existing = $product->gallery_images ?? [];
            foreach ($request->file('gallery_images') as $file) {
                $existing[] = $file->store('products/gallery', 'public');
            }
            $validated['gallery_images'] = $existing;
        }

        // Variants JSON decode
        if ($request->has('variants_json')) {
            $decoded = json_decode($request->input('variants_json'), true);
            $validated['variants'] = is_array($decoded) ? $decoded : [];
        }

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Inline Quick Edit (AJAX inline name, price, stock, status)
     */
    public function quickUpdate(Request $request, Product $product)
    {
        $validated = $request->validate([
            'field' => 'required|string|in:name,price,discount_price,stock_quantity,is_active',
            'value' => 'required',
        ]);

        $field = $validated['field'];
        $value = $validated['value'];

        if ($field === 'is_active') {
            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        } elseif (in_array($field, ['price', 'discount_price'])) {
            $value = (float) $value;
        } elseif ($field === 'stock_quantity') {
            $value = (int) $value;
        }

        $product->update([$field => $value]);

        return response()->json([
            'success' => true,
            'message' => ucfirst(str_replace('_', ' ', $field)) . ' updated successfully.',
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => number_format($product->price, 2),
                'discount_price' => $product->discount_price ? number_format($product->discount_price, 2) : null,
                'stock_quantity' => $product->stock_quantity,
                'stock_status' => $product->stock_status,
                'is_active' => $product->is_active,
            ]
        ]);
    }

    /**
     * Soft Delete Product (Move to Recycle Bin)
     */
    public function destroy(Product $product)
    {
        $product->delete();
        $this->reorderProducts();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product moved to Recycle Bin.'
            ]);
        }

        return redirect()->route('products.index')->with('success', 'Product moved to Recycle Bin.');
    }

    /**
     * Restore product from Recycle Bin
     */
    public function restore($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();
        $this->reorderProducts();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Product '{$product->name}' restored successfully."
            ]);
        }

        return redirect()->route('products.index', ['trash' => 1])->with('success', "Product '{$product->name}' restored successfully.");
    }

    /**
     * Permanent Delete product and media files
     */
    public function forceDelete($id)
    {
        $product = Product::withTrashed()->findOrFail($id);

        if ($product->thumbnail && Storage::disk('public')->exists($product->thumbnail)) {
            Storage::disk('public')->delete($product->thumbnail);
        }
        if (!empty($product->gallery_images)) {
            foreach ($product->gallery_images as $img) {
                if (Storage::disk('public')->exists($img)) {
                    Storage::disk('public')->delete($img);
                }
            }
        }

        $product->forceDelete();
        $this->reorderProducts();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product permanently deleted.'
            ]);
        }

        return redirect()->route('products.index', ['trash' => 1])->with('success', 'Product permanently deleted.');
    }

    /**
     * Empty entire Recycle Bin
     */
    public function emptyTrash()
    {
        $trashed = Product::onlyTrashed()->get();
        foreach ($trashed as $product) {
            if ($product->thumbnail && Storage::disk('public')->exists($product->thumbnail)) {
                Storage::disk('public')->delete($product->thumbnail);
            }
            if (!empty($product->gallery_images)) {
                foreach ($product->gallery_images as $img) {
                    if (Storage::disk('public')->exists($img)) {
                        Storage::disk('public')->delete($img);
                    }
                }
            }
            $product->forceDelete();
        }

        return redirect()->route('products.index')->with('success', 'Recycle Bin emptied successfully.');
    }

    /**
     * Drag & Drop Update Order (Optimized with DB Transaction)
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array'
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->items as $index => $id) {
                Product::where('id', $id)->update([
                    'sort_order' => $index + 1
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Product order saved successfully.'
        ]);
    }

    /**
     * Reorder products after mutations
     */
    private function reorderProducts()
    {
        $products = Product::orderBy('sort_order')->orderBy('id')->get();
        foreach ($products as $index => $product) {
            $product->update([
                'sort_order' => $index + 1
            ]);
        }
    }

    /**
     * Bulk Product Actions (activate, deactivate, trash, restore, force_delete)
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'action' => 'required|string|in:activate,deactivate,delete,restore,force_delete'
        ]);

        $ids = $request->product_ids;

        switch ($request->action) {
            case 'delete':
                Product::whereIn('id', $ids)->delete();
                $this->reorderProducts();
                $message = count($ids) . " products moved to Recycle Bin.";
                break;

            case 'restore':
                Product::onlyTrashed()->whereIn('id', $ids)->restore();
                $this->reorderProducts();
                $message = count($ids) . " products restored from Recycle Bin.";
                break;

            case 'force_delete':
                $items = Product::onlyTrashed()->whereIn('id', $ids)->get();
                foreach ($items as $item) {
                    $item->forceDelete();
                }
                $this->reorderProducts();
                $message = count($ids) . " products permanently purged.";
                break;

            case 'activate':
                Product::whereIn('id', $ids)->update(['is_active' => true]);
                $message = count($ids) . " products activated.";
                break;

            case 'deactivate':
                Product::whereIn('id', $ids)->update(['is_active' => false]);
                $message = count($ids) . " products deactivated.";
                break;

            default:
                return back()->with('error', 'Invalid action');
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Reset Product Sorting to ID order
     */
    public function resetOrder()
    {
        $products = Product::orderBy('id')->get();
        foreach ($products as $index => $product) {
            $product->update([
                'sort_order' => $index + 1
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Products reset to default order.'
        ]);
    }

    /**
     * Export Products to CSV (preserving current sort order)
     */
    public function exportCsv(Request $request)
    {
        $fileName = 'products_export_' . date('Y-m-d_His') . '.csv';
        $products = Product::orderBy('sort_order')->orderBy('id')->get();

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'Sort Order', 'ID', 'SKU', 'Barcode', 'Product Name', 'Price', 
            'Discount Price', 'Discount End Date', 'Stock Quantity', 'Stock Status', 
            'Active', 'Description', 'Created At'
        ];

        $callback = function () use ($products, $columns) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->sort_order,
                    $product->id,
                    $product->sku,
                    $product->barcode,
                    $product->name,
                    $product->price,
                    $product->discount_price ?? '',
                    $product->discount_end_date ? $product->discount_end_date->format('Y-m-d') : '',
                    $product->stock_quantity,
                    ucfirst(str_replace('_', ' ', $product->stock_status)),
                    $product->is_active ? 'Yes' : 'No',
                    $product->description ?? '',
                    $product->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk Import Products from CSV
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        // Check BOM
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $header = fgetcsv($handle);
        if (!$header) {
            return back()->with('error', 'CSV file is empty or malformed.');
        }

        // Normalize headers
        $headerMap = array_map(function ($col) {
            return strtolower(trim(str_replace([' ', '_', '-'], '', $col)));
        }, $header);

        $imported = 0;
        $highestOrder = Product::max('sort_order') ?? 0;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                if (empty(array_filter($row))) {
                    continue;
                }

                $data = array_combine($headerMap, $row);

                $name = $data['productname'] ?? $data['name'] ?? null;
                $price = $data['price'] ?? 0;
                $sku = $data['sku'] ?? null;
                $barcode = $data['barcode'] ?? null;
                $stock = $data['stockquantity'] ?? $data['stock'] ?? 0;
                $desc = $data['description'] ?? null;
                $discount = $data['discountprice'] ?? null;
                $activeStr = strtolower($data['active'] ?? 'yes');
                $isActive = !in_array($activeStr, ['no', '0', 'false', 'inactive']);

                if (!$name) {
                    continue;
                }

                $highestOrder++;

                Product::updateOrCreate(
                    ['sku' => $sku ?: ('PRD-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 4)) . '-' . rand(1000, 9999))],
                    [
                        'name' => $name,
                        'barcode' => $barcode ?: ('890' . rand(100000000, 999999999)),
                        'price' => (float) $price,
                        'discount_price' => $discount ? (float) $discount : null,
                        'stock_quantity' => (int) $stock,
                        'description' => $desc,
                        'sort_order' => $highestOrder,
                        'is_active' => $isActive,
                    ]
                );
                $imported++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'CSV Import failed: ' . $e->getMessage());
        } finally {
            fclose($handle);
        }

        return redirect()->route('products.index')->with('success', "Successfully imported {$imported} products.");
    }

    /**
     * Download Printable PDF Catalog
     */
    public function downloadPdfCatalog()
    {
        $products = Product::orderBy('sort_order')->orderBy('id')->get();
        $generatedAt = now()->format('d M Y, h:i A');

        return view('products.catalog_pdf', compact('products', 'generatedAt'));
    }
}
