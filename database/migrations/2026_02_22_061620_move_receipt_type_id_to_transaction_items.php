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
        Schema::table('transaction_items', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->foreignId('receipt_type_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::table('transactions', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->dropForeign(['receipt_type_id']);
            $table->dropColumn('receipt_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->foreignId('receipt_type_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::table('transaction_items', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->dropForeign(['receipt_type_id']);
            $table->dropColumn('receipt_type_id');
        });
    }
};
