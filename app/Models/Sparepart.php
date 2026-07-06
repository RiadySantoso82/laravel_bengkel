<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sparepart extends Model
{
    protected $fillable = ['code', 'name', 'category_id', 'unit_id', 'buy_price', 'sell_price', 'stock_qty', 'min_stock'];
    protected $table = 'spareparts';

    public function category()
    {
        return $this->belongsTo(SparepartCategory::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
