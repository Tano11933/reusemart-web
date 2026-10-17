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
        if (Schema::hasTable('merchandise') && count(Schema::getForeignKeys('merchandise')) === 0) {
            Schema::table('merchandise', function (Blueprint $table) {
                $table->foreign(['id_pegawai'])->references(['id_pegawai'])->on('pegawai')->onUpdate('cascade')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('merchandise')) {
            Schema::table('merchandise', function (Blueprint $table) {
                $table->dropForeign('merchandise_id_pegawai_foreign');
            });
        }
    }
};
