<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeacherSubjectsSeeder extends Seeder
{
    public function run(): void
    {
        // Get teacher staff IDs (positions with 'Teacher' in name)
        $teacherPositions = DB::table('positions')
            ->where('name', 'like', '%Teacher%')
            ->orWhere('name', 'Head of Department')
            ->pluck('id')
            ->toArray();

        $teacherStaff = DB::table('staff')
            ->whereIn('position_id', $teacherPositions)
            ->pluck('id')
            ->toArray();

        $subjects = DB::table('subjects')->pluck('id')->toArray();
        $classes = DB::table('classes')->pluck('id')->toArray();

        $assignments = [];
        foreach ($teacherStaff as $staffId) {
            // Assign 2-4 subjects per teacher
            $numSubjects = rand(2, 4);
            $assignedSubjects = array_rand(array_flip($subjects), $numSubjects);

            if (!is_array($assignedSubjects)) {
                $assignedSubjects = [$assignedSubjects];
            }

            foreach ($assignedSubjects as $subjectId) {
                // Assign to 1-3 classes
                $numClasses = rand(1, 3);
                $assignedClasses = array_rand(array_flip($classes), $numClasses);

                if (!is_array($assignedClasses)) {
                    $assignedClasses = [$assignedClasses];
                }

                foreach ($assignedClasses as $classId) {
                    $assignments[] = [
                        'staff_id' => $staffId,
                        'subject_id' => $subjectId,
                        'class_id' => $classId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        DB::table('teacher_subjects')->insert($assignments);
    }
}
