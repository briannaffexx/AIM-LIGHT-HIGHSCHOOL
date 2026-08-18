<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeeStructuresSeeder extends Seeder
{
    public function run(): void
    {
        // Fee categories
        $categories = [
            ['name' => 'Tuition', 'code' => 'TUITION', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Development', 'code' => 'DEV', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Examination', 'code' => 'EXAM', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Boarding', 'code' => 'BOARDING', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Accommodation', 'code' => 'ACCOMM', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Meals', 'code' => 'MEALS', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ICT', 'code' => 'ICT', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sports', 'code' => 'SPORTS', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Medical', 'code' => 'MEDICAL', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Other', 'code' => 'OTHER', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('fee_categories')->insert($categories);

        // Get active term
        $activeTerm = DB::table('terms')->where('is_active', true)->first();

        if ($activeTerm) {
            $feeCategories = DB::table('fee_categories')->pluck('id', 'code');

            // Fee structures for DAY scholars
            $dayFees = [
                ['code' => 'TUITION', 'amount' => 15000],
                ['code' => 'DEV', 'amount' => 3000],
                ['code' => 'EXAM', 'amount' => 2500],
                ['code' => 'ICT', 'amount' => 1500],
                ['code' => 'SPORTS', 'amount' => 1000],
                ['code' => 'MEDICAL', 'amount' => 2000],
                ['code' => 'OTHER', 'amount' => 1000],
            ];

            foreach ($dayFees as $fee) {
                DB::table('fee_structures')->insert([
                    'classification' => 'day',
                    'fee_category_id' => $feeCategories[$fee['code']],
                    'term_id' => $activeTerm->id,
                    'amount' => $fee['amount'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Fee structures for BOARDING scholars
            $boardingFees = [
                ['code' => 'TUITION', 'amount' => 18000],
                ['code' => 'DEV', 'amount' => 4000],
                ['code' => 'EXAM', 'amount' => 3000],
                ['code' => 'BOARDING', 'amount' => 5000],
                ['code' => 'ACCOMM', 'amount' => 8000],
                ['code' => 'MEALS', 'amount' => 12000],
                ['code' => 'ICT', 'amount' => 2000],
                ['code' => 'SPORTS', 'amount' => 1500],
                ['code' => 'MEDICAL', 'amount' => 3000],
                ['code' => 'OTHER', 'amount' => 1500],
            ];

            foreach ($boardingFees as $fee) {
                DB::table('fee_structures')->insert([
                    'classification' => 'boarding',
                    'fee_category_id' => $feeCategories[$fee['code']],
                    'term_id' => $activeTerm->id,
                    'amount' => $fee['amount'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
