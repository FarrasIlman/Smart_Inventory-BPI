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
        Schema::create('production_materials', function (Blueprint $table) {
        $table->id('id_usage');
        $table->foreignId('id_production')->constrained('productions','id_production')->cascadeOnDelete();
        $table->foreignId('id_bahanbaku')->constrained('raw_materials','id_bahanbaku');
        $table->decimal('jumlah_estimasi',10,2);
        $table->decimal('jumlah_realisasi',10,2)->nullable();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_materials');
    }
};
