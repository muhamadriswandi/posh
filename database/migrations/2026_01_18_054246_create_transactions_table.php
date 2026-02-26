<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['debit', 'credit']);
            $table->foreignId('receipt_type_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('payment_channel_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
