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
        if (Schema::hasTable('barang') && count(Schema::getForeignKeys('barang')) === 0) {
            Schema::table('barang', function (Blueprint $table) {
                $table->foreign(['id_hunter'])->references(['id_pegawai'])->on('pegawai')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign(['id_gudang'])->references(['id_pegawai'])->on('pegawai')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign(['id_penitip'])->references(['id_penitip'])->on('penitip')->onUpdate('cascade')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('barang')) {
            Schema::table('barang', function (Blueprint $table) {
                $table->dropForeign('barang_id_hunter_foreign');
                $table->dropForeign('barang_id_gudang_foreign');
                $table->dropForeign('barang_id_penitip_foreign');
            });
        }
    }
};
