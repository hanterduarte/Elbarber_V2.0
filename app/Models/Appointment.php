<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'barber_id',
        'client_id',
        'date',
        'time',
        'status',
        'notes'
    ];

    protected $dates = [
        'date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'date' => 'date',
        'status' => 'string'
    ];

    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'appointment_service');
    }
} 