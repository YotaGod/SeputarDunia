<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('RoleSeeder');
        $this->call('PermissionSeeder');
        $this->call('UserSeeder');
        $this->call('CategorySeeder'); // Akan ditambahkan nanti jika diperlukan data kategori dummy
    }
}