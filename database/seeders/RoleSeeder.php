<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
  
    
    public function run(): void
    {
        $employeeRole = Role::create(['name' => 'employee', 'guard_name' => 'web']);
        $superAdminRole = Role::create(['name' => 'super-admin', 'guard_name' => 'web']);

        // Super admin mendapat semua permissions kecuali 'can_access_mobile'
        $allPermissions = Permission::all()->pluck('name')->toArray();
        $permissionsForSuperAdmin = array_diff($allPermissions, ['can_access_mobile','view_request_pending', 'approve_preliminary']);
        $superAdminRole->givePermissionTo($permissionsForSuperAdmin);

        // Employee biasa hanya bisa akses mobile
        $employeeRole->givePermissionTo('can_access_mobile');
    }

}
