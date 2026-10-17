<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kolom dan foreign key mungkin sudah dibuat oleh migrasi
        // create_pemesanan (skema diambil dari database lama), jadi cukup dilewati.
        if (Schema::hasColumn('pemesanan', 'alamat_pengiriman')) {
            return;
        }

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
        if (! Schema::hasTable('pemesanan') || ! Schema::hasColumn('pemesanan', 'alamat_pengiriman')) {
            return;
        }

        Schema::table('pemesanan', function (Blueprint $table) {
            $table->dropForeign(['alamat_pengiriman']);
            $table->dropColumn('alamat_pengiriman');
        });
    }
};
