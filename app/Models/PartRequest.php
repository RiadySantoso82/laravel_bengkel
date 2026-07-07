<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartRequest extends Model
{
    protected $fillable = ['order_id', 'mechanic_id', 'status', 'requested_at'];
    protected $table = 'part_requests';

    public function details()
    {
        return $this->hasMany(PartRequestDetail::class, 'part_request_id');
    }

    public function order()
    {
        return $this->belongsTo(ServiceOrder::class, 'order_id');
    }

    public function mechanic()
    {
        return $this->belongsTo(Mechanic::class);
    }
}
