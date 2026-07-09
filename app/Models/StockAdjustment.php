<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    protected $fillable = ['part_id', 'user_id', 'qty', 'reason', 'notes', 'transaction_date'];
    protected $table = 'stock_adjustments';
    protected $casts = ['transaction_date' => 'date'];

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class, 'part_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
