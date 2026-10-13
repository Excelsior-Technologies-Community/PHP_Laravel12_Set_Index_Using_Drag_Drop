<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductModernFeaturesTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test Catalog listing and stats
     */
    public function test_catalog_index_and_stats()
    {
        $response = $this->get(route('products.index'));
        $response->assertStatus(200);
        $response->assertSee('Product Sorter & Manager');
        $response->assertSee('Live Instant Search');
    }

    /**
     * Test AJAX live search and filter
     */
    public function test_ajax_live_search()
    {
        Product::create([
            'name' => 'Quantum Test Widget',
            'sku' => 'PRD-QTW-999',
            'price' => 149.99,
            'stock_quantity' => 20,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this->getJson(route('products.index', ['search' => 'Quantum']));
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'html', 'pagination', 'total']);
        $this->assertTrue(str_contains($response->json('html'), 'Quantum Test Widget'));
    }

    /**
     * Test Inline Quick Update
     */
    public function test_quick_update()
    {
        $product = Product::create([
            'name' => 'Initial Name',
            'price' => 50.00,
            'stock_quantity' => 10,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // Update Name
        $response = $this->postJson(route('products.quick-update', $product->id), [
            'field' => 'name',
            'value' => 'Updated Quick Name',
        ]);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertEquals('Updated Quick Name', $product->fresh()->name);

        // Update Price
        $response2 = $this->postJson(route('products.quick-update', $product->id), [
            'field' => 'price',
            'value' => 89.99,
        ]);
        $response2->assertStatus(200);
        $this->assertEquals(89.99, (float) $product->fresh()->price);

        // Update Status
        $response3 = $this->postJson(route('products.quick-update', $product->id), [
            'field' => 'is_active',
            'value' => 0,
        ]);
        $response3->assertStatus(200);
        $this->assertFalse((bool) $product->fresh()->is_active);
    }

    /**
     * Test Soft Deletes, Recycle Bin, and Restore
     */
    public function test_soft_deletes_and_recycle_bin()
    {
        $product = Product::create([
            'name' => 'Deletable Item',
            'price' => 25.00,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // Soft Delete
        $delResponse = $this->deleteJson(route('products.destroy', $product->id));
        $delResponse->assertStatus(200);
        $this->assertSoftDeleted('products', ['id' => $product->id]);

        // Verify in Recycle Bin
        $trashResponse = $this->get(route('products.index', ['trash' => 1]));
        $trashResponse->assertStatus(200);
        $trashResponse->assertSee('Recycle Bin');
        $trashResponse->assertSee('Deletable Item');

        // Restore
        $restoreResponse = $this->postJson(route('products.restore', $product->id));
        $restoreResponse->assertStatus(200);
        $this->assertNotSoftDeleted('products', ['id' => $product->id]);
    }

    /**
     * Test Drag & Drop Order Update
     */
    public function test_drag_drop_update_order()
    {
        $p1 = Product::create(['name' => 'Item A', 'price' => 10, 'sort_order' => 1]);
        $p2 = Product::create(['name' => 'Item B', 'price' => 20, 'sort_order' => 2]);

        $response = $this->postJson(route('products.update-order'), [
            'items' => [$p2->id, $p1->id],
        ]);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals(1, $p2->fresh()->sort_order);
        $this->assertEquals(2, $p1->fresh()->sort_order);
    }

    /**
     * Test CSV Export stream
     */
    public function test_csv_export()
    {
        $response = $this->get(route('products.export-csv'));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    /**
     * Test PDF Catalog view
     */
    public function test_pdf_catalog_view()
    {
        $response = $this->get(route('products.catalog-pdf'));
        $response->assertStatus(200);
        $response->assertSee('Product Catalog');
    }
}
