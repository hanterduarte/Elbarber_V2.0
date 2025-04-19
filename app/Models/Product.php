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
        'description',
        'price',
        'stock',
        'min_stock',
        'sku',
        'barcode',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'min_stock' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function saleItems()
    {
        return $this->morphMany(SaleItem::class, 'itemable');
    }

    public function sales()
    {
        return $this->belongsToMany(Sale::class, 'product_sale')
            ->withPivot('quantity', 'price', 'discount')
            ->withTimestamps();
    }

    public function barbershop()
    {
        return $this->belongsTo(Barbershop::class);
    }

    public function getStockStatusAttribute()
    {
        if ($this->stock <= 0) {
            return 'out_of_stock';
        } elseif ($this->stock <= $this->min_stock) {
            return 'low_stock';
        }
        return 'in_stock';
    }
} 