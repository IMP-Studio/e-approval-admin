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
            'view_users',
            'add_users',
            'edit_users',
            'delete_users',
            'import_users',
            'export_users',

            'view_divisions',
            'add_divisions',
            'edit_divisions',
            'delete_divisions',
            'import_divisions',
            'export_divisions',

            'view_positions',
            'add_positions',
            'edit_positions',
            'delete_positions',
            'import_positions',
            'export_positions',

            'view_employees',
            'add_employees',
            'edit_employees',
            'delete_employees',
            'import_employees',
            'export_employees',

            'view_presences',
            'add_presences',
            'edit_presences',
            'delete_presences',
            'import_presences',
            'export_presences',

            'view_partners',
            'add_partners',
            'edit_partners',
            'delete_partners',
            'import_partners',
            'export_partners',

            'view_projects',
            'add_projects',
            'edit_projects',
            'delete_projects',
            'import_projects',
            'export_projects',

            'view_leaves',
            'add_leaves',
            'edit_leaves',
            'delete_leaves',
            'import_leaves',
            'export_leaves',

            'view_standups',
            'add_standups',
            'edit_standups',
            'delete_standups',
            'import_standups',
            'export_standups',

            'view_teleworks',
            'add_teleworks',
            'edit_teleworks',
            'delete_teleworks',
            'import_teleworks',
            'export_teleworks',
        ];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

    }
}
