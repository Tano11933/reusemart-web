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
        if (Schema::hasTable('detail_pemesanan') && count(Schema::getForeignKeys('detail_pemesanan')) === 0) {
            Schema::table('detail_pemesanan', function (Blueprint $table) {
                $table->foreign(['id_barang'])->references(['id_barang'])->on('barang')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign(['id_pemesanan'])->references(['id_pemesanan'])->on('pemesanan')->onUpdate('cascade')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('detail_pemesanan')) {
            Schema::table('detail_pemesanan', function (Blueprint $table) {
                $table->dropForeign('detail_pemesanan_id_barang_foreign');
                $table->dropForeign('detail_pemesanan_id_pemesanan_foreign');
            });
        }
    }
};
