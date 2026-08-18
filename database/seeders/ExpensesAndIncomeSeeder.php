<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpensesAndIncomeSeeder extends Seeder
{
    public function run(): void
    {
        $users      = DB::table('users')->pluck('id')->toArray();
        $activeTerm = DB::table('terms')->where('is_active', true)->first();

        if (! $activeTerm) {
            return;
        }

        // Expenses
        $expenseCategories = ['Food', 'Utilities', 'Maintenance', 'Teaching Materials', 'Transport',
            'Boarding Supplies', 'ICT', 'Administration', 'Sports', 'Medical'];
        $expenses = [];
        for ($i = 1; $i <= 20; $i++) {
            $expenses[] = [
                'term_id'     => $activeTerm->id,
                'category'    => $expenseCategories[array_rand($expenseCategories)],
                'amount'      => rand(1000, 50000),
                'date'        => now()->subDays(rand(1, 60))->toDateString(),
                'description' => 'Expense ' . $i . ': ' . fake()->sentence(3),
                'recorded_by' => $users[array_rand($users)],
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
        }
        DB::table('expenses')->insert($expenses);

        // Other Income
        $incomeCategories = ['Donations', 'Grants', 'Fundraising', 'Interest', 'Rentals', 'Other'];
        $income           = [];
        for ($i = 1; $i <= 10; $i++) {
            $income[] = [
                'term_id'     => $activeTerm->id,
                'category'    => $incomeCategories[array_rand($incomeCategories)],
                'amount'      => rand(5000, 100000),
                'date'        => now()->subDays(rand(1, 60))->toDateString(),
                'source'      => 'Source ' . $i,
                'description' => 'Income ' . $i . ': ' . fake()->sentence(3),
                'recorded_by' => $users[array_rand($users)],
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
        }
        DB::table('other_income')->insert($income);

        // Budgets
        $budgetCategories = ['Tuition', 'Development', 'Examination', 'Boarding', 'Accommodation',
            'Meals', 'ICT', 'Sports', 'Medical', 'Food', 'Utilities', 'Maintenance',
            'Teaching Materials', 'Transport', 'Administration'];
        $budgets = [];
        foreach ($budgetCategories as $category) {
            $budgeted  = rand(10000, 100000);
            $actual    = rand(5000, $budgeted);
            $budgets[] = [
                'term_id'         => $activeTerm->id,
                'category'        => $category,
                'budgeted_amount' => $budgeted,
                'actual_spent'    => $actual,
                'created_at'      => now(),
                'updated_at'      => now(),
            ];
        }
        DB::table('budgets')->insert($budgets);
    }
}
