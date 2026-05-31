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
        Schema::create('stock_movements', function (Blueprint $table) {
        $table->id('id_movement');
        $table->foreignId('id_bahanbaku')->constrained('raw_materials','id_bahanbaku');
        $table->enum('tipe_transaksi',['masuk','keluar','penyesuaian']);
        $table->decimal('jumlah',10,2);
        $table->date('tanggal');
        $table->text('keterangan')->nullable();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
