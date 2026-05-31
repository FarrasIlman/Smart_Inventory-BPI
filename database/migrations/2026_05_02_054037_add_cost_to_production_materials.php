<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('production_materials', function (Blueprint $table) {
            $table->decimal('harga', 12, 2)->nullable();
            $table->decimal('subtotal', 14, 2)->nullable();
        });
    }
};
