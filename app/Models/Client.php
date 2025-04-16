<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'address',
        'phone',
        'birth_date',
        'is_active',
        'city',
        'state',
        'zip_code',
        'notes'
    ];

    protected $dates = [
        'birth_date',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
} 