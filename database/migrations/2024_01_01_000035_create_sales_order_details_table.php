<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesOrderDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('sales_order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained('sales_orders')->onDelete('cascade');
            $table->foreignId('part_id')->nullable()->constrained('spareparts')->nullOnDelete();
            $table->integer('qty');
            $table->decimal('sell_price', 12, 2)->default(0);
            $table->decimal('cost_price', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sales_order_details');
    }
}
