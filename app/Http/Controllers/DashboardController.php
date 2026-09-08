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
use App\Models\DisciplineRecord;
use App\Models\Announcement;
use App\Models\Subject;
use App\Models\Timetable;
use App\Models\Assessment;
use App\Models\TeacherSubject;

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
            case 'parent':
                return redirect()->route('parent.dashboard');
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
            'recent_students' => Student::with('schoolClass')->latest()->take(5)->get(),
            'recent_staff' => Staff::with(['user', 'position', 'department'])->latest()->take(5)->get(),
        ];
        return view('dashboards.admin', $data);
    }

    private function headTeacherDashboard()
    {
        $data = [
            'total_students'      => Student::count(),
            'boarding_students'   => Student::where('classification', Student::CLASSIFICATION_BOARDING)->count(),
            'day_scholars'        => Student::where('classification', Student::CLASSIFICATION_DAY)->count(),
            'total_staff'         => Staff::count(),
            'total_classes'       => SchoolClass::count(),
            'total_subjects'      => Subject::count(),
            'total_beds'          => Bed::count(),
            'occupied_beds'       => Bed::where('status', Bed::STATUS_OCCUPIED)->count(),
            'pending_leaves'      => StudentMovement::where('status', StudentMovement::STATUS_PENDING)->count(),
            'pending_purchases'   => PurchaseRequest::where('status', PurchaseRequest::STATUS_PENDING)->count(),
            'pending_purchases_cost' => PurchaseRequest::where('status', PurchaseRequest::STATUS_PENDING)->sum('estimated_cost'),
            'total_discipline'    => DisciplineRecord::count(),
            'behavior_incidents'  => DisciplineRecord::where('incident_type', 'behavior')->count(),
            'academic_incidents'  => DisciplineRecord::where('incident_type', 'academic')->count(),
            'attendance_incidents'=> DisciplineRecord::where('incident_type', 'attendance')->count(),
            'recent_leaves'       => StudentMovement::with(['student.schoolClass'])
                ->where('status', StudentMovement::STATUS_PENDING)
                ->latest()
                ->take(5)
                ->get(),
            'recent_purchases'    => PurchaseRequest::with('requester')
                ->where('status', PurchaseRequest::STATUS_PENDING)
                ->latest()
                ->take(5)
                ->get(),
            'recent_students'     => Student::with('schoolClass')->latest()->take(5)->get(),
            'recent_staff'        => Staff::with(['user', 'position', 'department'])->latest()->take(5)->get(),
            'recent_incidents'    => DisciplineRecord::with(['student.schoolClass', 'recorder.user'])->latest()->take(5)->get(),
            'recent_announcements'=> Announcement::with('author')->latest()->take(4)->get(),
            'classes'             => SchoolClass::withCount('students')->get(),
        ];
        return view('dashboards.head_teacher', $data);
    }

    private function teacherDashboard()
    {
        $staff = Auth::user()->staff;

        $mySubjects = $staff ? $staff->teacherSubjects()
            ->with(['subject', 'schoolClass.students', 'assessments'])
            ->get() : collect();

        $classIds = $mySubjects->pluck('class_id')->unique()->filter();
        $mySubjectIds = $mySubjects->pluck('id')->unique()->filter();

        $myStudentsCount = $classIds->isNotEmpty() 
            ? Student::whereIn('class_id', $classIds)->count() 
            : 0;

        $recentAssessments = $mySubjectIds->isNotEmpty()
            ? Assessment::whereIn('teacher_subject_id', $mySubjectIds)
                ->with(['teacherSubject.subject', 'teacherSubject.schoolClass', 'term'])
                ->latest()
                ->take(5)
                ->get()
            : collect();

        $totalAssessmentsCount = $mySubjectIds->isNotEmpty()
            ? Assessment::whereIn('teacher_subject_id', $mySubjectIds)->count()
            : 0;

        $todayDayOfWeek = (int) date('N'); // 1 (Monday) to 7 (Sunday)
        $myTimetables = $staff
            ? Timetable::where('staff_id', $staff->id)
                ->with(['schoolClass', 'subject'])
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get()
            : collect();

        $timetableToday = $myTimetables->where('day_of_week', $todayDayOfWeek);

        $recentAnnouncements = Announcement::whereIn('target_audience', ['all', 'teachers'])
            ->with('author')
            ->latest()
            ->take(4)
            ->get();

        $recentDiscipline = $staff
            ? DisciplineRecord::where('recorded_by', $staff->id)
                ->with('student.schoolClass')
                ->latest()
                ->take(4)
                ->get()
            : collect();

        $data = [
            'staff'                 => $staff,
            'classes_count'         => $mySubjects->pluck('class_id')->unique()->count(),
            'subjects_count'        => $mySubjects->pluck('subject_id')->unique()->count(),
            'my_subjects'           => $mySubjects,
            'my_students_count'     => $myStudentsCount,
            'total_students'        => Student::count(),
            'total_assessments'     => $totalAssessmentsCount,
            'recent_assessments'    => $recentAssessments,
            'timetable_today'       => $timetableToday,
            'my_timetables'         => $myTimetables,
            'recent_announcements'  => $recentAnnouncements,
            'recent_discipline'     => $recentDiscipline,
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
