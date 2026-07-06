<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServiceCategoryIdToServiceTypesTable extends Migration
{
    public function up()
    {
        Schema::table('service_types', function (Blueprint $table) {
            $table->foreignId('service_category_id')->nullable()->constrained('service_categories')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('service_types', function (Blueprint $table) {
            $table->dropForeign(['service_category_id']);
            $table->dropColumn('service_category_id');
        });
    }
}
