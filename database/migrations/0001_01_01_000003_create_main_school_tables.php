<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Departments
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Positions
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Staff
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('staff_number')->unique();
            $table->foreignId('position_id')->constrained('positions');
            $table->foreignId('department_id')->constrained('departments');
            $table->string('employment_status')->default('active');
            $table->string('attendance_status')->default('present');
            $table->text('responsibilities')->nullable();
            $table->timestamps();
        });

        // Academic Years
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // Terms
        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->string('name');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // Classes
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('level');
            $table->timestamps();
        });

        // Students
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('admission_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->foreignId('class_id')->constrained('classes');
            $table->string('classification')->default('day');
            $table->string('status')->default('active');
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->string('guardian_email')->nullable();
            $table->timestamps();
        });

        // Student Histories
        Schema::create('student_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('action');
            $table->text('details')->nullable();
            $table->timestamp('recorded_at')->useCurrent();
        });

        // Subjects
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->timestamps();
        });

        // Teacher Subjects
        Schema::create('teacher_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->timestamps();
        });

        // Assessments
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_subject_id')->constrained('teacher_subjects')->onDelete('cascade');
            $table->foreignId('term_id')->constrained('terms')->onDelete('cascade');
            $table->string('name');
            $table->decimal('max_marks', 5, 2);
            $table->decimal('weight', 5, 2);
            $table->timestamps();
        });

        // Student Results
        Schema::create('student_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('assessment_id')->constrained('assessments')->onDelete('cascade');
            $table->decimal('marks_obtained', 5, 2);
            $table->timestamps();
        });

        // Houses
        Schema::create('houses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Dormitories
        Schema::create('dormitories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('house_id')->constrained('houses')->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });

        // Rooms
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dormitory_id')->constrained('dormitories')->onDelete('cascade');
            $table->string('name');
            $table->integer('capacity');
            $table->timestamps();
        });

        // Beds
        Schema::create('beds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->string('bed_number');
            $table->string('status')->default('available');
            $table->timestamps();
        });

        // Boarding Allocations
        Schema::create('boarding_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('bed_id')->constrained('beds')->onDelete('cascade');
            $table->timestamp('allocated_at')->useCurrent();
            $table->timestamp('vacated_at')->nullable();
            $table->timestamps();
        });

        // Boarding Attendance
        Schema::create('boarding_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->date('date');
            $table->string('roll_call_type');
            $table->string('status');
            $table->string('remarks')->nullable();
            $table->timestamps();
        });

        // Student Movements
        Schema::create('student_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('leave_type');
            $table->timestamp('departure_date')->nullable();
            $table->timestamp('expected_return_date')->nullable();
            $table->timestamp('actual_return_date')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('staff');
            $table->timestamps();
        });

        // Meal Schedules
        Schema::create('meal_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_id')->constrained('terms')->onDelete('cascade');
            $table->integer('day_of_week');
            $table->string('meal_type');
            $table->string('menu_item');
            $table->time('time');
            $table->timestamps();
        });

        // Boarding Resources
        Schema::create('boarding_resources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->string('status')->default('available');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Boarding Incidents
        Schema::create('boarding_incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('incident_type');
            $table->text('details');
            $table->text('follow_up_actions')->nullable();
            $table->foreignId('reported_by')->constrained('staff');
            $table->timestamp('reported_at')->useCurrent();
            $table->timestamps();
        });

        // Fee Categories
        Schema::create('fee_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->timestamps();
        });

        // Fee Structures
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->string('classification')->default('day');
            $table->foreignId('fee_category_id')->constrained('fee_categories');
            $table->foreignId('term_id')->constrained('terms');
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });

        // Student Accounts
        Schema::create('student_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->decimal('balance', 10, 2)->default(0.00);
            $table->decimal('total_invoiced', 10, 2)->default(0.00);
            $table->decimal('total_paid', 10, 2)->default(0.00);
            $table->timestamps();
        });

        // Invoices
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('term_id')->constrained('terms');
            $table->string('description');
            $table->decimal('amount_due', 10, 2);
            $table->string('status')->default('unpaid');
            $table->timestamps();
        });

        // Payments (fee_category_id nullable)
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('set null');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('term_id')->constrained('terms');
            $table->foreignId('fee_category_id')->nullable()->constrained('fee_categories')->onDelete('set null');
            $table->string('payment_reference')->unique();
            $table->decimal('amount', 10, 2);
            $table->timestamp('payment_date');
            $table->string('payment_method');
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamps();

            $table->index(['student_id', 'term_id']);
        });

        // Other Income
        Schema::create('other_income', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_id')->nullable()->constrained('terms');
            $table->string('category');
            $table->decimal('amount', 10, 2);
            $table->date('date');
            $table->string('source');
            $table->text('description')->nullable();
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamps();
        });

        // Expenses
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_id')->nullable()->constrained('terms');
            $table->string('category');
            $table->decimal('amount', 10, 2);
            $table->date('date');
            $table->text('description')->nullable();
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamps();
        });

        // Budgets
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_id')->constrained('terms')->onDelete('cascade');
            $table->string('category');
            $table->decimal('budgeted_amount', 10, 2);
            $table->decimal('actual_spent', 10, 2)->default(0.00);
            $table->timestamps();
        });

        // Suppliers
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        // Purchase Requests
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requested_by')->constrained('users');
            $table->string('item_name');
            $table->integer('quantity');
            $table->decimal('estimated_cost', 10, 2);
            $table->date('request_date')->useCurrent();
            $table->date('approval_date')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // Purchase Orders
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')->constrained('purchase_requests')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->string('order_number')->unique();
            $table->date('order_date')->useCurrent();
            $table->decimal('total_amount', 10, 2);
            $table->string('status')->default('ordered');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('purchase_requests');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('budgets');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('other_income');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('student_accounts');
        Schema::dropIfExists('fee_structures');
        Schema::dropIfExists('fee_categories');
        Schema::dropIfExists('boarding_incidents');
        Schema::dropIfExists('boarding_resources');
        Schema::dropIfExists('meal_schedules');
        Schema::dropIfExists('student_movements');
        Schema::dropIfExists('boarding_attendance');
        Schema::dropIfExists('boarding_allocations');
        Schema::dropIfExists('beds');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('dormitories');
        Schema::dropIfExists('houses');
        Schema::dropIfExists('student_results');
        Schema::dropIfExists('assessments');
        Schema::dropIfExists('teacher_subjects');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('student_histories');
        Schema::dropIfExists('students');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('terms');
        Schema::dropIfExists('academic_years');
        Schema::dropIfExists('staff');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('departments');
    }
};