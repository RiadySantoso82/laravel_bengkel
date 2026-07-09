<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockBatch extends Model
{
    protected $fillable = ['part_id', 'qty_in', 'qty_remaining', 'buy_price', 'received_date'];
    protected $table = 'stock_batches';
    protected $casts = ['received_date' => 'date'];

    public function sparepart() { return $this->belongsTo(Sparepart::class, 'part_id'); }
    public function allocations() { return $this->hasMany(StockMovementAllocation::class, 'batch_id'); }
}
