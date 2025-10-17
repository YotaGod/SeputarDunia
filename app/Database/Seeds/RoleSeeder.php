<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['id' => 1, 'name' => 'Administrator', 'description' => 'Akses penuh ke semua sistem dan manajemen peran.'],
            ['id' => 2, 'name' => 'Editor', 'description' => 'Mengelola, menyetujui, dan mempublikasikan artikel.'],
            ['id' => 3, 'name' => 'Penulis', 'description' => 'Membuat dan mengedit artikel sendiri.'],
            ['id' => 4, 'name' => 'Pengunjung Berlangganan', 'description' => 'Dapat berkomentar dan mengakses konten eksklusif.'],
            ['id' => 5, 'name' => 'Pengunjung Biasa', 'description' => 'Melihat konten publik.'],
        ];

        $this->db->table('roles')->insertBatch($roles);
    }
}