<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovePaymentMethodFromOrdersAndCreatePaymentTransactions extends Migration
{
    public function up()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropForeign(['payment_method_id']);
            $table->dropColumn('payment_method_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'payment_method_id')) {
                $table->dropForeign(['payment_method_id']);
                $table->dropColumn('payment_method_id');
            }
        });

        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference_type');
            $table->unsignedBigInteger('reference_id');
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('amount_received', 12, 2)->nullable();
            $table->decimal('change_amount', 12, 2)->nullable();
            $table->string('card_type')->nullable();
            $table->string('card_last4')->nullable();
            $table->string('issuing_bank')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('proof_url')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();

            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->nullOnDelete();
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'payment_method_id')) {
                $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->nullOnDelete();
            }
        });

        Schema::dropIfExists('payment_transactions');
    }
}
