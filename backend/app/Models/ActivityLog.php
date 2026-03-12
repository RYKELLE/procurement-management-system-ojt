<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'description',
        'subject_type',
        'subject_id',
    ];

    // Who performed the action
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
