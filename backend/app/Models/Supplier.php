<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
  use HasFactory;

  protected $appends = ['supplier_id'];

  protected $fillable = [
    'supplier_name',
    'contact',
    'email'
  ];

  public function getSupplierIdAttribute(): ?string
  {
    if (!$this->id) {
      return null;
    }

    return 'SUP-' . str_pad((string) $this->id, 3, '0', STR_PAD_LEFT);
  }

  public function items()
  {
    return $this->hasMany(Item::class);
  }
}
