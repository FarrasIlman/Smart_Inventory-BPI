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
        Schema::create('productions', function (Blueprint $table) {
        $table->id('id_production');
        $table->foreignId('id_order')->constrained('orders','id_order')->cascadeOnDelete();
        $table->date('tanggal_mulai')->nullable();
        $table->date('tanggal_selesai')->nullable();
        $table->integer('jumlah_produksi')->nullable();
        $table->enum('status_produksi',['belum','proses','selesai'])->default('belum');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};
