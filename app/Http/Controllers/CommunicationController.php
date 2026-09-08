<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;

class CommunicationController extends Controller
{
    public function announcements(Request $request)
    {
        $query = Announcement::with('author')->latest();

        if ($request->filled('target_audience')) {
            $query->where('target_audience', $request->target_audience);
        }

        $announcements = $query->paginate(20);

        return view('communication.announcements', compact('announcements'));
    }

    public function storeAnnouncement(Request $request)
    {
        $request->validate([
            'title'           => 'required|string|max:255',
            'content'         => 'required|string',
            'target_audience' => 'required|in:all,parents,teachers,students',
            'expires_at'      => 'nullable|date|after:today',
        ]);

        Announcement::create([
            'title'           => $request->title,
            'content'         => $request->content,
            'target_audience' => $request->target_audience,
            'created_by'      => Auth::id(),
            'expires_at'      => $request->expires_at,
        ]);

        return redirect()->route('communication.announcements')
            ->with('success', 'Announcement published successfully.');
    }

    public function deleteAnnouncement(int $id)
    {
        Announcement::findOrFail($id)->delete();

        return redirect()->route('communication.announcements')
            ->with('success', 'Announcement removed.');
    }
}
