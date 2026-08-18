<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AcademicController;
use App\Http\Controllers\BoardingController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentStaffController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// One-Click Railway Setup Helper
Route::get('/deploy-setup', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        return response()->json([
            'status' => 'success',
            'message' => 'Database migrated and seeded successfully!',
            'output' => \Illuminate\Support\Facades\Artisan::output(),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
});

// Public Homepage
Route::get('/', function () {
    if (Auth::check()) {
        return app(DashboardController::class)->index();
    }
    return view('welcome');
})->name('home');

// Auth Routes (Guests Only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Profile Routes
    Route::get('/profile/settings', [ProfileController::class, 'showSettings'])->name('profile.settings');
    Route::put('/profile/settings', [ProfileController::class, 'updateSettings'])->name('profile.settings.update');

    // Notification Routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    // Main Dashboard (fallback)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Head Teacher Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,head-teacher'])->prefix('headteacher')->name('headteacher.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Teacher Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,teacher,head-teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Boarding Staff Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,boarding-officer,warden-matron'])->prefix('boarding')->name('boarding.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Finance Staff Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,bursar,accountant'])->prefix('finance')->name('finance.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Procurement Staff Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,procurement-officer'])->prefix('procurement')->name('procurement.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Auditor Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,auditor'])->prefix('auditor')->name('auditor.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Academic Routes (Teacher / Admin / Head Teacher)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,teacher,head-teacher'])->prefix('academics')->name('academics.')->group(function () {
    Route::get('/teacher-subjects', [AcademicController::class, 'teacherSubjects'])->name('teacher-subjects');
    Route::get('/teacher-subjects/{teacherSubjectId}/assessments', [AcademicController::class, 'assessments'])->name('assessments');
    Route::post('/teacher-subjects/{teacherSubjectId}/assessments', [AcademicController::class, 'storeAssessment'])->name('assessments.store');
    Route::get('/assessments/{assessmentId}/marks', [AcademicController::class, 'enterMarks'])->name('marks');
    Route::post('/assessments/{assessmentId}/marks', [AcademicController::class, 'storeMarks'])->name('marks.store');
    Route::get('/students/{studentId}/report-card', [AcademicController::class, 'studentReportCard'])->name('report-card');
});

