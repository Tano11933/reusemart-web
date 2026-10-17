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
        if (!Schema::hasTable('merchandise')) {
            Schema::create('merchandise', function (Blueprint $table) {
                $table->string('id_merch')->primary();
                $table->string('id_pegawai')->index();
                $table->string('nama_merch');
                $table->string('gambar_merch')->nullable();
                $table->integer('stok');
                $table->integer('harga_poin');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchandise');
    }
};
