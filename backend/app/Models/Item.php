<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
  use HasFactory;

  protected $fillable = [
    'supplier_id',
    'item_name',
    'item_price',
    'item_description'
  ];

  public function requestItems() //an item can appear in many requests
  {
    return $this->hasMany(RequestItem::class);
  }

  public function supplier()
  {
    return $this->belongsTo(Supplier::class);
  }


}