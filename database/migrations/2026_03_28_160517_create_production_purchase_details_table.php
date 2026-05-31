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
        Schema::create('purchase_details', function (Blueprint $table) {
        $table->id('id_detail');
        $table->foreignId('id_purchase')->constrained('purchases','id_purchase')->cascadeOnDelete();
        $table->foreignId('id_bahanbaku')->constrained('raw_materials','id_bahanbaku');
        $table->decimal('jumlah',10,2);
        $table->decimal('harga',12,2)->nullable();
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_details');
    }
};
