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
        Schema::create('products', function (Blueprint $table) {
        $table->id('id_product');
        $table->foreignId('id_categories')->constrained('categories','id_categories')->cascadeOnDelete();;
        $table->string('nama_produk');
        $table->string('kategori_produk')->nullable();
        $table->text('deskripsi')->nullable();
        $table->string('gambar_produk')->nullable();
        $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
