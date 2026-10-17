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
        if (!Schema::hasTable('history_top_seller')) {
            Schema::create('history_top_seller', function (Blueprint $table) {
                $table->string('id_history_top_seller')->primary();
                $table->string('id_penitip')->index();
                $table->date('periode');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_top_seller');
    }
};
