<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Role & Permission dulu
        $this->call([
            RolePermissionSeeder::class,
        ]);

        // 2. Buat User (sebelum seeder lain yang butuh user)
        $admin = User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@mail.com',
            'password' => bcrypt('password'),
        ]);

        $petugas = User::factory()->create([
            'name' => 'Petugas Inventaris',
            'email' => 'petugas@mail.com',
            'password' => bcrypt('password'),
        ]);

        $admin->assignRole('admin');
        $petugas->assignRole('petugas');

        // 3. Seeder lainnya (setelah user sudah ada)
        $this->call([
            KategoriSeeder::class,
            LokasiSeeder::class,
            BarangSeeder::class,
            PeminjamanSeeder::class, // Sekarang aman karena user sudah ada
        ]);
    }
}