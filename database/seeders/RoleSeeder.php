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
        $employeeRole = Role::create(['name' => 'employee','guard_name' => 'web']);
        $superAdminRole = Role::create(['name' => 'super-admin', 'guard_name' => 'web']);

        $ordinaryEmployeePermission = Permission::create(['name' => 'ordinary_employee', 'guard_name' => 'web']);
        $headOfTribePermission = Permission::create(['name' => 'head_of_tribe','guard_name' => 'web']);
        $humanResourcePermission = Permission::create(['name' => 'human_resource', 'guard_name' => 'web']);
        $presidentPermission = Permission::create(['name' => 'president', 'guard_name' => 'web']);

        $employeeRole->givePermissionTo($ordinaryEmployeePermission);
        $employeeRole->givePermissionTo($headOfTribePermission);
        $employeeRole->givePermissionTo($humanResourcePermission);
        $employeeRole->givePermissionTo($presidentPermission);
    }
}
