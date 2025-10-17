<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // Hak Akses Role & User
            ['key' => 'role-manage', 'description' => 'Mengelola peran dan hak akses'],
            ['key' => 'user-manage', 'description' => 'Mengelola pengguna (blokir, ganti peran)'],

            // Hak Akses Artikel
            ['key' => 'article-create', 'description' => 'Membuat artikel baru'],
            ['key' => 'article-edit-own', 'description' => 'Mengedit artikel yang dibuat sendiri'],
            ['key' => 'article-edit-all', 'description' => 'Mengedit semua artikel'],
            ['key' => 'article-review', 'description' => 'Meninjau dan menyetujui artikel (pending -> published)'],
            ['key' => 'article-publish', 'description' => 'Mempublikasikan artikel (langsung ke published)'],

            // Hak Akses Lainnya
            ['key' => 'category-manage', 'description' => 'Mengelola kategori dan tag'],
            ['key' => 'comment-manage', 'description' => 'Moderasi komentar'],
            ['key' => 'can-comment', 'description' => 'Dapat memberikan komentar'],
            ['key' => 'access-exclusive-content', 'description' => 'Mengakses konten berlangganan'],
        ];

        $this->db->table('permissions')->insertBatch($permissions);

        // Menghubungkan Permissions ke Roles (Role_Permissions)
        $rolePermissions = [];

        // 1. Administrator: Akses penuh
        $adminPermissions = $this->db->table('permissions')->select('id')->get()->getResultArray();
        foreach ($adminPermissions as $p) {
            $rolePermissions[] = ['role_id' => 1, 'permission_id' => $p['id']];
        }

        // 2. Editor: Mengelola & Publikasi
        $editorKeys = ['article-edit-all', 'article-review', 'article-publish', 'comment-manage', 'category-manage'];
        $editorPermissions = $this->db->table('permissions')->whereIn('key', $editorKeys)->select('id')->get()->getResultArray();
        foreach ($editorPermissions as $p) {
            $rolePermissions[] = ['role_id' => 2, 'permission_id' => $p['id']];
        }

        // 3. Penulis: Buat & Edit milik sendiri
        $writerKeys = ['article-create', 'article-edit-own'];
        $writerPermissions = $this->db->table('permissions')->whereIn('key', $writerKeys)->select('id')->get()->getResultArray();
        foreach ($writerPermissions as $p) {
            $rolePermissions[] = ['role_id' => 3, 'permission_id' => $p['id']];
        }

        // 4. Pengunjung Berlangganan: Komentar & Akses Eksklusif
        $subscriberKeys = ['can-comment', 'access-exclusive-content'];
        $subscriberPermissions = $this->db->table('permissions')->whereIn('key', $subscriberKeys)->select('id')->get()->getResultArray();
        foreach ($subscriberPermissions as $p) {
            $rolePermissions[] = ['role_id' => 4, 'permission_id' => $p['id']];
        }

        // Pengunjung Biasa (Role ID 5) tidak memiliki hak akses khusus selain yang bersifat publik.

        $this->db->table('role_permissions')->insertBatch($rolePermissions);
    }
}