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
        Schema::create('purchases', function (Blueprint $table) {
        $table->id('id_purchase');
        $table->foreignId('id_supplier')->constrained('suppliers','id_supplier');
        $table->date('tanggal_pembelian');
        $table->enum('status_pembelian',['dipesan','diterima']);
        $table->timestamps();
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