/*
|--------------------------------------------------------------------------
| Boarding Management Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,head-teacher,boarding-officer,warden-matron'])->prefix('boarding')->name('boarding.')->group(function () {
    Route::get('/rooms', [BoardingController::class, 'rooms'])->name('rooms');
    Route::post('/allocate-bed', [BoardingController::class, 'allocateBed'])->name('allocate');
    Route::post('/vacate-bed/{allocationId}', [BoardingController::class, 'vacateBed'])->name('vacate');
    Route::get('/attendance', [BoardingController::class, 'attendance'])->name('attendance');
    Route::post('/attendance', [BoardingController::class, 'storeAttendance'])->name('store-attendance');
    Route::get('/movements', [BoardingController::class, 'movements'])->name('movements');
    Route::post('/movements', [BoardingController::class, 'storeMovement'])->name('movements.store');
    Route::post('/movements/{id}/approve', [BoardingController::class, 'approveMovement'])->name('movements.approve');
    Route::post('/movements/{id}/depart', [BoardingController::class, 'departMovement'])->name('movements.depart');
    Route::post('/movements/{id}/return', [BoardingController::class, 'returnMovement'])->name('movements.return');
    Route::get('/meals', [BoardingController::class, 'meals'])->name('meals');
    Route::post('/meals', [BoardingController::class, 'storeMeal'])->name('meals.store');
    Route::get('/incidents', [BoardingController::class, 'incidents'])->name('incidents');
    Route::post('/incidents', [BoardingController::class, 'storeIncident'])->name('incidents.store');
    Route::get('/resources', [BoardingController::class, 'resources'])->name('resources');
    Route::post('/resources', [BoardingController::class, 'storeResource'])->name('resources.store');
});

/*
|--------------------------------------------------------------------------
| Finance Management Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,bursar,accountant'])->prefix('finance')->name('finance.')->group(function () {
    Route::get('/fee-structures', [FinanceController::class, 'feeStructures'])->name('fee-structures');
    Route::post('/fee-structures', [FinanceController::class, 'storeFeeStructure'])->name('fee-structures.store');
    Route::get('/accounts', [FinanceController::class, 'studentAccounts'])->name('accounts');
    Route::get('/students/{studentId}/invoices', [FinanceController::class, 'invoices'])->name('invoices');
    Route::post('/students/{studentId}/invoices', [FinanceController::class, 'storeInvoice'])->name('invoices.store');
    Route::post('/payments', [FinanceController::class, 'storePayment'])->name('payments.store');
    Route::get('/expenses', [FinanceController::class, 'expenses'])->name('expenses');
    Route::post('/expenses', [FinanceController::class, 'storeExpense'])->name('expenses.store');
    Route::post('/income', [FinanceController::class, 'storeIncome'])->name('income.store');
    Route::get('/budgets', [FinanceController::class, 'budgets'])->name('budgets');
    Route::post('/budgets', [FinanceController::class, 'storeBudget'])->name('budgets.store');
});

/*
|--------------------------------------------------------------------------
| Shared Procurement Routes (Finance Staff & Procurement Officers)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,bursar,accountant,procurement-officer'])->prefix('finance')->name('finance.')->group(function () {
    Route::get('/procurement', [FinanceController::class, 'procurement'])->name('procurement');
    Route::post('/procurement/requests', [FinanceController::class, 'storePurchaseRequest'])->name('procurement.request.store');
    Route::post('/procurement/requests/{id}/approve', [FinanceController::class, 'approvePurchaseRequest'])->name('procurement.request.approve');
    Route::post('/procurement/requests/{id}/reject', [FinanceController::class, 'rejectPurchaseRequest'])->name('procurement.request.reject');
    Route::post('/procurement/orders', [FinanceController::class, 'storePurchaseOrder'])->name('procurement.order.store');
    Route::post('/procurement/orders/{id}/status', [FinanceController::class, 'updateOrderStatus'])->name('procurement.order.status');
});

/*
|--------------------------------------------------------------------------
| Procurement Staff Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,procurement-officer'])->prefix('procurement')->name('procurement.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Auditor Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,auditor'])->prefix('auditor')->name('auditor.')->group(function () {
    Route::get('/logs', [DashboardController::class, 'auditorDashboard'])->name('logs');
});

/*
|--------------------------------------------------------------------------
| Student & Staff Management Routes (Admin, Head Teacher)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,head-teacher'])->prefix('management')->group(function () {
    // Students
    Route::get('/students', [StudentStaffController::class, 'students'])->name('students.index');
    Route::get('/students/create', [StudentStaffController::class, 'createStudent'])->name('students.create');
    Route::post('/students', [StudentStaffController::class, 'storeStudent'])->name('students.store');
    Route::get('/students/{student}', [StudentStaffController::class, 'showStudent'])->name('students.show');
    Route::get('/students/{student}/edit', [StudentStaffController::class, 'editStudent'])->name('students.edit');
    Route::put('/students/{student}', [StudentStaffController::class, 'updateStudent'])->name('students.update');
    Route::delete('/students/{student}', [StudentStaffController::class, 'destroyStudent'])->name('students.destroy');

    // Staff
    Route::get('/staff', [StudentStaffController::class, 'staff'])->name('staff.index');
    Route::get('/staff/create', [StudentStaffController::class, 'createStaff'])->name('staff.create');
    Route::post('/staff', [StudentStaffController::class, 'storeStaff'])->name('staff.store');
    Route::get('/staff/{staff}', [StudentStaffController::class, 'showStaff'])->name('staff.show');
    Route::get('/staff/{staff}/edit', [StudentStaffController::class, 'editStaff'])->name('staff.edit');
    Route::put('/staff/{staff}', [StudentStaffController::class, 'updateStaff'])->name('staff.update');
    Route::delete('/staff/{staff}', [StudentStaffController::class, 'destroyStaff'])->name('staff.destroy');
});

/*
|--------------------------------------------------------------------------
| Notification Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::post('/{notification}/read', [NotificationController::class, 'markRead'])->name('read');
    Route::post('/read-all', [NotificationController::class, 'markAllRead'])->name('read-all');
});
