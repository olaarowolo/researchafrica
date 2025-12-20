<?php

/**
 * Research Africa Multi-Journal Transformation
 * Migration Rollback Script for Sprint 1
 *
 * This script safely rolls back the semantic clarity migration
 * and restores data integrity
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

class SemanticClarityRollback
{
    private $logFile;
    private $backupFile;

    public function __construct()
    {
        $this->logFile = __DIR__ . '/rollback_log_' . date('Y-m-d_H-i-s') . '.txt';
        $this->backupFile = $this->createBackupBeforeRollback();
    }

    private function log($message)
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] {$message}" . PHP_EOL;
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
        echo $logMessage;
    }

    private function createBackupBeforeRollback()
    {
        $timestamp = date('Y-m-d_H-i-s');
        $backupFile = __DIR__ . "/../backups/pre_rollback_backup_{$timestamp}.sql";

        $this->log("Creating backup before rollback: {$backupFile}");

        try {
            // Create directory if it doesn't exist
            $backupDir = dirname($backupFile);
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            // Get database configuration
            $config = config('database.connections.mysql');

            // Create backup using mysqldump
            $command = sprintf(
                'mysqldump -h%s -u%s -p%s %s > %s',
                $config['host'],
                $config['username'],
                $config['password'] ?? '',
                $config['database'],
                $backupFile
            );

            exec($command . ' 2>&1', $output, $returnCode);

            if ($returnCode === 0) {
                $this->log("✅ Pre-rollback backup created successfully");
                return $backupFile;
            } else {
                $this->log("❌ Failed to create pre-rollback backup: " . implode("\n", $output));
                return null;
            }

        } catch (Exception $e) {
            $this->log("❌ Error creating backup: " . $e->getMessage());
            return null;
        }
    }

    public function rollbackMigration()
    {
        $this->log("🔄 Starting semantic clarity migration rollback...");

        try {
            // Step 1: Verify current migration status
            $this->log("Verifying migration status...");
            $this->verifyMigrationStatus();

            // Step 2: Backup current data
            $this->backupCurrentData();

            // Step 3: Rollback the migration
            $this->log("Rolling back migration...");
            $this->runRollback();

            // Step 4: Verify rollback
            $this->verifyRollback();

            $this->log("🎉 Migration rollback completed successfully!");

        } catch (Exception $e) {
            $this->log("❌ Rollback failed: " . $e->getMessage());
            $this->log("🔧 Check backup file: {$this->backupFile}");
            throw $e;
        }
    }

    private function verifyMigrationStatus()
    {
        // Check if the new columns exist
        $columns = DB::select("SHOW COLUMNS FROM article_categories");
        $columnNames = array_column($columns, 'Field');

        $requiredColumns = ['name', 'display_name', 'is_journal', 'journal_slug'];
        $missingColumns = array_diff($requiredColumns, $columnNames);

        if (!empty($missingColumns)) {
            throw new Exception("Migration appears to not have been applied. Missing columns: " . implode(', ', $missingColumns));
        }

        $this->log("✅ Migration status verified - all semantic columns present");
    }

    private function backupCurrentData()
    {
        $this->log("Backing up current data from new columns...");

        try {
            $data = DB::table('article_categories')
                ->select('id', 'name', 'display_name', 'is_journal', 'journal_slug', 'category_name')
                ->whereNotNull('name')
                ->get();

            if ($data->isNotEmpty()) {
                $backupFile = __DIR__ . "/../backups/semantic_data_backup_" . date('Y-m-d_H-i-s') . '.json';
                file_put_contents($backupFile, $data->toJson());
                $this->log("✅ Current semantic data backed up to: {$backupFile}");
            } else {
                $this->log("ℹ️ No semantic data to backup");
            }

        } catch (Exception $e) {
            $this->log("⚠️ Warning: Could not backup semantic data: " . $e->getMessage());
        }
    }

    private function runRollback()
    {
        // Rollback using Laravel migration
        $exitCode = null;
        $output = [];

        exec('php artisan migrate:rollback --step=1 2>&1', $output, $exitCode);

        if ($exitCode !== 0) {
            throw new Exception("Migration rollback failed. Output: " . implode("\n", $output));
        }

        $this->log("✅ Laravel migration rolled back");
    }

    private function verifyRollback()
    {
        // Verify columns are removed
        $columns = DB::select("SHOW COLUMNS FROM article_categories");
        $columnNames = array_column($columns, 'Field');

        $removedColumns = ['name', 'display_name', 'is_journal', 'journal_slug'];
        $existingColumns = array_intersect($removedColumns, $columnNames);

        if (!empty($existingColumns)) {
            throw new Exception("Rollback verification failed. Columns still exist: " . implode(', ', $existingColumns));
        }

        // Verify original category_name still works
        $originalData = DB::table('article_categories')->first();
        if (!$originalData || !isset($originalData->category_name)) {
            throw new Exception("Rollback verification failed. Original data structure compromised.");
        }

        $this->log("✅ Rollback verified - all semantic columns removed, original structure intact");
    }

    public static function run()
    {
        echo "🔄 Research Africa - Semantic Clarity Migration Rollback\n";
        echo "=================================================\n\n";

        $rollback = new self();

        try {
            $rollback->rollbackMigration();

            echo "\n🎉 Rollback completed successfully!\n";
            echo "📁 Backup files:\n";
            echo "   - Database: {$rollback->backupFile}\n";
            echo "   - Log: {$rollback->logFile}\n";

        } catch (Exception $e) {
            echo "\n❌ Rollback failed!\n";
            echo "📁 Check backup: {$rollback->backupFile}\n";
            echo "📝 Full log: {$rollback->logFile}\n";
            echo "💡 To restore from backup, run:\n";
            echo "   mysql -u[username] -p[password] researchafrica < {$rollback->backupFile}\n";

            exit(1);
        }
    }
}

// Run the rollback
SemanticClarityRollback::run();

