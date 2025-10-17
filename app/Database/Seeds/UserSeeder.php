<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'role_id'     => 1, // Administrator
            'username'    => 'admin_seputardunia',
            'email'       => 'admin@seputardunia.com',
            // Ganti 'password123' dengan password yang lebih aman di produksi
            'password'    => password_hash('password123', PASSWORD_BCRYPT),
            'is_subscribed' => 1, // Anggap admin juga berlangganan
            'status'      => 'active',
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $this->db->table('users')->insert($data);
    }
}