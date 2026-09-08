<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop boarding resources and create general school assets inventory
        Schema::dropIfExists('boarding_resources');

        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category'); // e.g. furniture, lab, computers, sports, boarding, other
            $table->integer('total_quantity')->default(1);
            $table->integer('assigned_quantity')->default(0);
            $table->string('status')->default('good'); // good, damaged, need_replacement
            $table->text('condition_notes')->nullable();
            $table->timestamps();
        });

        // 2. Parent-Student Link
        Schema::create('parent_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['parent_id', 'student_id']);
        });

        // 3. Discipline Records
        Schema::create('discipline_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('incident_type'); // e.g. academic, behavior, attendance
            $table->text('details');
            $table->text('action_taken');
            $table->integer('warnings_issued')->default(0);
            $table->foreignId('recorded_by')->constrained('staff')->onDelete('cascade');
            $table->timestamps();
        });

        // 4. Timetables (Class and Exam schedules)
        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->integer('day_of_week'); // 0 = Sunday, 1 = Monday, etc.
            $table->time('start_time');
            $table->time('end_time');
            $table->string('timetable_type')->default('class'); // class, exam
            $table->string('room_name')->nullable();
            $table->timestamps();
        });

        // 5. Library Books
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author');
            $table->string('isbn')->unique()->nullable();
            $table->string('category'); // e.g. Science, Fiction, Mathematics
            $table->integer('total_copies')->default(1);
            $table->integer('available_copies')->default(1);
            $table->timestamps();
        });

        // 6. Library Borrowing Transactions
        Schema::create('library_borrows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->timestamp('borrowed_at')->useCurrent();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->decimal('fine_amount', 8, 2)->default(0.00);
            $table->string('status')->default('borrowed'); // borrowed, returned, overdue
            $table->timestamps();
        });

        // 7. Communication Announcements
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('target_audience')->default('all'); // all, parents, teachers, students
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('library_borrows');
        Schema::dropIfExists('books');
        Schema::dropIfExists('timetables');
        Schema::dropIfExists('discipline_records');
        Schema::dropIfExists('parent_student');
        Schema::dropIfExists('inventory');

        // Recreate boarding resources
        Schema::create('boarding_resources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->string('status')->default('available');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
};
