<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->index(['class_id', 'status'], 'idx_students_class_status');
            $table->index(['classification', 'status'], 'idx_students_classification_status');
        });

        Schema::table('boarding_attendance', function (Blueprint $table) {
            $table->unique(['student_id', 'date', 'roll_call_type'], 'uniq_boarding_attendance_roll_call');
        });

        Schema::table('student_movements', function (Blueprint $table) {
            $table->index(['status', 'expected_return_date'], 'idx_movements_status_return');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->index(['student_id', 'term_id'], 'idx_invoices_student_term');
            $table->index('status', 'idx_invoices_status');
        });

        Schema::table('discipline_records', function (Blueprint $table) {
            $table->index(['student_id', 'incident_type'], 'idx_discipline_student_type');
        });

        Schema::table('library_borrows', function (Blueprint $table) {
            $table->index(['student_id', 'status'], 'idx_borrows_student_status');
            $table->index(['book_id', 'status'], 'idx_borrows_book_status');
        });

        Schema::table('timetables', function (Blueprint $table) {
            $table->index(['class_id', 'day_of_week'], 'idx_timetables_class_day');
        });

        Schema::table('assessments', function (Blueprint $table) {
            $table->index(['teacher_subject_id', 'term_id'], 'idx_assessments_subject_term');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropIndex('idx_assessments_subject_term');
        });

        Schema::table('timetables', function (Blueprint $table) {
            $table->dropIndex('idx_timetables_class_day');
        });

        Schema::table('library_borrows', function (Blueprint $table) {
            $table->dropIndex('idx_borrows_student_status');
            $table->dropIndex('idx_borrows_book_status');
        });

        Schema::table('discipline_records', function (Blueprint $table) {
            $table->dropIndex('idx_discipline_student_type');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('idx_invoices_student_term');
            $table->dropIndex('idx_invoices_status');
        });

        Schema::table('student_movements', function (Blueprint $table) {
            $table->dropIndex('idx_movements_status_return');
        });

        Schema::table('boarding_attendance', function (Blueprint $table) {
            $table->dropUnique('uniq_boarding_attendance_roll_call');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex('idx_students_class_status');
            $table->dropIndex('idx_students_classification_status');
        });
    }
};
