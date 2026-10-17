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
        if (!Schema::hasTable('donasi')) {
            Schema::create('donasi', function (Blueprint $table) {
                $table->string('id_donasi')->primary();
                $table->string('id_barang')->index();
                $table->string('id_request_donasi')->nullable()->index();
                $table->string('id_organisasi')->index();
                $table->date('tgl_donasi');
                $table->string('nama_penerima')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donasi');
    }
};
