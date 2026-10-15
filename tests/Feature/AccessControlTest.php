<?php

namespace Tests\Feature;

use Tests\TestCase;

class AccessControlTest extends TestCase
{
    /**
     * Halaman internal tidak boleh bisa dibuka tanpa login.
     */
    public function test_pengunjung_diarahkan_ke_halaman_login(): void
    {
        $halaman = [
            '/home-beli',
            '/cart',
            '/pembeli-alamat',
            '/checkout',
            '/penitip/barang-titipan',
            '/penitip/profil',
            '/organisasi/index_organisasi',
            '/pegawai/home',
            '/pegawai/admin',
            '/pegawai/gudang',
            '/pegawai/cs',
            '/jabatan',
            '/klaim-merchandise-cs',
            '/tukar-poin-cs',
            '/pegawai/nota-pemesanan',
        ];

        foreach ($halaman as $uri) {
            $this->get($uri)->assertRedirect('/login');
        }
    }

    public function test_pembeli_tidak_bisa_membuka_halaman_pegawai(): void
    {
        $this->withSession($this->sesi('pembeli', 'PB1'))
            ->get('/pegawai/admin')
            ->assertRedirect('/home-beli')
            ->assertSessionHas('error');
    }

    public function test_penitip_tidak_bisa_membuka_halaman_pembeli(): void
    {
        $this->withSession($this->sesi('penitip', 'P1'))
            ->get('/cart')
            ->assertRedirect('/penitip/barang-titipan')
            ->assertSessionHas('error');
    }

    public function test_organisasi_tidak_bisa_membuka_halaman_pegawai(): void
    {
        $this->withSession($this->sesi('organisasi', 'ORG1'))
            ->get('/pegawai/admin')
            ->assertRedirect('/organisasi/index_organisasi')
            ->assertSessionHas('error');
    }

    public function test_pegawai_gudang_tidak_bisa_membuka_halaman_admin(): void
    {
        $this->withSession($this->sesi('pegawai', 'PG1', 'Gudang'))
            ->get('/jabatan')
            ->assertRedirect('/pegawai/home')
            ->assertSessionHas('error');
    }

    public function test_pegawai_cs_tidak_bisa_membuka_halaman_gudang(): void
    {
        $this->withSession($this->sesi('pegawai', 'PG2', 'Customer Service'))
            ->get('/pegawai/gudang')
            ->assertRedirect('/pegawai/cs')
            ->assertSessionHas('error');
    }

    public function test_pegawai_admin_tidak_bisa_membuka_laporan_owner(): void
    {
        $this->withSession($this->sesi('pegawai', 'PG3', 'Admin'))
            ->get('/owner/laporan-penjualan-bulan')
            ->assertRedirect('/pegawai/admin')
            ->assertSessionHas('error');
    }

    public function test_permintaan_json_tanpa_login_dijawab_401(): void
    {
        $this->getJson('/cart')->assertStatus(401);
    }

    /**
     * Susun data session seperti hasil login AuthController.
     */
    private function sesi(string $role, string $id, ?string $jabatan = null): array
    {
        $data = [
            'role' => $role,
            'user_id' => $id,
            'nama' => 'Pengguna Uji',
            'jabatan' => $jabatan,
        ];

        $kolom = [
            'pembeli' => 'id_pembeli',
            'penitip' => 'id_penitip',
            'organisasi' => 'id_organisasi',
            'pegawai' => 'id_pegawai',
        ];

        if (isset($kolom[$role])) {
            $data[$kolom[$role]] = $id;
        }

        return $data;
    }
}
