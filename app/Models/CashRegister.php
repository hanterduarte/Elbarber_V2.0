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
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'total_withdrawals' => 'decimal:2',
        'total_deposits' => 'decimal:2',
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
} 