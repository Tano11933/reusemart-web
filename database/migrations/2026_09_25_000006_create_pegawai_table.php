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
        if (!Schema::hasTable('pegawai')) {
            Schema::create('pegawai', function (Blueprint $table) {
                $table->string('id_pegawai')->primary();
                $table->string('id_jabatan')->index();
                $table->integer('id_role')->index();
                $table->string('email');
                $table->string('password');
                $table->string('nama_pegawai');
                $table->date('tgl_lahir');
                $table->string('nomor_telepon', 15);
                $table->string('alamat');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
