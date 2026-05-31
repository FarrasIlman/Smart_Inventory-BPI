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
        Schema::create('raw_materials', function (Blueprint $table) {
        $table->id('id_bahanbaku');
        $table->string('nama_bahanbaku');
        $table->string('satuan');
        $table->decimal('stok',10,2)->default(0);
        $table->decimal('stok_minimum',10,2)->default(0);
        $table->string('gambar_bahan')->nullable();
        $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_materials');
    }
};
