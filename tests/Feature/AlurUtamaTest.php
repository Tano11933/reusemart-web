<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlurUtamaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_login_pembeli_lalu_membuka_beranda_pembeli(): void
    {
        $this->post('/login', [
            'email' => 'budi@reusemart.test',
            'password' => 'password123',
        ])->assertOk()->assertJsonPath('success', true);

        $this->get('/home-beli')->assertOk();
    }

    public function test_login_pegawai_admin_lalu_membuka_kelola_pegawai_dan_jabatan(): void
    {
        $this->post('/login', [
            'email' => 'admin@reusemart.test',
            'password' => 'password123',
        ])->assertOk()->assertJsonPath('jabatan', 'Admin');

        $this->get('/pegawai/admin')->assertOk();
        $this->get('/jabatan')->assertOk();
    }

    public function test_login_pegawai_gudang_lalu_membuka_inventaris(): void
    {
        $this->post('/login', [
            'email' => 'gudang@reusemart.test',
            'password' => 'password123',
        ])->assertOk();

        $this->get('/pegawai/gudang')->assertOk();
    }

    public function test_login_customer_service_lalu_membuka_dasbor_cs(): void
    {
        $this->post('/login', [
            'email' => 'cs@reusemart.test',
            'password' => 'password123',
        ])->assertOk();

        $this->get('/pegawai/cs')->assertOk();
        $this->get('/klaim-merchandise-cs')->assertOk();
    }

    public function test_login_mobile_lewat_api(): void
    {
        $this->postJson('/api/login', [
            'email' => 'budi@reusemart.test',
            'password' => 'password123',
        ])->assertOk()->assertJsonPath('success', true);
    }

    public function test_riwayat_mobile_menyediakan_tanggal_pesanan(): void
    {
        $response = $this->getJson('/api/pembeli/PEM1/history-data');

        $response->assertOk()->assertJsonStructure([
            '0' => ['id_pemesanan', 'status', 'tgl_pesan', 'total_harga_pesanan', 'detail'],
        ]);
        $this->assertNotEmpty($response->json('0.tgl_pesan'));
    }

    public function test_data_demo_mobile_mengisi_tugas_riwayat_dan_komisi(): void
    {
        $demoId = 'DEMO-KURIR-' . now()->format('Ym');
        $this->assertDatabaseHas('pemesanan', ['id_pemesanan' => $demoId]);
        $this->assertDatabaseHas('detail_pemesanan', [
            'id_pemesanan' => $demoId,
            'id_barang' => 'R2',
        ]);
        $this->assertDatabaseHas('barang', ['id_barang' => 'R2']);
        $pengiriman = $this->getJson('/api/pegawai/P6/pengiriman')->assertOk();
        $this->assertNotEmpty($pengiriman->json());

        $riwayat = $this->getJson('/api/kurir/P6/history')->assertOk();
        $riwayat->assertJsonStructure([
            '0' => ['id_pemesanan', 'status', 'detail'],
        ]);
        $this->assertNotEmpty($riwayat->json('0.detail'), json_encode($riwayat->json()));

        $komisi = $this->getJson('/api/pegawai/P5/komisi')->assertOk();
        $this->assertGreaterThan(0, $komisi->json('total_komisi'));

        $hunterBarang = $this->getJson('/api/barang/hunter/P5')->assertOk();
        $this->assertNotEmpty($hunterBarang->json());
    }

    public function test_register_pembeli_baru(): void
    {
        $this->post('/register/pembeli', [
            'nama' => 'Wawan Uji',
            'email' => 'wawan@reusemart.test',
            'password' => 'password123',
            'nomor_telepon' => '081234567890',
            'jenis_kelamin' => 'Pria',
            'tgl_lahir' => '2000-01-01',
        ])->assertRedirect('/login');

        $this->assertDatabaseHas('pembeli', ['email' => 'wawan@reusemart.test']);
    }

    public function test_password_salah_ditolak(): void
    {
        $this->post('/login', [
            'email' => 'budi@reusemart.test',
            'password' => 'salah-sekali',
        ])->assertStatus(401)->assertJsonPath('success', false);
    }
}
