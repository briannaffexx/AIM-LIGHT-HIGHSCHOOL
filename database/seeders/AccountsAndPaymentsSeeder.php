<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountsAndPaymentsSeeder extends Seeder
{
    public function run(): void
    {
        $students      = DB::table('students')->pluck('id')->toArray();
        $activeTerm    = DB::table('terms')->where('is_active', true)->first();
        $feeCategories = DB::table('fee_categories')->pluck('id', 'code');
        $users         = DB::table('users')->pluck('id')->toArray();

        if (! $activeTerm) {
            return;
        }

        // Get fee structures for each classification
        $feeStructures = DB::table('fee_structures')
            ->where('term_id', $activeTerm->id)
            ->get()
            ->groupBy('classification');

        foreach ($students as $studentId) {
            $student        = DB::table('students')->find($studentId);
            $classification = $student->classification;

            // Calculate total fees for this student
            $totalFees = 0;
            $fees      = $feeStructures[$classification] ?? [];

            foreach ($fees as $fee) {
                $totalFees += $fee->amount;
            }

            // Create invoice
            $invoiceId = DB::table('invoices')->insertGetId([
                'student_id'  => $studentId,
                'term_id'     => $activeTerm->id,
                'description' => 'Term ' . $activeTerm->name . ' Fees - ' . $classification,
                'amount_due'  => $totalFees,
                'status'      => rand(0, 3) === 0 ? 'paid' : 'unpaid', // 25% chance of being paid
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            // Create student account
            $totalPaid = 0;
            if (rand(0, 3) === 0) {
                // Some students have paid
                $paidCategories = array_rand(array_flip($fees->pluck('fee_category_id')->toArray()), rand(1, 3));
                if (! is_array($paidCategories)) {
                    $paidCategories = [$paidCategories];
                }

                foreach ($paidCategories as $catId) {
                    $fee = $fees->firstWhere('fee_category_id', $catId);
                    if ($fee) {
                        $amount     = rand(0, 1) === 1 ? $fee->amount : rand(500, $fee->amount);
                        $totalPaid += $amount;

                        DB::table('payments')->insert([
                            'invoice_id'        => $invoiceId,
                            'student_id'        => $studentId,
                            'term_id'           => $activeTerm->id,
                            'fee_category_id'   => $catId,
                            'payment_reference' => 'REF-' . date('Y') . '-' . strtoupper(uniqid()),
                            'amount'            => $amount,
                            'payment_date'      => now()->subDays(rand(1, 30)),
                            'payment_method'    => ['Cash', 'Bank Transfer', 'M-PESA', 'Cheque'][array_rand(['Cash', 'Bank Transfer', 'M-PESA', 'Cheque'])],
                            'recorded_by'       => $users[array_rand($users)],
                            'created_at'        => now(),
                            'updated_at'        => now(),
                        ]);
                    }
                }
            }

            $balance = $totalFees - $totalPaid;

            DB::table('student_accounts')->insert([
                'student_id'     => $studentId,
                'balance'        => $balance,
                'total_invoiced' => $totalFees,
                'total_paid'     => $totalPaid,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }
    }
}