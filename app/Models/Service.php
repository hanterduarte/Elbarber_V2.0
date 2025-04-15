<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'duration',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration' => 'integer',
        'status' => 'boolean'
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function saleItems()
    {
        return $this->morphMany(SaleItem::class, 'itemable');
    }
} 