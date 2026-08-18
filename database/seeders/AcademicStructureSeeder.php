<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcademicStructureSeeder extends Seeder
{
    public function run(): void
    {
        // Academic Years
        $years = [
            ['name' => '2024 Academic Year', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '2025 Academic Year', 'is_active' => false, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('academic_years')->insert($years);

        // Terms
        $terms     = [];
        $termNames = ['Term 1', 'Term 2', 'Term 3'];
        for ($yearId = 1; $yearId <= 2; $yearId++) {
            foreach ($termNames as $index => $name) {
                $terms[] = [
                    'academic_year_id' => $yearId,
                    'name'             => $name,
                    'is_active'        => ($yearId == 1 && $index == 0), // Only Term 1 of 2024 is active
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            }
        }
        DB::table('terms')->insert($terms);

        // Classes (Forms)
        $classes = [
            ['name' => 'Form 1A', 'level' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Form 1B', 'level' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Form 2A', 'level' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Form 2B', 'level' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Form 3A', 'level' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Form 3B', 'level' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Form 4A', 'level' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Form 4B', 'level' => 4, 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('classes')->insert($classes);

        // Subjects
        $subjects = [
            ['name' => 'Mathematics', 'code' => 'MATH', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'English', 'code' => 'ENG', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kiswahili', 'code' => 'KISW', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Biology', 'code' => 'BIO', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Chemistry', 'code' => 'CHEM', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Physics', 'code' => 'PHY', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'History', 'code' => 'HIST', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Geography', 'code' => 'GEO', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Business Studies', 'code' => 'BUS', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Agriculture', 'code' => 'AGR', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Computer Studies', 'code' => 'COMP', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Religious Education', 'code' => 'RE', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Physical Education', 'code' => 'PE', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Home Science', 'code' => 'HOME', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Art & Design', 'code' => 'ART', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('subjects')->insert($subjects);
    }
}
