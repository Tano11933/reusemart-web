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
        if (Schema::hasTable('pembeli') && count(Schema::getForeignKeys('pembeli')) === 0) {
            Schema::table('pembeli', function (Blueprint $table) {
                $table->foreign(['id_role'])->references(['id_role'])->on('role')->onUpdate('cascade')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pembeli')) {
            Schema::table('pembeli', function (Blueprint $table) {
                $table->dropForeign('pembeli_id_role_foreign');
            });
        }
    }
};
