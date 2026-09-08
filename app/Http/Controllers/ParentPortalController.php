<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Announcement;

class ParentPortalController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Load children linked via parent_student pivot
        $children = $user->children()->with([
            'schoolClass',
            'account',
            'results.assessment.teacherSubject.subject',
            'activeAllocation.bed.room.dormitory.house',
        ])->get();

        // Load school announcements visible to parents or all
        $announcements = Announcement::whereIn('target_audience', ['all', 'parents'])
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->latest()
            ->take(5)
            ->get();

        return view('parent.dashboard', compact('children', 'announcements'));
    }

    public function childProfile(int $studentId)
    {
        $user = Auth::user();

        // Ensure parent owns this student link
        $student = $user->children()->with([
            'schoolClass',
            'account',
            'results.assessment.teacherSubject.subject',
            'results.assessment.term',
            'movements' => fn($q) => $q->latest()->take(5),
            'disciplineRecords' => fn($q) => $q->latest()->take(5),
            'libraryBorrows.book',
        ])->findOrFail($studentId);

        return view('parent.child_profile', compact('student'));
    }
}
