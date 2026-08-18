<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Core structure must come first
            RolesAndPermissionsSeeder::class,
            DepartmentsAndPositionsSeeder::class,
            AcademicStructureSeeder::class,

            // Users and staff
            UsersAndStaffSeeder::class,

            // Students
            StudentsSeeder::class,

            // Boarding structure
            BoardingStructureSeeder::class,

            // Boarding allocations
            BoardingAllocationsSeeder::class,

            // Academic assignments
            TeacherSubjectsSeeder::class,

            // Financial structures
            FeeStructuresSeeder::class,

            // Accounts and payments
            AccountsAndPaymentsSeeder::class,

            // Expenses and income
            ExpensesAndIncomeSeeder::class,

            // Procurement
            SuppliersAndProcurementSeeder::class,

            // Meal schedules
            MealSchedulesSeeder::class,
        ]);
    }
}
