<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FeeCategory;
use App\Models\FeeStructure;
use App\Models\Student;
use App\Models\StudentAccount;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\OtherIncome;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\Supplier;
use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use App\Models\Term;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\SystemNotification;

class FinanceController extends Controller
{
    public function feeStructures()
    {
        $categories = FeeCategory::all();
        $structures = FeeStructure::with(['category', 'term'])->get();
        $terms = Term::all();
        return view('finance.fee_structures', compact('categories', 'structures', 'terms'));
    }

    public function storeFeeStructure(Request $request)
    {
        $request->validate([
            'classification' => 'required|in:day,boarding',
            'fee_category_id' => 'required|exists:fee_categories,id',
            'term_id' => 'required|exists:terms,id',
            'amount' => 'required|numeric|min:0',
        ]);

        FeeStructure::updateOrCreate(
            [
                'classification' => $request->classification,
                'fee_category_id' => $request->fee_category_id,
                'term_id' => $request->term_id,
            ],
            ['amount' => $request->amount]
        );

        return redirect()->back()->with('success', 'Fee structure updated successfully.');
    }

    public function studentAccounts(Request $request)
    {
        $query = StudentAccount::with(['student.schoolClass']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%");
            });
        }

        $accounts = $query->paginate(15);
        return view('finance.accounts', compact('accounts'));
    }

    public function invoices($studentId)
    {
        $student = Student::findOrFail($studentId);
        $invoices = Invoice::where('student_id', $studentId)->with('term')->latest()->get();
        $terms = Term::all();
        return view('finance.invoices', compact('student', 'invoices', 'terms'));
    }

    public function storeInvoice(Request $request, $studentId)
    {
        $request->validate([
            'term_id' => 'required|exists:terms,id',
            'description' => 'required|string|max:255',
            'amount_due' => 'required|numeric|min:0',
        ]);

        $invoice = Invoice::create([
            'student_id' => $studentId,
            'term_id' => $request->term_id,
            'description' => $request->description,
            'amount_due' => $request->amount_due,
            'status' => Invoice::STATUS_UNPAID,
        ]);

        // Update student account
        $account = StudentAccount::firstOrCreate(['student_id' => $studentId]);
        $account->increment('total_invoiced', $request->amount_due);
        $account->increment('balance', $request->amount_due);

        return redirect()->back()->with('success', 'Invoice generated successfully.');
    }

    public function storePayment(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,mobile_money',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);
        $amountToPay = $request->amount;

        // Check if amount exceeds what is remaining
        $paidSoFar = $invoice->payments()->sum('amount');
        $remaining = $invoice->amount_due - $paidSoFar;

        if ($amountToPay > $remaining) {
            return redirect()->back()->withErrors(['amount' => "Amount exceeds the remaining invoice balance of " . number_format($remaining, 2)]);
        }

        // Record payment
        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'student_id' => $invoice->student_id,
            'term_id' => $invoice->term_id,
            'fee_category_id' => null, // can be linked if invoice is associated with a category
            'payment_reference' => 'PAY-' . strtoupper(Str::random(8)),
            'amount' => $amountToPay,
            'payment_date' => now(),
            'payment_method' => $request->payment_method,
            'recorded_by' => Auth::id(),
        ]);

        // Update invoice status
        $newPaidTotal = $paidSoFar + $amountToPay;
        if ($newPaidTotal >= $invoice->amount_due) {
            $invoice->update(['status' => Invoice::STATUS_PAID]);
        } else {
            $invoice->update(['status' => Invoice::STATUS_PARTIALLY_PAID]);
        }

        // Update student account
        $account = StudentAccount::where('student_id', $invoice->student_id)->first();
        if ($account) {
            $account->decrement('balance', $amountToPay);
            $account->increment('total_paid', $amountToPay);
        }

        // Notify student if user account exists
        $student = $invoice->student;
        if ($student && $student->user_id) {
            SystemNotification::notifyUser(
                $student->user_id,
                'payment_received',
                'Payment Received',
                'A payment of KES ' . number_format($amountToPay, 2) . ' (Ref: ' . $payment->payment_reference . ') was recorded.',
                '/finance/students/' . $student->id . '/invoices'
            );
        }

        return redirect()->back()->with('success', 'Payment recorded successfully. Ref: ' . $payment->payment_reference);
    }

    public function expenses()
    {
        $expenses = Expense::with(['recorder', 'term'])->latest()->paginate(15);
        $income = OtherIncome::with(['recorder', 'term'])->latest()->paginate(15);
        return view('finance.expenses', compact('expenses', 'income'));
    }

    public function storeExpense(Request $request)
    {
        $request->validate([
            'category' => 'required|in:food,utilities,maintenance,teaching_materials,transport,boarding_supplies,ict,admin,sports,other',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
        ]);

        $activeTerm = Term::where('is_active', true)->first();

        $expense = Expense::create([
            'term_id' => $activeTerm ? $activeTerm->id : null,
            'category' => $request->category,
            'amount' => $request->amount,
            'date' => now()->format('Y-m-d'),
            'description' => $request->description,
            'recorded_by' => Auth::id(),
        ]);

        // Increment actual_spent in active budget if exists
        if ($activeTerm) {
            $budget = Budget::where('term_id', $activeTerm->id)
                ->where('category', $request->category)
                ->first();
            if ($budget) {
                $budget->increment('actual_spent', $request->amount);
            }
        }

        return redirect()->back()->with('success', 'Expense recorded successfully.');
    }

    public function storeIncome(Request $request)
    {
        $request->validate([
            'category' => 'required|in:donation,grant,other',
            'amount' => 'required|numeric|min:0.01',
            'source' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $activeTerm = Term::where('is_active', true)->first();

        OtherIncome::create([
            'term_id' => $activeTerm ? $activeTerm->id : null,
            'category' => $request->category,
            'amount' => $request->amount,
            'date' => now()->format('Y-m-d'),
            'source' => $request->source,
            'description' => $request->description,
            'recorded_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Income record added successfully.');
    }

    public function budgets()
    {
        $terms = Term::all();
        $budgets = Budget::with('term')->get();
        return view('finance.budgets', compact('terms', 'budgets'));
    }

    public function storeBudget(Request $request)
    {
        $request->validate([
            'term_id' => 'required|exists:terms,id',
            'category' => 'required|string|max:255',
            'budgeted_amount' => 'required|numeric|min:0',
        ]);

        Budget::updateOrCreate(
            [
                'term_id' => $request->term_id,
                'category' => $request->category,
            ],
            ['budgeted_amount' => $request->budgeted_amount]
        );

        return redirect()->back()->with('success', 'Budget setting updated.');
    }

    public function procurement()
    {
        $requests = PurchaseRequest::with(['requester', 'approver'])->latest()->get();
        $orders = PurchaseOrder::with(['purchaseRequest', 'supplier'])->latest()->get();
        $suppliers = Supplier::all();
        return view('finance.procurement', compact('requests', 'orders', 'suppliers'));
    }

    public function storePurchaseRequest(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'estimated_cost' => 'required|numeric|min:0',
        ]);

        $pr = PurchaseRequest::create([
            'requested_by' => Auth::id(),
            'item_name' => $request->item_name,
            'quantity' => $request->quantity,
            'estimated_cost' => $request->estimated_cost,
            'status' => PurchaseRequest::STATUS_PENDING,
            'request_date' => now()->format('Y-m-d'),
        ]);

        // Notify Bursar about new purchase request
        SystemNotification::notifyRole(
            'Bursar',
            'purchase_request',
            'New Purchase Request',
            'Purchase request for ' . $pr->item_name . ' (Qty: ' . $pr->quantity . ') submitted by ' . Auth::user()->name . '.',
            '/finance/procurement'
        );

        return redirect()->back()->with('success', 'Purchase request submitted.');
    }

    public function approvePurchaseRequest($id)
    {
        $pr = PurchaseRequest::findOrFail($id);
        $pr->update([
            'status' => PurchaseRequest::STATUS_APPROVED,
            'approved_by' => Auth::id(),
            'approval_date' => now()->format('Y-m-d'),
        ]);

        // Notify requester
        if ($pr->requested_by) {
            SystemNotification::notifyUser(
                $pr->requested_by,
                'purchase_request_approved',
                'Purchase Request Approved',
                'Your purchase request for ' . $pr->item_name . ' was approved.',
                '/finance/procurement'
            );
        }

        return redirect()->back()->with('success', 'Purchase request approved.');
    }

    public function rejectPurchaseRequest($id)
    {
        $pr = PurchaseRequest::findOrFail($id);
        $pr->update([
            'status' => PurchaseRequest::STATUS_REJECTED,
            'approved_by' => Auth::id(),
            'approval_date' => now()->format('Y-m-d'),
        ]);

        // Notify requester
        if ($pr->requested_by) {
            SystemNotification::notifyUser(
                $pr->requested_by,
                'purchase_request_rejected',
                'Purchase Request Rejected',
                'Your purchase request for ' . $pr->item_name . ' was rejected.',
                '/finance/procurement'
            );
        }

        return redirect()->back()->with('success', 'Purchase request rejected.');
    }

    public function storePurchaseOrder(Request $request)
    {
        $request->validate([
            'purchase_request_id' => 'required|exists:purchase_requests,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $pr = PurchaseRequest::findOrFail($request->purchase_request_id);

        $order = PurchaseOrder::create([
            'purchase_request_id' => $pr->id,
            'supplier_id' => $request->supplier_id,
            'order_number' => 'PO-' . strtoupper(Str::random(8)),
            'order_date' => now()->format('Y-m-d'),
            'total_amount' => $request->total_amount,
            'status' => PurchaseOrder::STATUS_ORDERED,
        ]);

        $pr->update(['status' => PurchaseRequest::STATUS_ORDERED]);

        return redirect()->back()->with('success', 'Purchase Order generated. Ref: ' . $order->order_number);
    }

    public function updateOrderStatus($id, Request $request)
    {
        $order = PurchaseOrder::findOrFail($id);
        $request->validate(['status' => 'required|in:ordered,delivered,paid']);
        $order->update(['status' => $request->status]);

        // If paid, log it as an expense automatically
        if ($request->status === 'paid') {
            $activeTerm = Term::where('is_active', true)->first();

            Expense::create([
                'term_id' => $activeTerm ? $activeTerm->id : null,
                'category' => 'boarding_supplies', // default classification for POs
                'amount' => $order->total_amount,
                'date' => now()->format('Y-m-d'),
                'description' => "Paid Purchase Order {$order->order_number} for: " . $order->purchaseRequest->item_name,
                'recorded_by' => Auth::id(),
            ]);

            // Increment budget spent
            if ($activeTerm) {
                $budget = Budget::where('term_id', $activeTerm->id)
                    ->where('category', 'boarding_supplies')
                    ->first();
                if ($budget) {
                    $budget->increment('actual_spent', $order->total_amount);
                }
            }
        }

        return redirect()->back()->with('success', 'Order status updated.');
    }
}
