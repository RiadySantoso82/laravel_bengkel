<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    protected $fillable = ['name', 'base_price', 'estimated_duration', 'service_category_id'];
    protected $table = 'service_types';
}
