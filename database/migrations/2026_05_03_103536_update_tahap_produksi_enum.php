<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
    {
        DB::statement("
            ALTER TABLE orders 
            MODIFY tahap_produksi 
            ENUM('potong','branding','jahit','finishing','quality check','selesai')
            NULL
        ");
    }

    public function down()
    {
        DB::statement("
            ALTER TABLE orders 
            MODIFY tahap_produksi 
            ENUM('potong','branding','jahit','finishing','quality check')
            NULL
        ");
    }
};
