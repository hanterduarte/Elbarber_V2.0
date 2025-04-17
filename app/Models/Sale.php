<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'subtotal',
        'discount_percentage',
        'discount_amount',
        'final_total',
        'notes',
        'status'
    ];

    protected $casts = [
        'subtotal' => 'float',
        'discount_percentage' => 'float',
        'discount_amount' => 'float',
        'final_total' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function payments()
    {
        return $this->hasMany(SalePayment::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot(['quantity', 'price', 'total'])
            ->withTimestamps();
    }

    public function services()
    {
        return $this->belongsToMany(Service::class)
            ->withPivot(['quantity', 'price', 'total'])
            ->withTimestamps();
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
} 