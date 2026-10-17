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
        if (Schema::hasTable('pemesanan') && count(Schema::getForeignKeys('pemesanan')) === 0) {
            Schema::table('pemesanan', function (Blueprint $table) {
                $table->foreign(['id_pegawai'])->references(['id_pegawai'])->on('pegawai')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign(['id_pembeli'])->references(['id_pembeli'])->on('pembeli')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign(['alamat_pengiriman'])->references(['id_alamat'])->on('alamat')->onUpdate('restrict')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pemesanan')) {
            Schema::table('pemesanan', function (Blueprint $table) {
                $table->dropForeign('pemesanan_id_pegawai_foreign');
                $table->dropForeign('pemesanan_id_pembeli_foreign');
                $table->dropForeign('pemesanan_alamat_pengiriman_foreign');
            });
        }
    }
};
