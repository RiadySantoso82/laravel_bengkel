<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartReturn extends Model
{
    protected $fillable = ['part_request_detail_id', 'mechanic_id', 'confirmed_by', 'qty_returned', 'reason', 'returned_at', 'confirmed_at'];
    protected $table = 'part_returns';

    public function detail()
    {
        return $this->belongsTo(PartRequestDetail::class, 'part_request_detail_id');
    }

    public function mechanic()
    {
        return $this->belongsTo(Mechanic::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}
