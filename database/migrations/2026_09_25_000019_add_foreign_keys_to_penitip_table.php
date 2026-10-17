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
        if (Schema::hasTable('penitip') && count(Schema::getForeignKeys('penitip')) === 0) {
            Schema::table('penitip', function (Blueprint $table) {
                $table->foreign(['id_role'])->references(['id_role'])->on('role')->onUpdate('cascade')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('penitip')) {
            Schema::table('penitip', function (Blueprint $table) {
                $table->dropForeign('penitip_id_role_foreign');
            });
        }
    }
};
