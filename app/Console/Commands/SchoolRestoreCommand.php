<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SchoolRestoreCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'school:restore {file : The backup filename or full path to restore} {--force : Force restoration without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore the school database from a local .sql or .sql.gz backup file.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $fileArg = $this->argument('file');
        $force = $this->option('force');

        // Resolve file path
        if (File::exists($fileArg)) {
            $filePath = $fileArg;
        } else {
            $filePath = storage_path('app/backups/' . $fileArg);
        }

        if (!File::exists($filePath)) {
            $this->error("Backup file not found at: {$filePath}");
            return Command::FAILURE;
        }

        if (!$force) {
            if (!$this->confirm("WARNING: This will overwrite current database tables and data with backup snapshot: '{$fileArg}'. Do you wish to proceed?", false)) {
                $this->info("Restoration cancelled.");
                return Command::SUCCESS;
            }
        }

        $this->info("Reading backup file...");
        $content = File::get($filePath);

        if (str_ends_with($filePath, '.gz')) {
            $this->info("Decompressing Gzip backup...");
            $content = gzdecode($content);
            if ($content === false) {
                $this->error("Failed to decompress Gzip backup archive.");
                return Command::FAILURE;
            }
        }

        $this->info("Executing database restoration...");

        try {
            DB::unprepared($content);
            $this->info("✓ Database restored successfully from: " . basename($filePath));
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Error during restoration: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
