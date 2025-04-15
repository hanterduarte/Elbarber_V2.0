<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barbershop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'created_at'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function barbers()
    {
        return $this->hasMany(Barber::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function cashRegisters()
    {
        return $this->hasMany(CashRegister::class);
    }
} 