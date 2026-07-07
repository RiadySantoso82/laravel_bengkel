<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('part_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('service_orders')->onDelete('cascade');
            $table->foreignId('mechanic_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('requested');
            $table->dateTime('requested_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('part_requests');
    }
}
