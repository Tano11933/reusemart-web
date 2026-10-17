<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Isi database dengan data demo ReuseMart.
     */
    public function run(): void
    {
        $this->call(ReuseMartSeeder::class);
    }
}
