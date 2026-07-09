<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderDetail extends Model
{
    protected $fillable = ['sales_order_id', 'part_id', 'qty', 'sell_price', 'cost_price'];
    protected $table = 'sales_order_details';

    public function salesOrder() { return $this->belongsTo(SalesOrder::class, 'sales_order_id'); }
    public function sparepart() { return $this->belongsTo(Sparepart::class, 'part_id'); }
}
