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
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('total', 15, 2)->default(0)->change();
            $table->enum('payment_method', ['tunai', 'transfer', 'qris'])->default('tunai');
            $table->enum('payment_status', ['dibayar', 'menunggu_pembayaran', 'dibatalkan']);
            $table->decimal('cash', 15, 2)->default(0);
            $table->decimal('change', 15, 2)->default(0);
            $table->decimal('point_usage')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('payment_method');
            $table->dropColumn('payment_status');
            $table->dropColumn('cash');
            $table->dropColumn('change');
            $table->dropColumn('point_usage');
        });
    }
};
