<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentsAndPositionsSeeder extends Seeder
{
    public function run(): void
    {
        // Departments
        $departments = [
            ['name' => 'Administration', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Academic', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Science', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mathematics', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Languages', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Humanities', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Technical', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Boarding', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Finance', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Procurement', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sports', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Guidance & Counseling', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('departments')->insert($departments);

        // Positions
        $positions = [
            ['name' => 'Head Teacher', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Deputy Head Teacher', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Senior Teacher', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Head of Department', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Teacher', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Boarding Officer', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Warden', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Matron', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bursar', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Accountant', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Procurement Officer', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Storekeeper', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Auditor', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Librarian', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lab Technician', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sports Officer', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Guidance Counselor', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Secretary', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cleaner', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Security Guard', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('positions')->insert($positions);
    }
}
