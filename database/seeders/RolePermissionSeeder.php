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


        // Create permissions - YANG LAMA

        // Create permissions

        Permission::create(['name' => 'manage barang']);
        Permission::create(['name' => 'delete barang']);
        Permission::create(['name' => 'view kategori']);
        Permission::create(['name' => 'manage kategori']);
        Permission::create(['name' => 'view lokasi']);
        Permission::create(['name' => 'manage lokasi']);


        // BARU - Permission untuk Peminjaman
        Permission::create(['name' => 'view peminjaman']);
        Permission::create(['name' => 'manage peminjaman']);
        Permission::create(['name' => 'approve peminjaman']);



        // Create roles and assign existing permissions
        $petugasRole = Role::create(['name' => 'petugas']);
        $adminRole = Role::create(['name' => 'admin']);


        // Petugas permissions


        $petugasRole->givePermissionTo([
            'manage barang',
            'view kategori',
            'view lokasi',

            // BARU - Petugas bisa view & manage peminjaman
            'view peminjaman',
            'manage peminjaman',
            // Tapi TIDAK bisa approve
        ]);

        // Admin permissions - semua akses

        ]);


        $adminRole->givePermissionTo(Permission::all());
    }
}
