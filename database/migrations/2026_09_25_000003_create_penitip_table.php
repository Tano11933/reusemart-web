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
        if (!Schema::hasTable('penitip')) {
            Schema::create('penitip', function (Blueprint $table) {
                $table->string('id_penitip')->primary();
                $table->integer('id_role')->index();
                $table->string('nama_penitip');
                $table->string('nik_penitip', 50);
                $table->string('jenis_kelamin');
                $table->date('tgl_lahir');
                $table->string('nomor_telepon', 15);
                $table->double('rating')->nullable();
                $table->double('saldo')->nullable();
                $table->string('email');
                $table->string('password');
                $table->double('poin')->nullable();
                $table->string('foto_ktp')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penitip');
    }
};
