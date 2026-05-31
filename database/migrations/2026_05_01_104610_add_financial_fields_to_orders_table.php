<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('orders', function (Blueprint $table) {
        $table->decimal('harga_satuan', 12, 2)->nullable();
        $table->decimal('total_harga', 12, 2)->nullable();
        $table->text('alamat')->nullable();
        $table->string('no_telepon')->nullable();
    });
}

public function down(): void
{
    Schema::table('orders', function (Blueprint $table) {
        $table->dropColumn([
            'harga_satuan',
            'total_harga',
            'alamat',
            'no_telepon'
        ]);
    });
}
};
