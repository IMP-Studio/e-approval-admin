<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    app()[PermissionRegistrar::class]->forgetCachedPermissions();

    $permissions = [

        'web permissions' => [
            'can_access_web',
        ],

        'divisions' => [
            'view_divisions',
            'add_divisions',
            'edit_divisions',
            'delete_divisions',
            'import_divisions',
            'export_divisions',
        ],

        'positions' => [ 
            'view_positions',
            'add_positions',
            'edit_positions',
            'delete_positions',
            'import_positions',
            'export_positions',
        ],

        'employees' => [
            'view_employees',
            'add_employees',
            'edit_employees',
            'delete_employees',
            'import_employees',
            'export_employees',
        ],

        'presences' => [
            'view_presences',
            'export_presences',
        ],

        'partners' => [
            'view_partners',
            'add_partners',
            'edit_partners',
            'delete_partners',
            'import_partners',
            'export_partners',
        ],

        'projects' => [
            'view_projects',
            'add_projects',
            'edit_projects',
            'delete_projects',
            'import_projects',
            'export_projects',
        ],

        'standups' => [
            'view_standups',
            'export_standups',
        ],

        'user permission' => [
            'view_permission',
            'assign_permission',
        ],

        'approve' => [
            'approve_preliminary', //untuk ht (status berubah dari pending ke preliminary)
            'approve_allowed', //untuk hr (status berbentuk prelminary ke allowed)
            'reject_presence',
        ],

        'mobile access' => [
            'can_access_mobile',
        ],

        'request presence' => [
            'view_request_pending', // untuk status pending
            'view_request_preliminary', // untuk status preliminary
        ],
        
    ];

    foreach ($permissions as $group => $permissionList) {
        foreach ($permissionList as $permission) {
            Permission::create(['name' => $permission, 'group' => $group]);
        }
    }
    // foreach ($permissions as $permission) {
    //     Permission::create(['name' => $permission]);
    // }
}
}