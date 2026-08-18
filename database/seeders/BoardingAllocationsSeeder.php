<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BoardingAllocationsSeeder extends Seeder
{
    public function run(): void
    {
        // Get all boarding students
        $boardingStudents = DB::table('students')
            ->where('classification', 'boarding')
            ->pluck('id')
            ->toArray();

        // Get all available beds
        $availableBeds = DB::table('beds')
            ->where('status', 'available')
            ->pluck('id')
            ->toArray();

        $allocations = [];
        foreach ($boardingStudents as $index => $studentId) {
            if (isset($availableBeds[$index])) {
                $allocations[] = [
                    'student_id'   => $studentId,
                    'bed_id'       => $availableBeds[$index],
                    'allocated_at' => now()->subDays(rand(1, 30)),
                    'vacated_at'   => null,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];

                // Update bed status to occupied
                DB::table('beds')
                    ->where('id', $availableBeds[$index])
                    ->update(['status' => 'occupied', 'updated_at' => now()]);
            }
        }

        DB::table('boarding_allocations')->insert($allocations);
    }
}
