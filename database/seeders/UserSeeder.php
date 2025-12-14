<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // Admin
            [
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'wilayah_id' => null,
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Petugas Pos
            [
                'name' => 'Petugas Pos Utama',
                'email' => 'pos@example.com',
                'username' => 'petugas_pos',
                'password' => Hash::make('password'),
                'role' => 'petugas_pos',
                'wilayah_id' => null,
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Keamanan - Wilayah Utara
            [
                'name' => 'Keamanan Utara',
                'email' => 'keamanan.utara@example.com',
                'username' => 'keamanan_utara',
                'password' => Hash::make('password'),
                'role' => 'keamanan',
                'wilayah_id' => 1,
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Keamanan - Wilayah Selatan
            [
                'name' => 'Keamanan Selatan',
                'email' => 'keamanan.selatan@example.com',
                'username' => 'keamanan_selatan',
                'password' => Hash::make('password'),
                'role' => 'keamanan',
                'wilayah_id' => 2,
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Keamanan - Wilayah Timur
            [
                'name' => 'Keamanan Timur',
                'email' => 'keamanan.timur@example.com',
                'username' => 'keamanan_timur',
                'password' => Hash::make('password'),
                'role' => 'keamanan',
                'wilayah_id' => 3,
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Keamanan - Wilayah Barat
            [
                'name' => 'Keamanan Barat',
                'email' => 'keamanan.barat@example.com',
                'username' => 'keamanan_barat',
                'password' => Hash::make('password'),
                'role' => 'keamanan',
                'wilayah_id' => 4,
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Keamanan - Wilayah Tengah
            [
                'name' => 'Keamanan Tengah',
                'email' => 'keamanan.tengah@example.com',
                'username' => 'keamanan_tengah',
                'password' => Hash::make('password'),
                'role' => 'keamanan',
                'wilayah_id' => 5,
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);
    }
}
