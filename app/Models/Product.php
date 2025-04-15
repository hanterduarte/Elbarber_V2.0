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
        'stock',
        'created_at',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'status' => 'boolean'
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