<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = ['part_id', 'type', 'qty', 'reference_id'];
    protected $table = 'stock_movements';

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class, 'part_id');
    }
}
