<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kolom mungkin sudah dibuat oleh migrasi create_pemesanan, jadi cukup dilewati.
        if (Schema::hasColumn('pemesanan', 'metode_pembayaran')) {
            return;
        }

        Schema::table('pemesanan', function (Blueprint $table) {
            $table->string('metode_pembayaran', 24)->nullable()->after('bukti_transfer');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('pemesanan') || ! Schema::hasColumn('pemesanan', 'metode_pembayaran')) {
            return;
        }

        Schema::table('pemesanan', function (Blueprint $table) {
            $table->dropColumn('metode_pembayaran');
        });
    }
};
