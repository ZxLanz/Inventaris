<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

<<<<<<< HEAD
        // Create permissions - YANG LAMA
=======
        // Create permissions
>>>>>>> 7128ee3caecc07cd0adb1d836df3fe5b20ca7d83
        Permission::create(['name' => 'manage barang']);
        Permission::create(['name' => 'delete barang']);
        Permission::create(['name' => 'view kategori']);
        Permission::create(['name' => 'manage kategori']);
        Permission::create(['name' => 'view lokasi']);
        Permission::create(['name' => 'manage lokasi']);

<<<<<<< HEAD
        // BARU - Permission untuk Peminjaman
        Permission::create(['name' => 'view peminjaman']);
        Permission::create(['name' => 'manage peminjaman']);
        Permission::create(['name' => 'approve peminjaman']);

=======
>>>>>>> 7128ee3caecc07cd0adb1d836df3fe5b20ca7d83
        // Create roles and assign existing permissions
        $petugasRole = Role::create(['name' => 'petugas']);
        $adminRole = Role::create(['name' => 'admin']);

<<<<<<< HEAD
        // Petugas permissions
=======
>>>>>>> 7128ee3caecc07cd0adb1d836df3fe5b20ca7d83
        $petugasRole->givePermissionTo([
            'manage barang',
            'view kategori',
            'view lokasi',
<<<<<<< HEAD
            // BARU - Petugas bisa view & manage peminjaman
            'view peminjaman',
            'manage peminjaman',
            // Tapi TIDAK bisa approve
        ]);

        // Admin permissions - semua akses
=======
        ]);

>>>>>>> 7128ee3caecc07cd0adb1d836df3fe5b20ca7d83
        $adminRole->givePermissionTo(Permission::all());
    }
}