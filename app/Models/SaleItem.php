<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'itemable_id',
        'itemable_type',
        'quantity',
        'price',
        'discount'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount' => 'decimal:2'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function itemable()
    {
        return $this->morphTo();
    }
} 