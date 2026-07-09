<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = ['order_id', 'total_amount', 'discount', 'payment_status'];
    protected $table = 'invoices';

    public function order()
    {
        return $this->belongsTo(ServiceOrder::class, 'order_id');
    }

    public function payments()
    {
        return $this->morphMany(PaymentTransaction::class, 'reference', 'reference_type', 'reference_id');
    }
}
