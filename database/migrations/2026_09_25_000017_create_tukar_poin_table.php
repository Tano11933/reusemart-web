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
        if (!Schema::hasTable('tukar_poin')) {
            Schema::create('tukar_poin', function (Blueprint $table) {
                $table->string('id_tukar_poin')->primary();
                $table->string('id_pembeli')->index();
                $table->string('id_merch')->index();
                $table->string('id_pegawai')->nullable()->index();
                $table->date('tgl_tukar');
                $table->date('tgl_ambil')->nullable();
                $table->integer('jml');
                $table->string('status')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tukar_poin');
    }
};
