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
        'cost',
        'stock',
        'min_stock',
        'is_active'
    ];

    protected $casts = [
        'price' => 'float',
        'cost' => 'float',
        'is_active' => 'boolean'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function saleItems()
    {
        return $this->morphMany(SaleItem::class, 'itemable');
    }
} 