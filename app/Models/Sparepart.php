<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sparepart extends Model
{
    protected $fillable = ['code', 'name', 'category_id', 'unit_id', 'buy_price', 'sell_price', 'min_stock'];
    protected $table = 'spareparts';
    protected $appends = ['stock_qty'];

    public function category()
    {
        return $this->belongsTo(SparepartCategory::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class, 'part_id');
    }

    public function getStockQtyAttribute()
    {
        $in = $this->movements()->where('movement_type', 'in')->sum('qty');
        $out = $this->movements()->where('movement_type', 'out')->sum('qty');
        return $in - $out;
    }
}
