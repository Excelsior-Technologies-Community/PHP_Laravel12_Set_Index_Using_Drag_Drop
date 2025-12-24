<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Display all products in order
    public function index()
    {
        $products = Product::ordered()->get();
        return view('products.index', compact('products'));
    }

    // Show create form
    public function create()
    {
        return view('products.create');
    }

    // Store new product
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        // Get the highest sort_order and add 1
        $highestOrder = Product::max('sort_order');
        $validated['sort_order'] = $highestOrder ? $highestOrder + 1 : 1;

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    // Show edit form
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    // Update product
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    // Delete product
    public function destroy(Product $product)
    {
        $product->delete();
        
        // Reorder remaining products
        $this->reorderProducts();
        
        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    // Update sort order via AJAX
    public function updateOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
        ]);

        foreach ($request->items as $index => $itemId) {
            Product::where('id', $itemId)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    // Reorder products after deletion
    private function reorderProducts()
    {
        $products = Product::ordered()->get();
        
        foreach ($products as $index => $product) {
            $product->update(['sort_order' => $index + 1]);
        }
    }
}