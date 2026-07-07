<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartRequestDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('part_request_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_request_id')->constrained('part_requests')->onDelete('cascade');
            $table->foreignId('part_id')->nullable()->constrained('spareparts')->nullOnDelete();
            $table->integer('qty_requested')->default(1);
            $table->integer('qty_fulfilled')->default(0);
            $table->string('status')->default('pending');
            $table->foreignId('fulfilled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('fulfilled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('part_request_details');
    }
}
