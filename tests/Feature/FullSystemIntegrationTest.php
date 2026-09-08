<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use App\Models\Staff;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\TeacherSubject;
use App\Models\Assessment;
use App\Models\StudentResult;
use App\Models\Term;
use App\Models\AcademicYear;
use App\Models\House;
use App\Models\Dormitory;
use App\Models\Room;
use App\Models\Bed;
use App\Models\BoardingAllocation;
use App\Models\BoardingAttendance;
use App\Models\StudentMovement;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\StudentAccount;
use App\Models\Book;
use App\Models\LibraryBorrow;
use App\Models\Inventory;
use App\Models\Timetable;
use App\Models\Announcement;
use App\Models\DisciplineRecord;
use App\Models\Department;
use App\Models\Position;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;

class FullSystemIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }
    protected function getAdminUser()
    {
        return User::whereHas('roles', fn($q) => $q->where('name', 'Super Admin'))->first()
            ?? User::factory()->create();
    }

    protected function getTeacherUser()
    {
        return User::whereHas('roles', fn($q) => $q->where('name', 'Teacher'))->first();
    }

    protected function getBoardingUser()
    {
        return User::whereHas('roles', fn($q) => $q->where('name', 'Boarding Officer'))->first();
    }

    protected function getFinanceUser()
    {
        return User::whereHas('roles', fn($q) => $q->where('name', 'Bursar'))->first();
    }

    protected function getParentUser()
    {
        return User::whereHas('roles', fn($q) => $q->where('name', 'Parent/Guardian'))->first();
    }

    public function test_landing_page_renders_cleanly(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('AIM-LIGHT');
    }

    public function test_student_admission_workflow(): void
    {
        $admin = $this->getAdminUser();
        $class = SchoolClass::first();

        $studentData = [
            'first_name' => 'Kondwani',
            'last_name' => 'Banda',
            'email' => 'kondwani.banda.' . uniqid() . '@school.com',
            'admission_number' => 'ADM-' . rand(10000, 99999),
            'class_id' => $class->id,
            'classification' => 'day',
            'guardian_name' => 'Chifundo Banda',
            'guardian_phone' => '+265991234567',
            'guardian_email' => 'chifundo@banda.mw',
        ];

        $response = $this->actingAs($admin)->post(route('students.store'), $studentData);
        $response->assertRedirect(route('students.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('students', [
            'admission_number' => $studentData['admission_number'],
            'classification' => 'day',
        ]);

        $createdStudent = Student::where('admission_number', $studentData['admission_number'])->first();
        $this->assertNotNull($createdStudent->account);
        $this->assertEquals(0, $createdStudent->account->balance);
    }

    public function test_academic_assessments_and_marks_workflow(): void
    {
        $teacher = $this->getTeacherUser();
        $staff = $teacher->staff;
        $ts = TeacherSubject::where('staff_id', $staff->id)->first() ?? TeacherSubject::first();
        $term = Term::where('is_active', true)->first() ?? Term::first();

        // 1. Create Assessment
        $assessmentResponse = $this->actingAs($teacher)->post(route('academics.assessments.store', $ts->id), [
            'name' => 'End of Term Exam ' . rand(100, 999),
            'term_id' => $term->id,
            'max_marks' => 100,
            'weight' => 50,
        ]);
        $assessmentResponse->assertRedirect();

        $assessment = Assessment::where('teacher_subject_id', $ts->id)->latest()->first();
        $this->assertNotNull($assessment);

        // 2. Enter marks for student
        $student = Student::where('class_id', $ts->class_id)->first();
        if ($student) {
            $marksResponse = $this->actingAs($teacher)->post(route('academics.marks.store', $assessment->id), [
                'marks' => [
                    $student->id => 85.5,
                ],
            ]);
            $marksResponse->assertRedirect();

            $this->assertDatabaseHas('student_results', [
                'student_id' => $student->id,
                'assessment_id' => $assessment->id,
                'marks_obtained' => 85.5,
            ]);

            // 3. View Report Card
            $reportResponse = $this->actingAs($teacher)->get(route('academics.report-card', $student->id));
            $reportResponse->assertStatus(200);
            $reportResponse->assertSee($student->full_name);
        }
    }

    public function test_boarding_bed_allocation_and_roll_call(): void
    {
        $boardingOfficer = $this->getBoardingUser();
        $student = Student::where('classification', 'boarding')->first();
        $bed = Bed::where('status', 'available')->first();

        if ($student && $bed) {
            // Allocate bed
            $allocResponse = $this->actingAs($boardingOfficer)->post(route('boarding.allocate'), [
                'student_id' => $student->id,
                'bed_id' => $bed->id,
            ]);
            $allocResponse->assertRedirect();
            $this->assertEquals(Bed::STATUS_OCCUPIED, $bed->fresh()->status);

            // Record attendance
            $attResponse = $this->actingAs($boardingOfficer)->post(route('boarding.store-attendance'), [
                'date' => now()->format('Y-m-d'),
                'roll_call_type' => 'evening',
                'status' => [
                    $student->id => 'present',
                ],
                'remarks' => [
                    $student->id => 'On time',
                ],
            ]);
            $attResponse->assertRedirect();
            $this->assertDatabaseHas('boarding_attendance', [
                'student_id' => $student->id,
                'status' => 'present',
            ]);
        }
    }

    public function test_finance_invoice_and_payment_flow(): void
    {
        $financeUser = $this->getFinanceUser();
        $student = Student::first();
        $term = Term::first();

        // 1. Generate Invoice
        $invoiceResponse = $this->actingAs($financeUser)->post(route('finance.invoices.store', $student->id), [
            'term_id' => $term->id,
            'description' => 'Tuition and Boarding Fees Term 1',
            'amount_due' => 150000.00,
        ]);
        $invoiceResponse->assertRedirect();

        $invoice = Invoice::where('student_id', $student->id)->latest('id')->first();
        $this->assertNotNull($invoice);
        $this->assertEquals(150000.00, (float) $invoice->amount_due);

        // 2. Record Partial Payment
        $paymentResponse = $this->actingAs($financeUser)->post(route('finance.payments.store'), [
            'invoice_id' => $invoice->id,
            'amount' => 100000.00,
            'payment_method' => 'bank_transfer',
        ]);
        $paymentResponse->assertRedirect();
        $this->assertEquals(Invoice::STATUS_PARTIALLY_PAID, $invoice->fresh()->status);

        // 3. Record Remaining Payment
        $paymentResponse2 = $this->actingAs($financeUser)->post(route('finance.payments.store'), [
            'invoice_id' => $invoice->id,
            'amount' => 50000.00,
            'payment_method' => 'cash',
        ]);
        $paymentResponse2->assertRedirect();
        $this->assertEquals(Invoice::STATUS_PAID, $invoice->fresh()->status);
    }

    public function test_library_book_issuance_and_returns(): void
    {
        $admin = $this->getAdminUser();
        $book = Book::where('available_copies', '>', 0)->first();
        $student = Student::first();

        if ($book && $student) {
            $initialCopies = $book->available_copies;

            $issueResponse = $this->actingAs($admin)->post(route('library.borrows.store'), [
                'book_id' => $book->id,
                'student_id' => $student->id,
                'due_at' => now()->addDays(7)->format('Y-m-d'),
            ]);
            $issueResponse->assertRedirect(route('library.borrows'));
            $this->assertEquals($initialCopies - 1, $book->fresh()->available_copies);

            $borrow = LibraryBorrow::where('book_id', $book->id)
                ->where('student_id', $student->id)
                ->where('status', 'borrowed')
                ->latest()
                ->first();

            $returnResponse = $this->actingAs($admin)->post(route('library.borrows.return', $borrow->id));
            $returnResponse->assertRedirect(route('library.borrows'));
            $this->assertEquals('returned', $borrow->fresh()->status);
            $this->assertEquals($initialCopies, $book->fresh()->available_copies);
        }
    }

    public function test_inventory_asset_validation(): void
    {
        $admin = $this->getAdminUser();

        // Valid asset
        $validResponse = $this->actingAs($admin)->post(route('inventory.store'), [
            'name' => 'Science Lab Microscope ' . rand(10, 99),
            'category' => 'laboratory',
            'total_quantity' => 10,
            'assigned_quantity' => 5,
            'status' => 'good',
            'condition_notes' => 'Tested and functional',
        ]);
        $validResponse->assertRedirect(route('inventory.index'));

        // Invalid: assigned_quantity > total_quantity
        $invalidResponse = $this->actingAs($admin)->post(route('inventory.store'), [
            'name' => 'Invalid Test Asset',
            'category' => 'other',
            'total_quantity' => 5,
            'assigned_quantity' => 10,
            'status' => 'good',
        ]);
        $invalidResponse->assertSessionHasErrors(['assigned_quantity']);
    }

    public function test_parent_portal_access(): void
    {
        $parent = $this->getParentUser();
        if ($parent) {
            $response = $this->actingAs($parent)->get(route('parent.dashboard'));
            $response->assertStatus(200);
            $response->assertSee('Parent & Guardian Portal');

            $child = $parent->children()->first();
            if ($child) {
                $childResponse = $this->actingAs($parent)->get(route('parent.child', $child->id));
                $childResponse->assertStatus(200);
                $childResponse->assertSee($child->full_name);
            }
        }
    }

    public function test_admin_backup_management_workflow(): void
    {
        $admin = $this->getAdminUser();

        // 1. Visit Backups Index
        $indexResponse = $this->actingAs($admin)->get(route('admin.backups.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Database Backups & Disaster Recovery');

        // 2. Create a Manual Backup
        $createResponse = $this->actingAs($admin)->post(route('admin.backups.create'), [
            'is_milestone' => 1,
            'label' => 'Test_Term_Snapshot',
        ]);
        $createResponse->assertRedirect(route('admin.backups.index'));
        $createResponse->assertSessionHas('success');

        // 3. Verify backup file exists
        $backupService = app(\App\Services\BackupService::class);
        $backups = $backupService->listBackups();
        $this->assertNotEmpty($backups);

        $latestBackup = $backups[0];
        $this->assertTrue($latestBackup['is_milestone']);

        // 4. Download Backup
        $downloadResponse = $this->actingAs($admin)->get(route('admin.backups.download', $latestBackup['filename']));
        $downloadResponse->assertStatus(200);
        $downloadResponse->assertHeader('content-type', 'application/gzip');

        // 5. Delete Backup
        $deleteResponse = $this->actingAs($admin)->delete(route('admin.backups.destroy', $latestBackup['filename']));
        $deleteResponse->assertRedirect(route('admin.backups.index'));
        $deleteResponse->assertSessionHas('success');
    }

    public function test_system_notifications_workflow(): void
    {
        $admin = $this->getAdminUser();

        // 1. Dispatch a test notification
        \App\Models\SystemNotification::notifyUser(
            $admin->id,
            'test_alert',
            'Term Exam Schedule Published',
            'The examination schedule for Term 1 has been finalized.',
            '/academics/subjects'
        );

        // 2. Fetch notifications via AJAX endpoint
        $response = $this->actingAs($admin)->getJson(route('notifications.index'));
        $response->assertStatus(200);
        $response->assertJsonStructure(['notifications', 'unread_count']);
        $this->assertGreaterThanOrEqual(1, $response->json('unread_count'));

        $notifications = $response->json('notifications');
        $this->assertNotEmpty($notifications);
        $firstNotifId = $notifications[0]['id'];

        // 3. Mark single notification as read
        $markReadResponse = $this->actingAs($admin)->postJson(route('notifications.read', $firstNotifId));
        $markReadResponse->assertStatus(200);
        $markReadResponse->assertJson(['ok' => true]);

        // 4. Mark all read endpoint
        $markAllResponse = $this->actingAs($admin)->postJson(route('notifications.read-all'));
        $markAllResponse->assertStatus(200);
        $markAllResponse->assertJson(['ok' => true]);

        // Verify unread count is 0
        $verifyResponse = $this->actingAs($admin)->getJson(route('notifications.index'));
        $this->assertEquals(0, $verifyResponse->json('unread_count'));
    }
}



