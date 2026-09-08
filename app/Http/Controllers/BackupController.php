<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BackupService;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    protected BackupService $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    /**
     * Display the list of backups and system backup health.
     */
    public function index()
    {
        $backups = $this->backupService->listBackups();
        $isSupabaseConfigured = $this->backupService->isSupabaseConfigured();

        $totalSize = array_sum(array_column($backups, 'size'));
        $formattedTotalSize = $this->backupService->formatBytes($totalSize);

        $dailyCount = count(array_filter($backups, fn($b) => !$b['is_milestone']));
        $milestoneCount = count(array_filter($backups, fn($b) => $b['is_milestone']));

        return view('admin.backups', compact(
            'backups',
            'isSupabaseConfigured',
            'totalSize',
            'formattedTotalSize',
            'dailyCount',
            'milestoneCount'
        ));
    }

    /**
     * Trigger immediate manual backup generation.
     */
    public function create(Request $request)
    {
        $isMilestone = $request->boolean('is_milestone', false);
        $label = $request->input('label');

        try {
            $result = $this->backupService->createBackup($isMilestone, $label);

            $msg = "Backup created successfully ({$result['formatted_size']}).";
            if ($result['cloud_uploaded']) {
                $msg .= " Uploaded to Supabase Cloud Storage.";
            } elseif ($result['cloud_error']) {
                $msg .= " (Cloud upload warning: {$result['cloud_error']})";
            }

            return redirect()->route('admin.backups.index')->with('success', $msg);
        } catch (\Throwable $e) {
            return redirect()->route('admin.backups.index')->with('error', "Backup generation failed: " . $e->getMessage());
        }
    }

    /**
     * Download a specific backup file to the user's browser.
     */
    public function download(string $filename)
    {
        $cleanName = basename($filename);
        $filePath = storage_path('app/backups/' . $cleanName);

        if (!File::exists($filePath)) {
            return redirect()->back()->with('error', 'Backup file not found.');
        }

        return response()->download($filePath, $cleanName, [
            'Content-Type' => 'application/gzip',
        ]);
    }

    /**
     * Delete a backup file.
     */
    public function destroy(string $filename)
    {
        $deleted = $this->backupService->deleteBackup($filename);

        if ($deleted) {
            return redirect()->route('admin.backups.index')->with('success', "Backup '{$filename}' deleted.");
        }

        return redirect()->route('admin.backups.index')->with('error', "Failed to delete backup '{$filename}'.");
    }
}
