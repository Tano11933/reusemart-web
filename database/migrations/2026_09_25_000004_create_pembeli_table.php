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
        if (!Schema::hasTable('pembeli')) {
            Schema::create('pembeli', function (Blueprint $table) {
                $table->string('id_pembeli')->primary();
                $table->integer('id_role')->index();
                $table->string('nama_pembeli');
                $table->string('jenis_kelamin');
                $table->date('tgl_lahir');
                $table->string('nomor_telepon', 15);
                $table->string('email');
                $table->string('password');
                $table->double('poin')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembeli');
    }
};
