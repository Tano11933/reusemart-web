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
        if (!Schema::hasTable('request_donasi')) {
            Schema::create('request_donasi', function (Blueprint $table) {
                $table->string('id_request_donasi')->primary();
                $table->string('id_organisasi')->index();
                $table->date('tgl_request_donasi');
                $table->string('nama_barang_request');
                $table->string('status_req')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_donasi');
    }
};
