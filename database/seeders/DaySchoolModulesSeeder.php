<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Student;
use App\Models\Staff;
use App\Models\Book;
use App\Models\LibraryBorrow;
use App\Models\Inventory;
use App\Models\DisciplineRecord;
use App\Models\Timetable;
use App\Models\Announcement;

class DaySchoolModulesSeeder extends Seeder
{
    public function run(): void
    {
        // --- 1. Create Parent/Guardian role ---
        $parentRole = Role::firstOrCreate(['name' => 'Parent/Guardian', 'guard_name' => 'web']);

        // --- 2. Create 3 sample parent accounts and link to existing students ---
        $parentData = [
            ['first_name' => 'Robert',   'last_name' => 'Carter',  'email' => 'parent1@school.com'],
            ['first_name' => 'Margaret', 'last_name' => 'Banda',   'email' => 'parent2@school.com'],
            ['first_name' => 'Peter',    'last_name' => 'Mwangi',  'email' => 'parent3@school.com'],
        ];

        $students = Student::orderBy('id')->take(6)->get();
        $createdParents = [];

        foreach ($parentData as $i => $pd) {
            $user = User::firstOrCreate(['email' => $pd['email']], [
                'first_name' => $pd['first_name'],
                'last_name'  => $pd['last_name'],
                'email'      => $pd['email'],
                'password'   => Hash::make('password'),
                'uuid'       => \Illuminate\Support\Str::uuid(),
                'status'     => 'active',
            ]);
            $user->syncRoles([$parentRole]);
            $createdParents[] = $user;

            // Assign 2 students per parent
            if ($students->count() > 0) {
                $chunk = $students->slice($i * 2, 2);
                foreach ($chunk as $student) {
                    DB::table('parent_student')->insertOrIgnore([
                        'parent_id'  => $user->id,
                        'student_id' => $student->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // --- 3. Seed Library Books ---
        $books = [
            ['title' => 'Mathematics For Secondary Schools', 'author' => 'J. Okelo',       'isbn' => '978-99-10-01', 'category' => 'Mathematics', 'total_copies' => 30],
            ['title' => 'Comprehensive Biology',             'author' => 'P. Mwangi',       'isbn' => '978-99-10-02', 'category' => 'Science',      'total_copies' => 25],
            ['title' => 'English Grammar in Use',            'author' => 'Raymond Murphy',  'isbn' => '978-99-10-03', 'category' => 'Language',     'total_copies' => 20],
            ['title' => 'History of East Africa',            'author' => 'M. Kariuki',      'isbn' => '978-99-10-04', 'category' => 'Humanities',   'total_copies' => 15],
            ['title' => 'Physics Practicals S3 & S4',       'author' => 'O. Amara',        'isbn' => '978-99-10-05', 'category' => 'Science',      'total_copies' => 18],
            ['title' => 'Creative Writing & Literature',     'author' => 'A. Wanjiru',      'isbn' => '978-99-10-06', 'category' => 'Language',     'total_copies' => 10],
            ['title' => 'Accounts & Commerce S1-S4',        'author' => 'T. Ochieng',      'isbn' => '978-99-10-07', 'category' => 'Commerce',     'total_copies' => 22],
            ['title' => 'Geography of Africa',               'author' => 'H. Nyirangarama','isbn' => '978-99-10-08', 'category' => 'Humanities',   'total_copies' => 14],
        ];

        foreach ($books as $b) {
            Book::firstOrCreate(['isbn' => $b['isbn']], array_merge($b, ['available_copies' => $b['total_copies']]));
        }

        // --- 4. Seed sample borrow transactions ---
        $allStudents = Student::take(5)->get();
        $allBooks    = Book::all();

        if ($allStudents->count() && $allBooks->count()) {
            foreach ($allStudents as $i => $student) {
                $book = $allBooks[$i % $allBooks->count()];
                $due  = now()->addDays(14);

                LibraryBorrow::create([
                    'book_id'    => $book->id,
                    'student_id' => $student->id,
                    'borrowed_at'=> now()->subDays(3),
                    'due_at'     => $due,
                    'status'     => 'borrowed',
                ]);
                $book->decrement('available_copies');
            }
        }

        // --- 5. Seed Asset Inventory ---
        $assets = [
            ['name' => 'Student Desks',          'category' => 'furniture',  'total_quantity' => 200, 'assigned_quantity' => 195, 'status' => 'good'],
            ['name' => 'Teacher Chairs',          'category' => 'furniture',  'total_quantity' => 30,  'assigned_quantity' => 28,  'status' => 'good'],
            ['name' => 'Desktop Computers (Lab)', 'category' => 'computers',  'total_quantity' => 40,  'assigned_quantity' => 38,  'status' => 'good'],
            ['name' => 'Projectors',              'category' => 'computers',  'total_quantity' => 10,  'assigned_quantity' => 8,   'status' => 'good'],
            ['name' => 'Chemistry Reagent Sets',  'category' => 'lab',        'total_quantity' => 25,  'assigned_quantity' => 20,  'status' => 'good'],
            ['name' => 'Football Sets',           'category' => 'sports',     'total_quantity' => 12,  'assigned_quantity' => 10,  'status' => 'good'],
            ['name' => 'Dormitory Beds',          'category' => 'boarding',   'total_quantity' => 150, 'assigned_quantity' => 140, 'status' => 'good'],
            ['name' => 'Mattresses',              'category' => 'boarding',   'total_quantity' => 150, 'assigned_quantity' => 138, 'status' => 'good', 'condition_notes' => '12 need replacement'],
            ['name' => 'Generators',              'category' => 'other',      'total_quantity' => 2,   'assigned_quantity' => 2,   'status' => 'good'],
            ['name' => 'Old Physics Equipment',   'category' => 'lab',        'total_quantity' => 8,   'assigned_quantity' => 8,   'status' => 'damaged', 'condition_notes' => 'Scheduled for repair'],
        ];

        foreach ($assets as $asset) {
            Inventory::create($asset);
        }

        // --- 6. Seed Discipline Records ---
        $staff = Staff::first();
        if ($staff && $allStudents->count()) {
            $incidents = [
                ['type' => 'behavior',  'details' => 'Student was found using a mobile phone during class hours.', 'action' => 'Verbal warning issued. Phone confiscated until end of term.'],
                ['type' => 'attendance','details' => 'Student absent 5 consecutive days without explanation.', 'action' => 'Parents contacted by phone. Written apology submitted.'],
                ['type' => 'academic',  'details' => 'Student caught copying during CAT examination.', 'action' => 'Paper marked as zero. Parents informed. Retake not permitted.'],
            ];

            foreach ($incidents as $i => $incident) {
                $student = $allStudents[$i % $allStudents->count()];
                DisciplineRecord::create([
                    'student_id'      => $student->id,
                    'incident_type'   => $incident['type'],
                    'details'         => $incident['details'],
                    'action_taken'    => $incident['action'],
                    'warnings_issued' => $i === 0 ? 1 : 0,
                    'recorded_by'     => $staff->id,
                ]);
            }
        }

        // --- 7. Seed Timetable (Class schedules for Form 1) ---
        $class   = \App\Models\SchoolClass::first();
        $subject = \App\Models\Subject::first();
        if ($class && $subject && $staff) {
            $slots = [
                ['day' => 1, 'start' => '07:30', 'end' => '08:15', 'room' => 'Room A1'],
                ['day' => 1, 'start' => '08:15', 'end' => '09:00', 'room' => 'Room A1'],
                ['day' => 2, 'start' => '07:30', 'end' => '08:15', 'room' => 'Lab 1'],
                ['day' => 3, 'start' => '10:00', 'end' => '10:45', 'room' => 'Room B2'],
                ['day' => 4, 'start' => '11:00', 'end' => '11:45', 'room' => 'Room A1'],
                ['day' => 5, 'start' => '07:30', 'end' => '08:15', 'room' => 'Room C3'],
            ];

            $subjects = \App\Models\Subject::take(4)->get();
            foreach ($slots as $i => $slot) {
                $sub = $subjects[$i % $subjects->count()];
                Timetable::create([
                    'class_id'       => $class->id,
                    'subject_id'     => $sub->id,
                    'staff_id'       => $staff->id,
                    'day_of_week'    => $slot['day'],
                    'start_time'     => $slot['start'],
                    'end_time'       => $slot['end'],
                    'timetable_type' => 'class',
                    'room_name'      => $slot['room'],
                ]);
            }
        }

        // --- 8. Seed Announcements ---
        $admin = User::role('Super Admin')->first();
        if ($admin) {
            $notices = [
                ['title' => 'End of Term Examinations Schedule', 'content' => 'End of term examinations will commence on the 15th of September. All students must ensure they have cleared their fees before sitting exams.', 'audience' => 'all'],
                ['title' => 'Parents Meeting — Term 2 Reports', 'content' => 'Parents and guardians are cordially invited to the school for the Term 2 academic report collection on Saturday, 23rd August 2026. Time: 9:00 AM — 1:00 PM.', 'audience' => 'parents'],
                ['title' => 'Staff CPD Training This Friday', 'content' => 'All teaching staff are reminded of the Continuous Professional Development training session scheduled for Friday afternoon in the conference room.', 'audience' => 'teachers'],
                ['title' => 'Sports Gala Registration Open', 'content' => 'Students interested in representing the school in the inter-school athletics gala should register with the Head of Sports by end of this week.', 'audience' => 'students'],
            ];

            foreach ($notices as $notice) {
                Announcement::create([
                    'title'           => $notice['title'],
                    'content'         => $notice['content'],
                    'target_audience' => $notice['audience'],
                    'created_by'      => $admin->id,
                    'expires_at'      => now()->addMonths(2),
                ]);
            }
        }
    }
}
