<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersAndStaffSeeder extends Seeder
{
    public function run(): void
    {
        // Get department and position IDs
        $departments = DB::table('departments')->pluck('id', 'name');
        $positions = DB::table('positions')->pluck('id', 'name');

        $users = [
            // Admin
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'admin@school.com',
                'password' => Hash::make('password123'),
                'phone' => '0712345678',
                'status' => 'active',
                'user_uuid' => Str::uuid(),
                'staff_number' => 'STF-0001',
                'position' => 'Head Teacher',
                'department' => 'Administration',
                'role' => 'Super Admin',
            ],
            // Head Teacher
            [
                'first_name' => 'James',
                'last_name' => 'Ochieng',
                'email' => 'headteacher@school.com',
                'password' => Hash::make('password123'),
                'phone' => '0723456789',
                'status' => 'active',
                'user_uuid' => Str::uuid(),
                'staff_number' => 'STF-0002',
                'position' => 'Head Teacher',
                'department' => 'Administration',
                'role' => 'Head Teacher',
            ],
            // Teachers
            [
                'first_name' => 'Grace',
                'last_name' => 'Akinyi',
                'email' => 'grace.akinyi@school.com',
                'password' => Hash::make('password123'),
                'phone' => '0734567890',
                'status' => 'active',
                'user_uuid' => Str::uuid(),
                'staff_number' => 'STF-0003',
                'position' => 'Teacher',
                'department' => 'Mathematics',
                'role' => 'Teacher',
            ],
            [
                'first_name' => 'Peter',
                'last_name' => 'Mwangi',
                'email' => 'peter.mwangi@school.com',
                'password' => Hash::make('password123'),
                'phone' => '0745678901',
                'status' => 'active',
                'user_uuid' => Str::uuid(),
                'staff_number' => 'STF-0004',
                'position' => 'Teacher',
                'department' => 'Languages',
                'role' => 'Teacher',
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Njeri',
                'email' => 'sarah.njeri@school.com',
                'password' => Hash::make('password123'),
                'phone' => '0756789012',
                'status' => 'active',
                'user_uuid' => Str::uuid(),
                'staff_number' => 'STF-0005',
                'position' => 'Teacher',
                'department' => 'Science',
                'role' => 'Teacher',
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Odhiambo',
                'email' => 'david.odhiambo@school.com',
                'password' => Hash::make('password123'),
                'phone' => '0767890123',
                'status' => 'active',
                'user_uuid' => Str::uuid(),
                'staff_number' => 'STF-0006',
                'position' => 'Teacher',
                'department' => 'Humanities',
                'role' => 'Teacher',
            ],
            // Boarding Staff
            [
                'first_name' => 'Mary',
                'last_name' => 'Wanjiru',
                'email' => 'boarding@school.com',
                'password' => Hash::make('password123'),
                'phone' => '0778901234',
                'status' => 'active',
                'user_uuid' => Str::uuid(),
                'staff_number' => 'STF-0007',
                'position' => 'Boarding Officer',
                'department' => 'Boarding',
                'role' => 'Boarding Officer',
            ],
            [
                'first_name' => 'Alice',
                'last_name' => 'Okoth',
                'email' => 'matron@school.com',
                'password' => Hash::make('password123'),
                'phone' => '0789012345',
                'status' => 'active',
                'user_uuid' => Str::uuid(),
                'staff_number' => 'STF-0008',
                'position' => 'Matron',
                'department' => 'Boarding',
                'role' => 'Warden/Matron',
            ],
            // Finance Staff
            [
                'first_name' => 'Michael',
                'last_name' => 'Kariuki',
                'email' => 'bursar@school.com',
                'password' => Hash::make('password123'),
                'phone' => '0790123456',
                'status' => 'active',
                'user_uuid' => Str::uuid(),
                'staff_number' => 'STF-0009',
                'position' => 'Bursar',
                'department' => 'Finance',
                'role' => 'Bursar',
            ],
            [
                'first_name' => 'Catherine',
                'last_name' => 'Mwangi',
                'email' => 'accountant@school.com',
                'password' => Hash::make('password123'),
                'phone' => '0701234567',
                'status' => 'active',
                'user_uuid' => Str::uuid(),
                'staff_number' => 'STF-0010',
                'position' => 'Accountant',
                'department' => 'Finance',
                'role' => 'Accountant',
            ],
            // Procurement
            [
                'first_name' => 'Robert',
                'last_name' => 'Kiprop',
                'email' => 'procurement@school.com',
                'password' => Hash::make('password123'),
                'phone' => '0712345670',
                'status' => 'active',
                'user_uuid' => Str::uuid(),
                'staff_number' => 'STF-0011',
                'position' => 'Procurement Officer',
                'department' => 'Procurement',
                'role' => 'Store/Procurement Officer',
            ],
            // Auditor
            [
                'first_name' => 'Jane',
                'last_name' => 'Muthoni',
                'email' => 'auditor@school.com',
                'password' => Hash::make('password123'),
                'phone' => '0723456701',
                'status' => 'active',
                'user_uuid' => Str::uuid(),
                'staff_number' => 'STF-0012',
                'position' => 'Auditor',
                'department' => 'Finance',
                'role' => 'Auditor',
            ],
        ];

        foreach ($users as $userData) {
            $roleName = $userData['role'];
            unset($userData['role']);

            // Create user
            $userId = DB::table('users')->insertGetId([
                'uuid' => $userData['user_uuid'],
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
                'email' => $userData['email'],
                'password' => $userData['password'],
                'phone' => $userData['phone'],
                'status' => $userData['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assign role
            $role = \Spatie\Permission\Models\Role::where('name', $roleName)->first();
            if ($role) {
                DB::table('model_has_roles')->insert([
                    'role_id' => $role->id,
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $userId,
                ]);
            }

            // Create staff record
            DB::table('staff')->insert([
                'user_id' => $userId,
                'staff_number' => $userData['staff_number'],
                'position_id' => $positions[$userData['position']],
                'department_id' => $departments[$userData['department']],
                'employment_status' => 'active',
                'attendance_status' => 'present',
                'responsibilities' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
