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
        if (!Schema::hasTable('barang')) {
            Schema::create('barang', function (Blueprint $table) {
                $table->string('id_barang')->primary();
                $table->string('id_penitip')->index();
                $table->string('id_gudang')->index();
                $table->string('id_hunter')->nullable()->index();
                $table->string('nama_barang');
                $table->string('deskripsi_barang');
                $table->string('kategori');
                $table->double('harga_barang');
                $table->integer('berat_barang')->nullable();
                $table->dateTime('tgl_titip');
                $table->date('tgl_garansi')->nullable();
                $table->date('tgl_laku')->nullable();
                $table->date('tgl_akhir');
                $table->date('tgl_ambil')->nullable();
                $table->boolean('garansi');
                $table->boolean('perpanjangan');
                $table->string('status');
                $table->string('foto_barang')->nullable();
                $table->string('foto_barang2');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
