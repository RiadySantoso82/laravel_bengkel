<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartRequestDetail extends Model
{
    protected $fillable = ['part_request_id', 'part_id', 'qty_requested', 'qty_fulfilled', 'qty_returned', 'status', 'fulfilled_by', 'fulfilled_at'];
    protected $table = 'part_request_details';

    public function partRequest()
    {
        return $this->belongsTo(PartRequest::class, 'part_request_id');
    }

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class, 'part_id');
    }

    public function fulfilledBy()
    {
        return $this->belongsTo(User::class, 'fulfilled_by');
    }
}
