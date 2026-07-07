<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceOrderChecklistTable extends Migration
{
    public function up()
    {
        Schema::create('service_order_checklist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('service_orders')->onDelete('cascade');
            $table->foreignId('checklist_item_id')->constrained('checklist_items')->onDelete('cascade');
            $table->foreignId('mechanic_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_checked')->default(false);
            $table->text('notes')->nullable();
            $table->dateTime('checked_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_order_checklist');
    }
}
