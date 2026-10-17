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
        if (!Schema::hasTable('pemesanan')) {
            Schema::create('pemesanan', function (Blueprint $table) {
                $table->string('id_pemesanan')->primary();
                $table->string('id_pembeli')->index();
                $table->string('id_pegawai')->nullable()->index();
                $table->dateTime('tgl_pesan');
                $table->date('tgl_kirim')->nullable();
                $table->date('tgl_ambil')->nullable();
                $table->integer('poin_pesanan')->nullable();
                $table->string('status');
                $table->string('jenis_pengantaran');
                $table->string('alamat_pengiriman')->nullable()->index();
                $table->dateTime('tgl_pembayaran')->nullable();
                $table->double('total_harga_pesanan')->nullable();
                $table->double('total_ongkir')->nullable();
                $table->double('harga_setelah_ongkir')->nullable();
                $table->double('potongan_harga')->nullable();
                $table->double('total_harga')->nullable();
                $table->string('bukti_transfer')->nullable();
                $table->string('metode_pembayaran', 24)->nullable();
                $table->string('status_pembayaran')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemesanan');
    }
};
