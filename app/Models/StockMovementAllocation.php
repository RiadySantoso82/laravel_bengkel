<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovementAllocation extends Model
{
    protected $fillable = ['movement_id', 'batch_id', 'qty_taken', 'cost_price'];
    protected $table = 'stock_movement_allocations';

    public function movement() { return $this->belongsTo(StockMovement::class, 'movement_id'); }
    public function batch() { return $this->belongsTo(StockBatch::class, 'batch_id'); }
}
