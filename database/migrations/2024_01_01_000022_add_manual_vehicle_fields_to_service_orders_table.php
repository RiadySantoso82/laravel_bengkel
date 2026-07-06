<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddManualVehicleFieldsToServiceOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
        });

        Schema::table('service_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('vehicle_id')->nullable()->change();
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->nullOnDelete();
            $table->string('vehicle_plate_manual')->nullable();
            $table->string('vehicle_info_manual')->nullable();
        });
    }

    public function down()
    {
        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropColumn(['vehicle_plate_manual', 'vehicle_info_manual']);
            $table->dropForeign(['vehicle_id']);
        });

        Schema::table('service_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('vehicle_id')->nullable(false)->change();
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
        });
    }
}
