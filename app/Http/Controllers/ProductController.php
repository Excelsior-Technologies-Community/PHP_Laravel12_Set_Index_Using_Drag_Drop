<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display products with Search + Pagination + Dashboard Stats
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $query = Product::query();

        // Search
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('price', 'LIKE', "%{$search}%");
            });
        }

        // Dashboard Statistics
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', 1)->count();
        $inactiveProducts = Product::where('is_active', 0)->count();
        $todayProducts = Product::whereDate('created_at', today())->count();

        // Product List
        $products = $query
            ->orderBy('sort_order')
            ->paginate(5)
            ->withQueryString();

        return view('products.index', compact(
            'products',
            'search',
            'totalProducts',
            'activeProducts',
            'inactiveProducts',
            'todayProducts'
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
     * Store Product
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        // Next Sort Order
        $highestOrder = Product::max('sort_order');

        $validated['sort_order'] = $highestOrder
            ? $highestOrder + 1
            : 1;

        Product::create($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully.');
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
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $product->update($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Delete Product
     */
    public function destroy(Product $product)
    {
        $product->delete();

        $this->reorderProducts();

        return redirect()
            ->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Drag & Drop Update Order
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array'
        ]);

        foreach ($request->items as $index => $id) {

            Product::where('id', $id)->update([
                'sort_order' => $index + 1
            ]);
        }

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Reorder Products After Delete
     */
    private function reorderProducts()
    {
        $products = Product::orderBy('sort_order')->get();

        foreach ($products as $index => $product) {

            $product->update([
                'sort_order' => $index + 1
            ]);
        }
    }

    /**
     * Bulk Product Actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'action' => 'required|string'
        ]);

        $products = Product::whereIn('id', $request->product_ids);

        switch ($request->action) {

            case 'delete':

                $products->delete();

                $this->reorderProducts();

                $message = "Products deleted successfully.";

                break;


            case 'activate':

                $products->update([
                    'is_active' => true
                ]);

                $message = "Products activated successfully.";

                break;


            case 'deactivate':

                $products->update([
                    'is_active' => false
                ]);

                $message = "Products deactivated successfully.";

                break;


            default:

                return back()->with(
                    'error',
                    'Invalid action'
                );
        }

        return redirect()
            ->route('products.index')
            ->with('success', $message);
    }


    /**
     * Reset Product Sorting
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
            'success' => true
        ]);
    }
}
