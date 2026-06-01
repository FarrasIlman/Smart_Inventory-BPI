<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('productions', function (Blueprint $table) {
            $table->string('warna_artikel')->nullable()->after('id_order');
            $table->string('model_potongan')->nullable()->after('warna_artikel');
            $table->string('petugas')->nullable()->after('model_potongan');
            $table->date('deadline_produksi')->nullable()->after('petugas');
            $table->text('catatan_potong')->nullable()->after('deadline_produksi');
        });
    }

    public function down()
    {
        Schema::table('productions', function (Blueprint $table) {
            $table->dropColumn(['warna_artikel', 'model_potongan', 'petugas', 'deadline_produksi', 'catatan_potong']);
        });
    }
};
