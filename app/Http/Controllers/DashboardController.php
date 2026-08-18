<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Staff;
use App\Models\SchoolClass;
use App\Models\Bed;
use App\Models\BoardingAllocation;
use App\Models\BoardingAttendance;
use App\Models\StudentMovement;
use App\Models\BoardingIncident;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\PurchaseRequest;
use App\Models\OtherIncome;
use App\Models\StudentAccount;
use App\Models\StudentResult;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Get the role slug version
        $roleName = $user->getRoleSlug();

        switch ($roleName) {
            case 'admin':
                return $this->adminDashboard();
            case 'head-teacher':
                return $this->headTeacherDashboard();
            case 'teacher':
                return $this->teacherDashboard();
            case 'boarding-officer':
            case 'warden-matron':
                return $this->boardingDashboard();
            case 'bursar':
            case 'accountant':
                return $this->financeDashboard();
            case 'procurement-officer':
                return $this->procurementDashboard();
            case 'auditor':
                return $this->auditorDashboard();
            default:
                return $this->studentDashboard($user);
        }
    }

    private function adminDashboard()
    {
        $data = [
            'total_students' => Student::count(),
            'boarding_students' => Student::where('classification', Student::CLASSIFICATION_BOARDING)->count(),
            'day_scholars' => Student::where('classification', Student::CLASSIFICATION_DAY)->count(),
            'total_staff' => Staff::count(),
            'total_classes' => SchoolClass::count(),
            'total_beds' => Bed::count(),
            'occupied_beds' => Bed::where('status', Bed::STATUS_OCCUPIED)->count(),
            'total_invoiced' => Invoice::sum('amount_due'),
            'total_collected' => Payment::sum('amount'),
            'total_expenses' => Expense::sum('amount'),
            'recent_students' => Student::latest()->take(5)->get(),
            'recent_staff' => Staff::with(['user', 'position', 'department'])->latest()->take(5)->get(),
        ];
        return view('dashboards.admin', $data);
    }

    private function headTeacherDashboard()
    {
        $data = [
            'total_students' => Student::count(),
            'total_staff' => Staff::count(),
            'pending_leaves' => StudentMovement::where('status', StudentMovement::STATUS_PENDING)->count(),
            'pending_purchases' => PurchaseRequest::where('status', PurchaseRequest::STATUS_PENDING)->count(),
            'total_invoiced' => Invoice::sum('amount_due'),
            'total_collected' => Payment::sum('amount'),
            'total_expenses' => Expense::sum('amount'),
            'recent_leaves' => StudentMovement::with('student')
                ->where('status', StudentMovement::STATUS_PENDING)
                ->latest()
                ->take(5)
                ->get(),
            'recent_purchases' => PurchaseRequest::with('requester')
                ->where('status', PurchaseRequest::STATUS_PENDING)
                ->latest()
                ->take(5)
                ->get(),
        ];
        return view('dashboards.head_teacher', $data);
    }

    private function teacherDashboard()
    {
        $staff = Auth::user()->staff;
        $data = [
            'classes_count' => $staff ? $staff->teacherSubjects()->distinct('class_id')->count('class_id') : 0,
            'subjects_count' => $staff ? $staff->teacherSubjects()->distinct('subject_id')->count('subject_id') : 0,
            'my_subjects' => $staff ? $staff->teacherSubjects()->with(['subject', 'schoolClass'])->get() : collect(),
            'total_students' => Student::count(),
        ];
        return view('dashboards.teacher', $data);
    }

    private function boardingDashboard()
    {
        $totalBeds = Bed::count();
        $occupiedBeds = Bed::where('status', Bed::STATUS_OCCUPIED)->count();
        $vacantBeds = $totalBeds - $occupiedBeds;
        $occupancyRate = $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100) : 0;

        $data = [
            'total_beds' => $totalBeds,
            'occupied_beds' => $occupiedBeds,
            'vacant_beds' => $vacantBeds,
            'occupancy_rate' => $occupancyRate,
            'active_leaves' => StudentMovement::where('status', StudentMovement::STATUS_DEPARTED)->count(),
            'overdue_leaves' => StudentMovement::where('status', StudentMovement::STATUS_DEPARTED)
                ->where('expected_return_date', '<', now())
                ->count(),
            'recent_incidents' => BoardingIncident::with(['student', 'reporter.user'])
                ->latest('reported_at')
                ->take(5)
                ->get(),
            'active_movements' => StudentMovement::with(['student', 'approver.user'])
                ->whereIn('status', [
                    StudentMovement::STATUS_PENDING,
                    StudentMovement::STATUS_APPROVED,
                    StudentMovement::STATUS_DEPARTED,
                ])
                ->latest()
                ->take(5)
                ->get(),
        ];
        return view('dashboards.boarding', $data);
    }

    private function financeDashboard()
    {
        $totalInvoiced = Invoice::sum('amount_due');
        $totalCollected = Payment::sum('amount');
        $totalOtherIncome = OtherIncome::sum('amount');
        $totalExpenses = Expense::sum('amount');

        $netPosition = ($totalCollected + $totalOtherIncome) - $totalExpenses;
        $collectionRate = $totalInvoiced > 0 ? round(($totalCollected / $totalInvoiced) * 100) : 0;

        $data = [
            'total_invoiced' => $totalInvoiced,
            'total_collected' => $totalCollected,
            'total_other_income' => $totalOtherIncome,
            'total_expenses' => $totalExpenses,
            'net_position' => $netPosition,
            'collection_rate' => $collectionRate,
            'recent_payments' => Payment::with(['student', 'recorder'])->latest()->take(5)->get(),
            'recent_expenses' => Expense::with('recorder')->latest()->take(5)->get(),
            'budgets' => Budget::all(),
        ];
        return view('dashboards.finance', $data);
    }

    private function procurementDashboard()
    {
        $data = [
            'total_requests' => PurchaseRequest::count(),
            'pending_requests' => PurchaseRequest::where('status', PurchaseRequest::STATUS_PENDING)->count(),
            'approved_requests' => PurchaseRequest::where('status', PurchaseRequest::STATUS_APPROVED)->count(),
            'ordered_requests' => PurchaseRequest::where('status', PurchaseRequest::STATUS_ORDERED)->count(),
            'recent_requests' => PurchaseRequest::with(['requester', 'approver'])->latest()->take(10)->get(),
        ];
        return view('dashboards.procurement', $data);
    }

    public function auditorDashboard()
    {
        $data = [
            'total_invoiced' => Invoice::sum('amount_due'),
            'total_collected' => Payment::sum('amount'),
            'total_expenses' => Expense::sum('amount'),
            'payments_log' => Payment::with(['student', 'recorder'])->latest()->take(15)->get(),
            'expenses_log' => Expense::with('recorder')->latest()->take(15)->get(),
            'budgets' => Budget::all(),
        ];
        return view('dashboards.auditor', $data);
    }

    private function studentDashboard($user)
    {
        $student = Student::where('user_id', $user->id)->first();
        if (!$student) {
            $class = SchoolClass::first();
            $student = Student::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'admission_number' => 'ADM-' . strtoupper(substr(md5($user->id), 0, 6)),
                    'first_name' => $user->first_name ?: 'Student',
                    'last_name' => $user->last_name ?: 'User',
                    'class_id' => $class ? $class->id : 1,
                    'classification' => 'boarding',
                    'status' => 'active',
                ]
            );
        }

        $allocation = BoardingAllocation::with(['bed.room.dormitory.house'])
            ->where('student_id', $student->id)
            ->whereNull('vacated_at')
            ->first();

        $account = StudentAccount::where('student_id', $student->id)->first();

        $results = StudentResult::with(['assessment.teacherSubject.subject'])
            ->where('student_id', $student->id)
            ->latest()
            ->take(10)
            ->get();

        $data = [
            'student' => $student,
            'allocation' => $allocation,
            'account' => $account,
            'results' => $results,
            'recent_movements' => StudentMovement::where('student_id', $student->id)->latest()->take(5)->get(),
        ];
        return view('dashboards.student', $data);
    }
}
