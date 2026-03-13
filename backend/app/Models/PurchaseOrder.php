<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseOrder extends Model
{
  use HasFactory;

  protected $fillable = [
    'request_id',
    'supplier_id',
    'created_by',
    'order_total_amount',
    'status',
  ];

  protected $casts = [
    'order_total_amount' => 'decimal:2',
  ];

  public function purchaseRequest()
  {
    return $this->belongsTo(PurchaseRequest::class, 'request_id');
  }

  public function supplier()
  {
    return $this->belongsTo(Supplier::class, );
  }

  public function createdBy()
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  public function invoice()
  {
    return $this->hasOne(Invoice::class, 'order_id');
  }
}