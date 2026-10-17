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
        if (Schema::hasTable('diskusi_produk') && count(Schema::getForeignKeys('diskusi_produk')) === 0) {
            Schema::table('diskusi_produk', function (Blueprint $table) {
                $table->foreign(['id_barang'])->references(['id_barang'])->on('barang')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign(['id_pegawai'])->references(['id_pegawai'])->on('pegawai')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign(['id_pembeli'])->references(['id_pembeli'])->on('pembeli')->onUpdate('cascade')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('diskusi_produk')) {
            Schema::table('diskusi_produk', function (Blueprint $table) {
                $table->dropForeign('diskusi_produk_id_barang_foreign');
                $table->dropForeign('diskusi_produk_id_pegawai_foreign');
                $table->dropForeign('diskusi_produk_id_pembeli_foreign');
            });
        }
    }
};
