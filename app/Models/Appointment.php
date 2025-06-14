<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'barber_id',
        'start_time',
        'end_time',
        'status',
        'notes',
        'total',
        'duration'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'total' => 'decimal:2'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function barber()
    {
        return $this->belongsTo(User::class, 'barber_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'appointment_service')
            ->withPivot('price')
            ->withTimestamps();
    }

    public function service()
    {
        return $this->belongsToMany(Service::class, 'appointment_service')
            ->withPivot('price')
            ->withTimestamps()
            ->first();
    }

    public function sale()
    {
        return $this->hasOne(Sale::class);
    }
} 