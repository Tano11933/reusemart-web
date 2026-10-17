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
        if (!Schema::hasTable('alamat')) {
            Schema::create('alamat', function (Blueprint $table) {
                $table->string('id_alamat')->primary();
                $table->string('id_pembeli')->index();
                $table->string('label_alamat');
                $table->string('kecamatan');
                $table->string('kabupaten');
                $table->string('detail_alamat');
                $table->string('desa');
                $table->boolean('default_alamat');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alamat');
    }
};
