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
        if (Schema::hasTable('history_top_seller') && count(Schema::getForeignKeys('history_top_seller')) === 0) {
            Schema::table('history_top_seller', function (Blueprint $table) {
                $table->foreign(['id_penitip'])->references(['id_penitip'])->on('penitip')->onUpdate('cascade')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('history_top_seller')) {
            Schema::table('history_top_seller', function (Blueprint $table) {
                $table->dropForeign('history_top_seller_id_penitip_foreign');
            });
        }
    }
};
