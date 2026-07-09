<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveStockQtyFromSparepartsTable extends Migration
{
    public function up()
    {
        Schema::table('spareparts', function (Blueprint $table) {
            $table->dropColumn('stock_qty');
        });
    }

    public function down()
    {
        Schema::table('spareparts', function (Blueprint $table) {
            $table->integer('stock_qty')->default(0);
        });
    }
}
