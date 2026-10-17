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
        if (!Schema::hasTable('diskusi_produk')) {
            Schema::create('diskusi_produk', function (Blueprint $table) {
                $table->string('id_diskusi')->primary();
                $table->string('id_pegawai')->nullable()->index();
                $table->string('id_pembeli')->nullable()->index();
                $table->string('id_barang')->index();
                $table->string('komentar');
                $table->date('tgl_komentar');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diskusi_produk');
    }
};
