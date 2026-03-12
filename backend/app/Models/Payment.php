<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'paid_by',
        'amount_paid',
        'payment_date',
        'payment_method',
        'reference_number',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount_paid'  => 'decimal:2',
    ];

    // The invoice this payment is for
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    // Who made the payment
    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}
