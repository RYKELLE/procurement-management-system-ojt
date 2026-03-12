<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'processed_by',
        'status',
        'amount',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount'   => 'decimal:2',
    ];

    // The purchase order this invoice belongs to
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'order_id');
    }

    // Who processed/created this invoice
    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Payment recorded for this invoice
    public function payment()
    {
        return $this->hasOne(Payment::class, 'invoice_id');
    }
}
