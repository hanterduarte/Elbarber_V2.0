<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'barbershop_id',
        'barber_id',
        'client_id',
        'payment_method_id',
        'total',
        'discount',
        'status',
        'notes'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'discount' => 'decimal:2'
    ];

    public function barbershop()
    {
        return $this->belongsTo(Barbershop::class);
    }

    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function services()
    {
        return $this->morphedByMany(Service::class, 'itemable', 'sale_items');
    }

    public function products()
    {
        return $this->morphedByMany(Product::class, 'itemable', 'sale_items');
    }
} 