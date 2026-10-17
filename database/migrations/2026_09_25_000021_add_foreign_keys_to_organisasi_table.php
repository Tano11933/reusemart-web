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
        if (Schema::hasTable('organisasi') && count(Schema::getForeignKeys('organisasi')) === 0) {
            Schema::table('organisasi', function (Blueprint $table) {
                $table->foreign(['id_role'])->references(['id_role'])->on('role')->onUpdate('cascade')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('organisasi')) {
            Schema::table('organisasi', function (Blueprint $table) {
                $table->dropForeign('organisasi_id_role_foreign');
            });
        }
    }
};
