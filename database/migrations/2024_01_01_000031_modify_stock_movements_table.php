<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyStockMovementsTable extends Migration
{
    public function up()
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->renameColumn('type', 'movement_type');
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->date('transaction_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['source_type', 'source_id', 'transaction_date', 'created_by']);
            $table->renameColumn('movement_type', 'type');
        });
    }
}
