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
        if (!Schema::hasTable('komisi')) {
            Schema::create('komisi', function (Blueprint $table) {
                $table->string('id_komisi')->primary();
                $table->string('id_barang')->nullable()->index();
                $table->string('id_pemesanan')->index();
                $table->string('id_pegawai')->nullable()->index();
                $table->double('komisi_perusahaan');
                $table->double('komisi_hunter')->nullable();
                $table->double('bonus')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komisi');
    }
};
