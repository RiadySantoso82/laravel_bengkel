<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQtyReturnedToPartRequestDetailsTable extends Migration
{
    public function up()
    {
        Schema::table('part_request_details', function (Blueprint $table) {
            $table->integer('qty_returned')->default(0);
        });
    }

    public function down()
    {
        Schema::table('part_request_details', function (Blueprint $table) {
            $table->dropColumn('qty_returned');
        });
    }
}
