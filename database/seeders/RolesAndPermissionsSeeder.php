<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions by module
        $permissions = [
            // Student Management
            'view students', 'create students', 'edit students', 'delete students',
            'view student histories',

            // Staff Management
            'view staff', 'create staff', 'edit staff', 'delete staff',

            // Academic Management
            'view subjects', 'create subjects', 'edit subjects', 'delete subjects',
            'view assessments', 'create assessments', 'edit assessments', 'delete assessments',
            'view results', 'create results', 'edit results', 'delete results',
            'view teacher subjects', 'create teacher subjects', 'edit teacher subjects', 'delete teacher subjects',

            // Boarding Management
            'view boarding', 'manage houses', 'manage dormitories', 'manage rooms', 'manage beds',
            'allocate beds', 'view boarding attendance', 'record boarding attendance',
            'manage movements', 'view movements', 'approve movements',
            'view meals', 'manage meals',
            'view resources', 'manage resources',
            'view incidents', 'report incidents',

            // Financial Management
            'view fees', 'manage fee structures', 'view invoices', 'create invoices', 'edit invoices',
            'view payments', 'record payments', 'delete payments',
            'view income', 'record income',
            'view expenses', 'record expenses',
            'view budgets', 'manage budgets',
            'view suppliers', 'manage suppliers',
            'view purchase requests', 'create purchase requests', 'approve purchase requests',
            'view purchase orders', 'create purchase orders',

            // Reports & Dashboard
            'view dashboard', 'view reports', 'generate reports',

            // System Administration
            'manage users', 'manage roles', 'view audit logs',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Define roles and their permissions
        $roles = [
            'Super Admin' => Permission::all()->pluck('name')->toArray(),

            'Head Teacher' => [
                'view students', 'create students', 'edit students',
                'view staff', 'create staff', 'edit staff',
                'view subjects', 'view assessments', 'view results',
                'view boarding', 'view boarding attendance', 'view movements', 'approve movements',
                'view fees', 'view invoices', 'view payments', 'view income', 'view expenses', 'view budgets',
                'view dashboard', 'view reports', 'generate reports',
            ],

            'Teacher' => [
                'view students', 'edit students',
                'view subjects', 'view assessments', 'create assessments', 'edit assessments',
                'view results', 'create results', 'edit results',
                'view teacher subjects',
                'view dashboard', 'view reports',
            ],

            'Boarding Officer' => [
                'view students', 'view boarding',
                'manage houses', 'manage dormitories', 'manage rooms', 'manage beds',
                'allocate beds', 'view boarding attendance', 'record boarding attendance',
                'view movements', 'manage movements', 'approve movements',
                'view meals', 'manage meals',
                'view resources', 'manage resources',
                'view incidents', 'report incidents',
                'view dashboard', 'view reports',
            ],

            'Warden/Matron' => [
                'view students', 'view boarding',
                'view boarding attendance', 'record boarding attendance',
                'view movements', 'manage movements',
                'view meals',
                'view resources',
                'view incidents', 'report incidents',
                'view dashboard',
            ],

            'Bursar' => [
                'view students', 'view staff',
                'view fees', 'manage fee structures',
                'view invoices', 'create invoices', 'edit invoices',
                'view payments', 'record payments',
                'view income', 'record income',
                'view expenses', 'record expenses',
                'view budgets', 'manage budgets',
                'view suppliers', 'manage suppliers',
                'view purchase requests', 'approve purchase requests',
                'view purchase orders', 'create purchase orders',
                'view dashboard', 'view reports', 'generate reports',
            ],

            'Accountant' => [
                'view students',
                'view fees', 'view invoices',
                'view payments', 'record payments',
                'view income', 'record income',
                'view expenses', 'record expenses',
                'view budgets',
                'view dashboard', 'view reports',
            ],

            'Store/Procurement Officer' => [
                'view suppliers', 'manage suppliers',
                'view purchase requests', 'create purchase requests',
                'view purchase orders', 'create purchase orders',
                'view expenses',
                'view dashboard',
            ],

            'Auditor' => [
                'view students', 'view staff',
                'view subjects', 'view assessments', 'view results',
                'view boarding', 'view boarding attendance', 'view movements',
                'view fees', 'view invoices', 'view payments', 'view income', 'view expenses', 'view budgets',
                'view audit logs',
                'view reports', 'generate reports',
            ],
        ];

        // Create roles and assign permissions
        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::create(['name' => $roleName, 'guard_name' => 'web']);
            $role->givePermissionTo($rolePermissions);
        }
    }
}
