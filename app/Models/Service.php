<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
        'duration',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration' => 'integer',
        'is_active' => 'boolean'
    ];

    public function appointments()
    {
        return $this->belongsToMany(Appointment::class, 'appointment_service')
            ->withPivot('price', 'duration')
            ->withTimestamps();
    }

    public function sales()
    {
        return $this->belongsToMany(Sale::class, 'service_sale')
            ->withPivot('quantity', 'price', 'discount')
            ->withTimestamps();
    }

    public function barbers()
    {
        return $this->belongsToMany(Barber::class, 'barber_service')
            ->withTimestamps();
    }

    public function saleItems()
    {
        return $this->morphMany(SaleItem::class, 'itemable');
    }
} 