<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BackupService;

class SchoolBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'school:backup 
                            {--milestone : Flag this backup as an exempt permanent term milestone}
                            {--label= : Optional descriptive label for this backup}
                            {--prune-days=60 : Number of days to retain daily backups}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a compressed database backup, upload to Supabase Cloud, and prune old snapshots.';

    /**
     * Execute the console command.
     */
    public function handle(BackupService $backupService): int
    {
        $isMilestone = $this->option('milestone');
        $label = $this->option('label');
        $pruneDays = (int) $this->option('prune-days');

        $this->info("Starting AIM-LIGHT School Database Backup...");
        $this->info("Mode: " . ($isMilestone ? "Term Milestone (Permanent)" : "Daily Snapshot"));

        $result = $backupService->createBackup($isMilestone, $label);

        $this->info("✓ Local Backup created: {$result['filename']} ({$result['formatted_size']})");

        if ($backupService->isSupabaseConfigured()) {
            if ($result['cloud_uploaded']) {
                $this->info("✓ Uploaded to Supabase Cloud Storage bucket successfully.");
            } else {
                $this->warn("⚠ Supabase upload failed. Local backup remains safe. Error: " . ($result['cloud_error'] ?? 'Unknown error'));
            }
        } else {
            $this->line("ℹ Supabase not configured in .env; skipping cloud sync.");
        }

        // Pruning
        if ($pruneDays > 0) {
            $this->info("Pruning daily backups older than {$pruneDays} days...");
            $deleted = $backupService->pruneBackups($pruneDays);
            if (count($deleted) > 0) {
                $this->info("✓ Pruned " . count($deleted) . " expired backups: " . implode(', ', $deleted));
            } else {
                $this->info("✓ No expired backups found.");
            }
        }

        $this->info("Database backup workflow completed successfully!");

        return Command::SUCCESS;
    }
}
