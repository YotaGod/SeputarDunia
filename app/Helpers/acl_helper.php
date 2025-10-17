<?php
// app/Helpers/acl_helper.php

use App\Models\RolePermissionModel;
use App\Models\SubscriptionModel; // <--- PASTIKAN INI ADA

/**
 * Cek apakah pengguna yang sedang login memiliki hak akses tertentu.
 * (Kode has_permission() dari Tahap 6)
 */
function has_permission(string $permissionKey): bool
{
    // ... (Logika has_permission) ...
    // Pastikan logika ini ada dan benar
    if (!session()->get('isLoggedIn')) {
        return false;
    }

    $roleId = session()->get('role_id');
    if ($roleId == 1) { // Admin
        return true;
    }
    
    $rolePermissionModel = new RolePermissionModel(); 
    return $rolePermissionModel->hasPermissionForRole($roleId, $permissionKey);
}


/**
 * Cek apakah pengguna saat ini adalah pelanggan aktif.
 */
function is_subscriber(): bool
{
    // Pastikan semua kode di sini ada:
    if (!session()->get('isLoggedIn')) {
        return false;
    }

    if (session()->get('is_subscribed') === true) {
        return true;
    }
    
    $userId = session()->get('user_id');
    if (!$userId) return false;

    $subModel = new SubscriptionModel();
    $activeSub = $subModel->where('user_id', $userId)
                          ->where('status', 'active')
                          ->where('end_date >=', date('Y-m-d H:i:s'))
                          ->first();
                          
    if ($activeSub) {
        session()->set('is_subscribed', true);
        return true;
    }

    return false;
}