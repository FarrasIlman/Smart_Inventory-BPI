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
        Schema::create('orders', function (Blueprint $table) {
        $table->id('id_order');
        $table->string('nama_pelanggan');
        $table->foreignId('id_product')->constrained('products','id_product');
        $table->integer('jumlah_pesanan');
        $table->date('tanggal_pesan');
        $table->date('deadline');
        $table->enum('status_order',['menunggu bahan','siap produksi','produksi','selesai']);
        $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
