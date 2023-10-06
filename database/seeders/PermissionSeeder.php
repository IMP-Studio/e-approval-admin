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

        // For Web
        'can_access_web',

        // For Divisions 
        'view_divisions',
        'add_divisions',
        'edit_divisions',
        'delete_divisions',
        'import_divisions',
        'export_divisions',

        // For Positions 
        'view_positions',
        'add_positions',
        'edit_positions',
        'delete_positions',
        'import_positions',
        'export_positions',

        // For Employees
        'view_employees',
        'add_employees',
        'edit_employees',
        'delete_employees',
        'import_employees',
        'export_employees',

        // For Presences
        'view_presences',
        'export_presences',

        // For Partners
        'view_partners',
        'add_partners',
        'edit_partners',
        'delete_partners',
        'import_partners',
        'export_partners',

        // For Projects
        'view_projects',
        'add_projects',
        'edit_projects',
        'delete_projects',
        'import_projects',
        'export_projects',

        // For Standups
        'view_standups',
        'export_standups',

        // For Roles
        'view_roles',
        'add_roles',
        'edit_roles',
        'delete_roles',
        'import_roles',
        'export_roles',

        //Permission
        'assign_permission',

        // APPROVE
        'approve_preliminary', //untuk ht (status berubah dari pending ke preliminary)
        'approve_allowed', //untuk hr (status berbentuk prelminary ke allowed)
        'reject_presence',

        //MOBILE||
        'can_access_mobile',

        //request 
        'view_request_pending', // untuk status pending
        'view_request_preliminary', // untuk status preliminary
        
    ];

    foreach ($permissions as $permission) {
        Permission::create(['name' => $permission]);
    }
}
}