<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPermissionRevocation extends Model
{
    protected $fillable = [
        'user_id',
        'permission_id',
    ];
}

