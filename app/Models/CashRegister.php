<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashRegister extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cash_registers';

    protected $fillable = [
        'user_id',
        'barber_id',
        'opening_balance',
        'closing_balance',
        'total_sales',
        'total_withdrawals',
        'total_deposits',
        'status',
        'opened_at',
        'closed_at',
        'notes'
    ];

    protected $casts = [
        'opening_balance' => 'float',
        'closing_balance' => 'float',
        'total_sales' => 'float',
        'total_withdrawals' => 'float',
        'total_deposits' => 'float',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime'
    ];

    protected $dates = [
        'opened_at',
        'closed_at',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    public function movements()
    {
        return $this->hasMany(CashRegisterMovement::class);
    }

    public function getCurrentBalance()
    {
        return $this->opening_balance + $this->total_sales + $this->total_deposits - $this->total_withdrawals;
    }
} 