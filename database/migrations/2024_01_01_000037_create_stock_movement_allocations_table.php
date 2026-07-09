<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockMovementAllocationsTable extends Migration
{
    public function up()
    {
        Schema::create('stock_movement_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movement_id')->constrained('stock_movements')->onDelete('cascade');
            $table->foreignId('batch_id')->constrained('stock_batches')->onDelete('cascade');
            $table->integer('qty_taken');
            $table->decimal('cost_price', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_movement_allocations');
    }
}
