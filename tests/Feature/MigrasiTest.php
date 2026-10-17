<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MigrasiTest extends TestCase
{
    use RefreshDatabase;

    public function test_semua_tabel_aplikasi_dibuat(): void
    {
        foreach ([
            'role', 'jabatan', 'penitip', 'pembeli', 'organisasi', 'pegawai',
            'merchandise', 'barang', 'alamat', 'pemesanan', 'detail_pemesanan',
            'komisi', 'diskusi_produk', 'request_donasi', 'donasi',
            'klaim_merchandise', 'tukar_poin', 'history_top_seller',
        ] as $tabel) {
            $this->assertTrue(Schema::hasTable($tabel), "Tabel $tabel tidak dibuat.");
        }
    }

    public function test_foreign_key_penting_dibuat(): void
    {
        $this->assertCount(3, Schema::getForeignKeys('barang'));
        $this->assertCount(3, Schema::getForeignKeys('pemesanan'));
        $this->assertCount(2, Schema::getForeignKeys('pegawai'));
        $this->assertCount(1, Schema::getForeignKeys('penitip'));
    }

    public function test_total_foreign_key_sesuai_skema_asli(): void
    {
        $total = 0;
        foreach ([
            'penitip', 'pembeli', 'organisasi', 'pegawai', 'merchandise',
            'barang', 'alamat', 'pemesanan', 'detail_pemesanan', 'komisi',
            'diskusi_produk', 'request_donasi', 'donasi', 'klaim_merchandise',
            'tukar_poin', 'history_top_seller',
        ] as $tabel) {
            $total += count(Schema::getForeignKeys($tabel));
        }

        $this->assertSame(32, $total);
    }
}
