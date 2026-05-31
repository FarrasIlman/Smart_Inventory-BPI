<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('raw_materials', function (Blueprint $table) {
            // Menggunakan decimal agar presisi untuk satuan berat/panjang (kg, meter, dll)
            $table->decimal('stok_terkunci', 10, 2)->default(0)->after('stok');
        });
    }

    public function down()
    {
        Schema::table('raw_materials', function (Blueprint $table) {
            $table->dropColumn('stok_terkunci');
        });
    }
};
