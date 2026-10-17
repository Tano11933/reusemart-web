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
        if (Schema::hasTable('donasi') && count(Schema::getForeignKeys('donasi')) === 0) {
            Schema::table('donasi', function (Blueprint $table) {
                $table->foreign(['id_barang'])->references(['id_barang'])->on('barang')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign(['id_organisasi'])->references(['id_organisasi'])->on('organisasi')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign(['id_request_donasi'])->references(['id_request_donasi'])->on('request_donasi')->onUpdate('cascade')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('donasi')) {
            Schema::table('donasi', function (Blueprint $table) {
                $table->dropForeign('donasi_id_barang_foreign');
                $table->dropForeign('donasi_id_organisasi_foreign');
                $table->dropForeign('donasi_id_request_donasi_foreign');
            });
        }
    }
};
