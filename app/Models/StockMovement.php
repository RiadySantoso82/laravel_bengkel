<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = ['part_id', 'movement_type', 'source_type', 'source_id', 'qty', 'transaction_date', 'created_by'];
    protected $table = 'stock_movements';
    protected $casts = ['transaction_date' => 'date'];

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class, 'part_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
