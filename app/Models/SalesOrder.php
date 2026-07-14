<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    protected $fillable = ['customer_id', 'user_id', 'total_amount', 'discount', 'payment_status'];
    protected $table = 'sales_orders';

    public function customer() { return $this->belongsTo(Customer::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function details() { return $this->hasMany(SalesOrderDetail::class, 'sales_order_id'); }
    public function payments() { return $this->hasMany(PaymentTransaction::class, 'reference_id')->where('reference_type', 'sales_order'); }
}
