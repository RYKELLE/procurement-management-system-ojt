<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseRequest extends Model
{
  use HasFactory; //lets the model generate data

  protected $fillable = [ //columns that are fillable
    'requested_by',
    'approved_by',
    'request_status',
    'reason',
    'date_approved'
  ];

  public function requester() //maps the column to the user model
  {
    return $this->belongsTo(User::class, 'requested_by');
  }

  public function approver()
  {
    return $this->belongsTo(User::class, 'approved_by');
  }

  public function items() //a request can have many items
  {
    return $this->hasMany(RequestItem::class);
  }
}