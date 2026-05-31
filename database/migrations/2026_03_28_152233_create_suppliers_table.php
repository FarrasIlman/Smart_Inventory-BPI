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
        
        Schema::create('suppliers', function (Blueprint $table) {
        $table->id('id_supplier');
        $table->string('kode_supplier')->nullable();
        $table->string('nama_supplier');
        $table->string('nama_pic')->nullable();
        $table->string('no_telepon')->nullable();
        $table->string('email')->nullable();
        $table->text('alamat')->nullable();
        $table->string('kota')->nullable();
        $table->integer('lead_time')->nullable();
        $table->integer('minimum_order')->nullable();
        $table->enum('status_supplier',['aktif','tidak aktif'])->default('aktif');
        $table->string('keterangan')->nullable();
        $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
