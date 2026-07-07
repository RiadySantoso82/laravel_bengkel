<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceOrderPhoto extends Model
{
    protected $fillable = ['order_id', 'user_id', 'type', 'photo_url', 'caption'];
    protected $table = 'service_order_photos';
}
