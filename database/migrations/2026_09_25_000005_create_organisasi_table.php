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
        if (!Schema::hasTable('organisasi')) {
            Schema::create('organisasi', function (Blueprint $table) {
                $table->string('id_organisasi')->primary();
                $table->integer('id_role')->index();
                $table->string('nama_organisasi');
                $table->string('nomor_telepon', 15);
                $table->string('email');
                $table->string('password');
                $table->string('alamat_organisasi');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organisasi');
    }
};
