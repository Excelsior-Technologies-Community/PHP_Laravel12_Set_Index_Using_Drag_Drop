<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'barcode',
        'description',
        'thumbnail',
        'gallery_images',
        'price',
        'discount_price',
        'discount_end_date',
        'stock_quantity',
        'low_stock_threshold',
        'variants',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'discount_end_date' => 'date',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'is_active' => 'boolean',
        'gallery_images' => 'array',
        'variants' => 'array',
    ];

    protected $appends = [
        'effective_price',
        'has_discount',
        'stock_status',
        'thumbnail_url',
    ];

    /**
     * Scope for ordering by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Scope for active products
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if product has active discount
     */
    public function getHasDiscountAttribute(): bool
    {
        if ($this->discount_price !== null && $this->discount_price > 0 && $this->discount_price < $this->price) {
            if ($this->discount_end_date) {
                return $this->discount_end_date->isFuture() || $this->discount_end_date->isToday();
            }
            return true;
        }
        return false;
    }

    /**
     * Get effective payable price
     */
    public function getEffectivePriceAttribute(): float
    {
        return $this->has_discount ? (float) $this->discount_price : (float) $this->price;
    }

    /**
     * Stock Status badge helper
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->stock_quantity <= 0) {
            return 'out_of_stock';
        }
        if ($this->stock_quantity <= ($this->low_stock_threshold ?? 5)) {
            return 'low_stock';
        }
        return 'in_stock';
    }

    /**
     * Full URL for thumbnail image
     */
    public function getThumbnailUrlAttribute(): string
    {
        if ($this->thumbnail && file_exists(public_path('storage/' . $this->thumbnail))) {
            return asset('storage/' . $this->thumbnail);
        }
        // Fallback placeholder with SVG data URI
        $nameInitial = strtoupper(substr($this->name ?? 'P', 0, 1));
        return "https://ui-avatars.com/api/?name=" . urlencode($nameInitial) . "&background=0D6EFD&color=fff&size=120";
    }

    /**
     * Boot helper to auto-generate SKU and Barcode if empty
     */
    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->sku)) {
                $product->sku = 'PRD-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $product->name ?? 'ITEM'), 0, 4)) . '-' . rand(1000, 9999);
            }
            if (empty($product->barcode)) {
                $product->barcode = '890' . rand(100000000, 999999999);
            }
        });
    }
}