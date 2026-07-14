<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
    protected $fillable = ['cash_category_id', 'user_id', 'type', 'amount', 'description', 'proof_url', 'transaction_date'];
    protected $table = 'cash_transactions';
    protected $casts = ['transaction_date' => 'date'];

    public function category()
    {
        return $this->belongsTo(CashCategory::class, 'cash_category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
