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
        if (!Schema::hasTable('detail_pemesanan')) {
            Schema::create('detail_pemesanan', function (Blueprint $table) {
                $table->string('id_detail_pemesanan')->primary();
                $table->integer('rating_diberikan')->nullable();
                $table->string('id_barang')->index();
                $table->string('id_pemesanan')->index();
                $table->double('harga');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pemesanan');
    }
};
