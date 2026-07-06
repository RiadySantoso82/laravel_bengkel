<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceOrder extends Model
{
    protected $fillable = ['vehicle_id', 'customer_id', 'mechanic_id', 'user_id', 'complaint', 'status', 'estimated_finish', 'actual_finish', 'vehicle_plate_manual', 'vehicle_info_manual'];
    protected $table = 'service_orders';

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function mechanic()
    {
        return $this->belongsTo(Mechanic::class);
    }

    public function details()
    {
        return $this->hasMany(ServiceOrderDetail::class, 'order_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'order_id');
    }
}
