<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceOrderPhotosTable extends Migration
{
    public function up()
    {
        Schema::create('service_order_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('service_orders')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->string('photo_url');
            $table->string('caption')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_order_photos');
    }
}
