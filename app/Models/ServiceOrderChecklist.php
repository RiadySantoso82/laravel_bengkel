<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceOrderChecklist extends Model
{
    protected $fillable = ['order_id', 'checklist_item_id', 'mechanic_id', 'is_checked', 'notes', 'checked_at'];
    protected $casts = ['is_checked' => 'boolean', 'checked_at' => 'datetime'];
    protected $table = 'service_order_checklist';
}
