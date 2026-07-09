<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockBatchesTable extends Migration
{
    public function up()
    {
        Schema::create('stock_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_id')->constrained('spareparts')->onDelete('cascade');
            $table->integer('qty_in');
            $table->integer('qty_remaining');
            $table->decimal('buy_price', 12, 2)->default(0);
            $table->date('received_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_batches');
    }
}
