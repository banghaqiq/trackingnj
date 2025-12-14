<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        $wilayah = [
            [
                'nama' => 'Wilayah Utara',
                'kode' => 'WLY-UTR',
                'deskripsi' => 'Wilayah bagian utara kampus',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Wilayah Selatan',
                'kode' => 'WLY-SLT',
                'deskripsi' => 'Wilayah bagian selatan kampus',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Wilayah Timur',
                'kode' => 'WLY-TMR',
                'deskripsi' => 'Wilayah bagian timur kampus',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Wilayah Barat',
                'kode' => 'WLY-BRT',
                'deskripsi' => 'Wilayah bagian barat kampus',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Wilayah Tengah',
                'kode' => 'WLY-TGH',
                'deskripsi' => 'Wilayah bagian tengah kampus',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('wilayah')->insert($wilayah);
    }
}
