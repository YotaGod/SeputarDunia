<?php

namespace App\Models;

use CodeIgniter\Model;

class RolePermissionModel extends Model
{
    protected $table = 'role_permissions';
    protected $allowedFields = ['role_id', 'permission_id'];
    protected $useTimestamps = false; 
    
    /**
     * Mengecek hak akses berdasarkan Role ID dan Permission Key
     */
    public function hasPermissionForRole(int $roleId, string $permissionKey): bool
    {
        $result = $this->select('role_permissions.permission_id')
                       ->join('permissions', 'permissions.id = role_permissions.permission_id')
                       ->where('role_id', $roleId)
                       ->where('permissions.key', $permissionKey)
                       ->countAllResults();

        return $result > 0;
    }
}