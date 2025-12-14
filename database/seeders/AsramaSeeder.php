<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AsramaSeeder extends Seeder
{
    public function run(): void
    {
        $asrama = [
            // Wilayah Utara (ID: 1)
            [
                'wilayah_id' => 1,
                'nama' => 'Asrama Putra Utara A',
                'kode' => 'APU-A',
                'alamat' => 'Jalan Utara No. 1',
                'kapasitas' => 100,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'wilayah_id' => 1,
                'nama' => 'Asrama Putri Utara B',
                'kode' => 'APU-B',
                'alamat' => 'Jalan Utara No. 2',
                'kapasitas' => 80,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Wilayah Selatan (ID: 2)
            [
                'wilayah_id' => 2,
                'nama' => 'Asrama Putra Selatan A',
                'kode' => 'APS-A',
                'alamat' => 'Jalan Selatan No. 1',
                'kapasitas' => 120,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'wilayah_id' => 2,
                'nama' => 'Asrama Putri Selatan B',
                'kode' => 'APS-B',
                'alamat' => 'Jalan Selatan No. 2',
                'kapasitas' => 90,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Wilayah Timur (ID: 3)
            [
                'wilayah_id' => 3,
                'nama' => 'Asrama Putra Timur A',
                'kode' => 'APT-A',
                'alamat' => 'Jalan Timur No. 1',
                'kapasitas' => 110,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'wilayah_id' => 3,
                'nama' => 'Asrama Putri Timur B',
                'kode' => 'APT-B',
                'alamat' => 'Jalan Timur No. 2',
                'kapasitas' => 85,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Wilayah Barat (ID: 4)
            [
                'wilayah_id' => 4,
                'nama' => 'Asrama Putra Barat A',
                'kode' => 'APB-A',
                'alamat' => 'Jalan Barat No. 1',
                'kapasitas' => 95,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'wilayah_id' => 4,
                'nama' => 'Asrama Putri Barat B',
                'kode' => 'APB-B',
                'alamat' => 'Jalan Barat No. 2',
                'kapasitas' => 75,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Wilayah Tengah (ID: 5)
            [
                'wilayah_id' => 5,
                'nama' => 'Asrama Putra Tengah',
                'kode' => 'APT-C',
                'alamat' => 'Jalan Tengah No. 1',
                'kapasitas' => 150,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'wilayah_id' => 5,
                'nama' => 'Asrama Putri Tengah',
                'kode' => 'APT-D',
                'alamat' => 'Jalan Tengah No. 2',
                'kapasitas' => 130,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('asrama')->insert($asrama);
    }
}
