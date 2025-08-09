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
        Schema::create('drugs', function (Blueprint $table) {
            $table->id();
            $table->string('barcode')->unique();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->decimal('price');
            $table->decimal('purchase_price'); //harga beli
            $table->decimal('modal');
            $table->unsignedInteger('stock')->default(0);
            $table->date('expired_at')->nullable(); // Tanggal kadaluarsa
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drugs');
    }
};
