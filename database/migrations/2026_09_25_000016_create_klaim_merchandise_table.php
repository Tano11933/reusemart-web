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
        if (!Schema::hasTable('klaim_merchandise')) {
            Schema::create('klaim_merchandise', function (Blueprint $table) {
                $table->integer('id_klaim', true);
                $table->string('id_pembeli', 20)->index();
                $table->string('id_merch', 10)->index();
                $table->date('tanggal_klaim');
                $table->date('tanggal_ambil')->nullable();
                $table->string('status_ambil')->nullable();
                $table->string('id_pegawai', 10)->index();
                $table->text('keterangan')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('klaim_merchandise');
    }
};
