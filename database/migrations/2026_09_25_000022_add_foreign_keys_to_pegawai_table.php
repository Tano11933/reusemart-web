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
        if (Schema::hasTable('pegawai') && count(Schema::getForeignKeys('pegawai')) === 0) {
            Schema::table('pegawai', function (Blueprint $table) {
                $table->foreign(['id_jabatan'])->references(['id_jabatan'])->on('jabatan')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign(['id_role'])->references(['id_role'])->on('role')->onUpdate('restrict')->onDelete('restrict');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pegawai')) {
            Schema::table('pegawai', function (Blueprint $table) {
                $table->dropForeign('pegawai_id_jabatan_foreign');
                $table->dropForeign('pegawai_id_role_foreign');
            });
        }
    }
};
