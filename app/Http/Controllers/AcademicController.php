<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\SchoolClass;
use App\Models\TeacherSubject;
use App\Models\Assessment;
use App\Models\StudentResult;
use App\Models\Student;
use App\Models\Term;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AcademicController extends Controller
{
    public function teacherSubjects()
    {
        $staff = Auth::user()->staff;

        if (!$staff) {
            return redirect()->back()->with('error', 'Staff record not found.');
        }

        $subjects = $staff->teacherSubjects()
            ->with(['subject', 'schoolClass'])
            ->get();

        return view('academics.teacher_subjects', compact('subjects'));
    }

    public function assessments($teacherSubjectId)
    {
        $teacherSubject = TeacherSubject::with(['subject', 'schoolClass'])
            ->findOrFail($teacherSubjectId);

        $staff = Auth::user()->staff;

        if (!$staff || ($staff->id !== $teacherSubject->staff_id && !Auth::user()->hasRole('admin'))) {
            abort(403, 'Unauthorized');
        }

        $assessments = Assessment::where('teacher_subject_id', $teacherSubjectId)
            ->with('term')
            ->get();

        $terms = Term::all();

        return view('academics.assessments', compact('teacherSubject', 'assessments', 'terms'));
    }

    public function storeAssessment(Request $request, $teacherSubjectId)
    {
        $teacherSubject = TeacherSubject::findOrFail($teacherSubjectId);

        $staff = Auth::user()->staff;

        if (!$staff || ($staff->id !== $teacherSubject->staff_id && !Auth::user()->hasRole('admin'))) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'name'      => 'required|string|max:255',
            'term_id'   => 'required|exists:terms,id',
            'max_marks' => 'required|numeric|min:0',
            'weight'    => 'required|numeric|min:0|max:100',
        ]);

        Assessment::create([
            'teacher_subject_id' => $teacherSubjectId,
            'term_id'            => $request->term_id,
            'name'               => $request->name,
            'max_marks'          => $request->max_marks,
            'weight'             => $request->weight,
        ]);

        return redirect()->back()->with('success', 'Assessment created.');
    }

    public function enterMarks($assessmentId)
    {
        $assessment = Assessment::with(['teacherSubject.subject', 'teacherSubject.schoolClass'])
            ->findOrFail($assessmentId);

        $staff = Auth::user()->staff;

        if (!$staff || ($staff->id !== $assessment->teacherSubject->staff_id && !Auth::user()->hasRole('admin'))) {
            abort(403, 'Unauthorized');
        }

        $classId = $assessment->teacherSubject->class_id;
        $students = Student::where('class_id', $classId)->get();
        $results = StudentResult::where('assessment_id', $assessmentId)
            ->pluck('marks_obtained', 'student_id')
            ->toArray();

        return view('academics.enter_marks', compact('assessment', 'students', 'results'));
    }

    public function storeMarks(Request $request, $assessmentId)
    {
        $assessment = Assessment::findOrFail($assessmentId);

        $staff = Auth::user()->staff;

        if (!$staff || ($staff->id !== $assessment->teacherSubject->staff_id && !Auth::user()->hasRole('admin'))) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'marks'   => 'required|array',
            'marks.*' => 'nullable|numeric|min:0',
        ]);

        foreach ($request->marks as $studentId => $mark) {
            if ($mark !== null && $mark !== '') {
                if ($mark > $assessment->max_marks) {
                    return redirect()->back()
                        ->withErrors(["marks.{$studentId}" => "Marks cannot exceed {$assessment->max_marks}."]);
                }

                StudentResult::updateOrCreate(
                    ['student_id' => $studentId, 'assessment_id' => $assessmentId],
                    ['marks_obtained' => $mark]
                );
            } else {
                StudentResult::where('student_id', $studentId)
                    ->where('assessment_id', $assessmentId)
                    ->delete();
            }
        }

        // Clear cache for report cards of affected students
        $activeYear = AcademicYear::where('is_active', true)->first();
        if ($activeYear) {
            $affectedStudents = StudentResult::where('assessment_id', $assessmentId)
                ->distinct()
                ->pluck('student_id');

            foreach ($affectedStudents as $id) {
                Cache::forget("report_card_{$id}_{$activeYear->id}");
            }
        }

        return redirect()->route('academics.assessments', $assessment->teacher_subject_id)
            ->with('success', 'Marks recorded.');
    }

    public function studentReportCard($studentId)
    {
        $student = Student::with(['schoolClass'])->findOrFail($studentId);

        $staff = Auth::user()->staff;

        if (!Auth::user()->hasRole('admin')) {
            if (!$staff) {
                abort(403, 'Unauthorized');
            }

            $teachesClass = TeacherSubject::where('staff_id', $staff->id)
                ->where('class_id', $student->class_id)
                ->exists();

            if (!$teachesClass) {
                abort(403, 'Unauthorized');
            }
        }

        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            return redirect()->back()->with('error', 'No active academic year.');
        }

        $terms = Term::where('academic_year_id', $activeYear->id)->get();

        $cacheKey = "report_card_{$studentId}_{$activeYear->id}";
        $reportData = Cache::remember($cacheKey, 300, function () use ($student, $activeYear, $terms) {
            return $this->calculateReportData($student, $activeYear, $terms);
        });

        return view('academics.report_card', compact('student', 'terms', 'reportData', 'activeYear'));
    }

    /**
     * PRIVATE helper – keeps report logic inside the controller.
     * Fixes the N+1 queries with a single eager-load + one extra query for results.
     */
    private function calculateReportData($student, $activeYear, $terms)
    {
        // Get all teacher-subject records for the class with assessments
        $teacherSubjects = TeacherSubject::with(['subject', 'assessments' => function ($q) use ($terms) {
            $q->whereIn('term_id', $terms->pluck('id'));
        }])->where('class_id', $student->class_id)->get();

        // Fetch ALL student results in ONE query
        $allAssessmentIds = $teacherSubjects->flatMap->assessments->pluck('id')->unique();
        $allResults = StudentResult::where('student_id', $student->id)
            ->whereIn('assessment_id', $allAssessmentIds)
            ->get()
            ->keyBy('assessment_id');

        $reportData = [];

        foreach ($teacherSubjects as $ts) {
            $subjectName = $ts->subject->name;
            $reportData[$subjectName] = [];

            foreach ($terms as $term) {
                $assessments = $ts->assessments->where('term_id', $term->id);

                if ($assessments->isEmpty()) {
                    $reportData[$subjectName][$term->name] = 'N/A';
                    continue;
                }

                $totalWeightedScore = 0;
                $totalWeight = 0;
                $hasResult = false;

                foreach ($assessments as $ass) {
                    $result = $allResults->get($ass->id);
                    if ($result) {
                        $percentage = ($result->marks_obtained / $ass->max_marks) * 100;
                        $totalWeightedScore += $percentage * ($ass->weight / 100);
                        $totalWeight += $ass->weight;
                        $hasResult = true;
                    }
                }

                if ($hasResult && $totalWeight > 0) {
                    $final = ($totalWeightedScore / $totalWeight) * 100;
                    $reportData[$subjectName][$term->name] = round($final, 1) . '%';
                } else {
                    $reportData[$subjectName][$term->name] = '-';
                }
            }
        }

        return $reportData;
    }
}
