<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartReturnsTable extends Migration
{
    public function up()
    {
        Schema::create('part_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_request_detail_id')->constrained('part_request_details')->onDelete('cascade');
            $table->foreignId('mechanic_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('qty_returned');
            $table->string('reason');
            $table->dateTime('returned_at')->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('part_returns');
    }
}
