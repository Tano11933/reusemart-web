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
        if (Schema::hasTable('alamat') && count(Schema::getForeignKeys('alamat')) === 0) {
            Schema::table('alamat', function (Blueprint $table) {
                $table->foreign(['id_pembeli'])->references(['id_pembeli'])->on('pembeli')->onUpdate('cascade')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('alamat')) {
            Schema::table('alamat', function (Blueprint $table) {
                $table->dropForeign('alamat_id_pembeli_foreign');
            });
        }
    }
};
