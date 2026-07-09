<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'reference_type', 'reference_id', 'payment_method_id', 'created_by',
        'amount', 'amount_received', 'change_amount',
        'card_type', 'card_last4', 'issuing_bank', 'reference_number', 'proof_url',
        'paid_at',
    ];
    protected $table = 'payment_transactions';
    protected $casts = ['paid_at' => 'datetime'];

    public function paymentMethod() { return $this->belongsTo(PaymentMethod::class, 'payment_method_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function reference() { return $this->morphTo('reference', 'reference_type', 'reference_id'); }
}
