<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // Scope for ordering by sort_order
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Scope for active products
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}