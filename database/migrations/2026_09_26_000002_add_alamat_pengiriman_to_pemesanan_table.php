<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemesanan', function (Blueprint $table) {
            $table->string('alamat_pengiriman')->nullable()->after('jenis_pengantaran');
            $table->foreign('alamat_pengiriman')
                ->references('id_alamat')
                ->on('alamat')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pemesanan', function (Blueprint $table) {
            $table->dropForeign(['alamat_pengiriman']);
            $table->dropColumn('alamat_pengiriman');
        });
    }
};
