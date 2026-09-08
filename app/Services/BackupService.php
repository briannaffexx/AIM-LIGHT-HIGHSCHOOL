<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PDO;

class BackupService
{
    protected string $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');
        if (!File::exists($this->backupDir)) {
            File::makeDirectory($this->backupDir, 0755, true);
        }
    }

    /**
     * Create a compressed database backup.
     *
     * @param bool $isMilestone
     * @param string|null $customLabel
     * @return array
     */
    public function createBackup(bool $isMilestone = false, ?string $customLabel = null): array
    {
        $timestamp = now()->format('Y-m-d_His');
        $prefix = $isMilestone ? 'milestone_' : 'daily_';
        if ($customLabel) {
            $labelClean = preg_replace('/[^a-zA-Z0-9_-]/', '', str_replace(' ', '_', $customLabel));
            $filename = "{$prefix}{$labelClean}_{$timestamp}.sql.gz";
        } else {
            $filename = "{$prefix}backup_{$timestamp}.sql.gz";
        }

        $filePath = $this->backupDir . DIRECTORY_SEPARATOR . $filename;

        // 1. Generate SQL Dump
        $sqlContent = $this->generateSqlDump();

        // 2. Compress and Save
        $compressed = gzencode($sqlContent, 9);
        File::put($filePath, $compressed);

        $fileSize = File::size($filePath);

        // 3. Upload to Supabase if configured
        $cloudUploaded = false;
        $cloudError = null;

        if ($this->isSupabaseConfigured()) {
            try {
                $cloudUploaded = $this->uploadToSupabase($filePath, $filename);
            } catch (\Throwable $e) {
                $cloudError = $e->getMessage();
                Log::error("Supabase Backup Upload Error: " . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'filename' => $filename,
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'formatted_size' => $this->formatBytes($fileSize),
            'is_milestone' => $isMilestone,
            'cloud_uploaded' => $cloudUploaded,
            'cloud_error' => $cloudError,
            'created_at' => now(),
        ];
    }

    /**
     * Generate pure PHP SQL dump of all database tables (Universal for MySQL/SQLite/PostgreSQL).
     */
    public function generateSqlDump(): string
    {
        $driver = config('database.default');
        $pdo = DB::connection()->getPdo();

        $output = "-- ========================================================\n";
        $output .= "-- AIM-LIGHT HIGH SCHOOL SYSTEM DATABASE BACKUP\n";
        $output .= "-- Generated: " . now()->toIso8601String() . "\n";
        $output .= "-- Driver: " . $driver . "\n";
        $output .= "-- ========================================================\n\n";

        if ($driver === 'sqlite') {
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            $tableNames = array_map(fn($t) => $t->name, $tables);
        } else {
            $tables = DB::select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');
            $tableNames = array_map(fn($t) => array_values((array)$t)[0], $tables);
        }

        $output .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        foreach ($tableNames as $table) {
            $output .= "-- --------------------------------------------------------\n";
            $output .= "-- Table structure for `{$table}`\n";
            $output .= "-- --------------------------------------------------------\n";
            $output .= "DROP TABLE IF EXISTS `{$table}`;\n";

            if ($driver === 'sqlite') {
                $createRow = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name = ?", [$table]);
                if (!empty($createRow) && !empty($createRow[0]->sql)) {
                    $output .= $createRow[0]->sql . ";\n\n";
                }
            } else {
                $createRow = DB::select("SHOW CREATE TABLE `{$table}`");
                if (!empty($createRow)) {
                    $createSql = ((array)$createRow[0])['Create Table'] ?? '';
                    $output .= $createSql . ";\n\n";
                }
            }

            // Dump Data
            $rows = DB::table($table)->get();
            if ($rows->count() > 0) {
                $output .= "-- Dumping data for `{$table}`\n";
                $columns = array_keys((array)$rows->first());
                $columnList = '`' . implode('`, `', $columns) . '`';

                $chunks = $rows->chunk(100);
                foreach ($chunks as $chunk) {
                    $output .= "INSERT INTO `{$table}` ({$columnList}) VALUES\n";
                    $valueRows = [];
                    foreach ($chunk as $row) {
                        $values = [];
                        foreach ((array)$row as $val) {
                            if (is_null($val)) {
                                $values[] = "NULL";
                            } elseif (is_numeric($val)) {
                                $values[] = $val;
                            } else {
                                $values[] = $pdo->quote($val);
                            }
                        }
                        $valueRows[] = "(" . implode(', ', $values) . ")";
                    }
                    $output .= implode(",\n", $valueRows) . ";\n";
                }
                $output .= "\n";
            }
        }

        $output .= "SET FOREIGN_KEY_CHECKS = 1;\n";
        $output .= "-- Backup completed successfully.\n";

        return $output;
    }

    /**
     * Upload backup file to Supabase Storage via REST API.
     */
    public function uploadToSupabase(string $filePath, string $filename): bool
    {
        $url = rtrim(env('SUPABASE_URL', ''), '/');
        $key = env('SUPABASE_SERVICE_ROLE_KEY') ?: env('SUPABASE_ANON_KEY', '');
        $bucket = env('SUPABASE_STORAGE_BUCKET', 'school-backups');

        if (empty($url) || empty($key)) {
            return false;
        }

        $fileContent = File::get($filePath);
        $endpoint = "{$url}/storage/v1/object/{$bucket}/{$filename}";

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$key}",
            'apikey' => $key,
            'Content-Type' => 'application/gzip',
            'x-upsert' => 'true',
        ])->withBody($fileContent, 'application/gzip')->post($endpoint);

        if (!$response->successful()) {
            Log::error("Supabase Storage upload failed: " . $response->body());
            return false;
        }

        return true;
    }

    /**
     * List all local and cloud backups with metadata.
     */
    public function listBackups(): array
    {
        $backups = [];
        $files = File::files($this->backupDir);

        foreach ($files as $file) {
            $name = $file->getFilename();
            if (!str_ends_with($name, '.sql.gz') && !str_ends_with($name, '.sql')) {
                continue;
            }

            $size = $file->getSize();
            $mtime = $file->getMTime();
            $isMilestone = str_starts_with($name, 'milestone_');

            $backups[] = [
                'filename' => $name,
                'path' => $file->getPathname(),
                'size' => $size,
                'formatted_size' => $this->formatBytes($size),
                'created_at' => \Carbon\Carbon::createFromTimestamp($mtime),
                'age_days' => round((time() - $mtime) / 86400, 1),
                'is_milestone' => $isMilestone,
                'location' => 'local',
            ];
        }

        usort($backups, fn($a, $b) => $b['created_at']->timestamp <=> $a['created_at']->timestamp);

        return $backups;
    }

    /**
     * Prune regular daily backups older than $days (Milestones are permanently preserved).
     */
    public function pruneBackups(int $days = 60): array
    {
        $deleted = [];
        $cutoff = now()->subDays($days)->timestamp;

        $files = File::files($this->backupDir);
        foreach ($files as $file) {
            $name = $file->getFilename();
            if (str_starts_with($name, 'milestone_')) {
                continue;
            }

            if ($file->getMTime() < $cutoff) {
                $deleted[] = $name;
                File::delete($file->getPathname());
                $this->deleteFromSupabase($name);
            }
        }

        return $deleted;
    }

    /**
     * Delete a single backup file locally and from Supabase.
     */
    public function deleteBackup(string $filename): bool
    {
        $cleanName = basename($filename);
        $filePath = $this->backupDir . DIRECTORY_SEPARATOR . $cleanName;

        $deletedLocal = false;
        if (File::exists($filePath)) {
            $deletedLocal = File::delete($filePath);
        }

        $this->deleteFromSupabase($cleanName);

        return $deletedLocal;
    }

    /**
     * Delete object from Supabase Storage.
     */
    public function deleteFromSupabase(string $filename): bool
    {
        if (!$this->isSupabaseConfigured()) {
            return false;
        }

        $url = rtrim(env('SUPABASE_URL', ''), '/');
        $key = env('SUPABASE_SERVICE_ROLE_KEY') ?: env('SUPABASE_ANON_KEY', '');
        $bucket = env('SUPABASE_STORAGE_BUCKET', 'school-backups');

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$key}",
                'apikey' => $key,
            ])->delete("{$url}/storage/v1/object/{$bucket}", [
                'prefixes' => [$filename],
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning("Could not delete from Supabase: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if Supabase credentials are configured in .env.
     */
    public function isSupabaseConfigured(): bool
    {
        return !empty(env('SUPABASE_URL')) && (!empty(env('SUPABASE_SERVICE_ROLE_KEY')) || !empty(env('SUPABASE_ANON_KEY')));
    }

    /**
     * Helper to format bytes into KB, MB, GB.
     */
    public function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
