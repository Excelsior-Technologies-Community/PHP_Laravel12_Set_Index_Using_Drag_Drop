<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku')->nullable()->after('name');
            $table->string('barcode')->nullable()->after('sku');
            $table->string('thumbnail')->nullable()->after('description');
            $table->json('gallery_images')->nullable()->after('thumbnail');
            $table->decimal('discount_price', 10, 2)->nullable()->after('price');
            $table->date('discount_end_date')->nullable()->after('discount_price');
            $table->integer('stock_quantity')->default(0)->after('discount_end_date');
            $table->integer('low_stock_threshold')->default(5)->after('stock_quantity');
            $table->json('variants')->nullable()->after('low_stock_threshold');
            $table->softDeletes()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn([
                'sku',
                'barcode',
                'thumbnail',
                'gallery_images',
                'discount_price',
                'discount_end_date',
                'stock_quantity',
                'low_stock_threshold',
                'variants',
            ]);
        });
    }
};
