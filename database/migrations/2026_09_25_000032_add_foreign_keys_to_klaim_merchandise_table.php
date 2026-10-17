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
        if (Schema::hasTable('klaim_merchandise') && count(Schema::getForeignKeys('klaim_merchandise')) === 0) {
            Schema::table('klaim_merchandise', function (Blueprint $table) {
                $table->foreign(['id_pegawai'])->references(['id_pegawai'])->on('pegawai')->onUpdate('cascade')->onDelete('restrict');
                $table->foreign(['id_merch'])->references(['id_merch'])->on('merchandise')->onUpdate('cascade')->onDelete('restrict');
                $table->foreign(['id_pembeli'])->references(['id_pembeli'])->on('pembeli')->onUpdate('cascade')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('klaim_merchandise')) {
            Schema::table('klaim_merchandise', function (Blueprint $table) {
                $table->dropForeign('klaim_merchandise_id_pegawai_foreign');
                $table->dropForeign('klaim_merchandise_id_merch_foreign');
                $table->dropForeign('klaim_merchandise_id_pembeli_foreign');
            });
        }
    }
};
