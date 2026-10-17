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
        if (Schema::hasTable('request_donasi') && count(Schema::getForeignKeys('request_donasi')) === 0) {
            Schema::table('request_donasi', function (Blueprint $table) {
                $table->foreign(['id_organisasi'])->references(['id_organisasi'])->on('organisasi')->onUpdate('cascade')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('request_donasi')) {
            Schema::table('request_donasi', function (Blueprint $table) {
                $table->dropForeign('request_donasi_id_organisasi_foreign');
            });
        }
    }
};
