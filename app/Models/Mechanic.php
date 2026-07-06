<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mechanic extends Model
{
    protected $fillable = ['name', 'specialization', 'phone', 'status', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
