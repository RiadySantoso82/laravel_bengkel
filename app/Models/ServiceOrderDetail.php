<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceOrderDetail extends Model
{
    protected $fillable = ['order_id', 'type', 'item_id', 'qty', 'price'];
    protected $table = 'service_order_details';

    public function order()
    {
        return $this->belongsTo(ServiceOrder::class, 'order_id');
    }
}
