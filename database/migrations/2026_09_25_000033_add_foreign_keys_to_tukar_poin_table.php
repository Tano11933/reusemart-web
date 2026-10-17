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
        if (Schema::hasTable('tukar_poin') && count(Schema::getForeignKeys('tukar_poin')) === 0) {
            Schema::table('tukar_poin', function (Blueprint $table) {
                $table->foreign(['id_pegawai'])->references(['id_pegawai'])->on('pegawai')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign(['id_merch'])->references(['id_merch'])->on('merchandise')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign(['id_pembeli'])->references(['id_pembeli'])->on('pembeli')->onUpdate('cascade')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tukar_poin')) {
            Schema::table('tukar_poin', function (Blueprint $table) {
                $table->dropForeign('tukar_poin_id_pegawai_foreign');
                $table->dropForeign('tukar_poin_id_merch_foreign');
                $table->dropForeign('tukar_poin_id_pembeli_foreign');
            });
        }
    }
};
