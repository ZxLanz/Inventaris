<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
<<<<<<< HEAD
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
=======
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            KategoriSeeder::class,
            LokasiSeeder::class,
            BarangSeeder::class,
        ]);

        $admin = User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@mail.com',
>>>>>>> 7128ee3caecc07cd0adb1d836df3fe5b20ca7d83
        ]);

        $petugas = User::factory()->create([
            'name' => 'Petugas Inventaris',
            'email' => 'petugas@mail.com',
<<<<<<< HEAD
            'password' => bcrypt('password'),
=======
>>>>>>> 7128ee3caecc07cd0adb1d836df3fe5b20ca7d83
        ]);

        $admin->assignRole('admin');
        $petugas->assignRole('petugas');
<<<<<<< HEAD

        // 3. Seeder lainnya (setelah user sudah ada)
        $this->call([
            KategoriSeeder::class,
            LokasiSeeder::class,
            BarangSeeder::class,
            PeminjamanSeeder::class, // Sekarang aman karena user sudah ada
        ]);
=======
>>>>>>> 7128ee3caecc07cd0adb1d836df3fe5b20ca7d83
    }
}