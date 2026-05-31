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
        Schema::create('bom', function (Blueprint $table) {
        $table->id('id_bom');
        $table->foreignId('id_product')->constrained('products','id_product')->cascadeOnDelete();
        $table->foreignId('id_bahanbaku')->constrained('raw_materials','id_bahanbaku')->cascadeOnDelete();
        $table->decimal('jumlah_kebutuhan',10,2);
        $table->decimal('persentase_waste',5,2)->default(0);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_bom');
    }
};
