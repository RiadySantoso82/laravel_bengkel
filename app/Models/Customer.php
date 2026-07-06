<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name', 'phone', 'email', 'address', 'is_walk_in'];
    protected $casts = ['is_walk_in' => 'boolean'];
}
