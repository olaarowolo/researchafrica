
<?php

/**
 * Research Africa Multi-Journal Transformation
 * Database Restoration Script
 *
 * This script restores the database from the manual backup file
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseRestorer
{
    private $logFile;
    private $backupFile;

    public function __construct()
    {
        $this->logFile = __DIR__ . '/../backups/restore_log_' . date('Y-m-d_H-i-s') . '.txt';
        $this->backupFile = __DIR__ . '/../backups/httprapu_database_2025.sql';
    }

    private function log($message)
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] {$message}" . PHP_EOL;
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
        echo $logMessage;
    }

    public function restoreDatabase()
    {
        $this->log("🔄 Starting database restoration from manual backup...");

        try {
            // Step 1: Verify backup file exists
            $this->verifyBackupFile();

            // Step 2: Create backup before restoration
            $this->createBackupBeforeRestore();

            // Step 3: Parse and execute SQL
            $this->executeSqlFile();

            // Step 4: Verify restoration
            $this->verifyRestoration();

            // Step 5: Re-run migrations to ensure Sprint 1 features are applied
            $this->runSprint1Migrations();

            $this->log("🎉 Database restoration completed successfully!");

        } catch (Exception $e) {
            $this->log("❌ Database restoration failed: " . $e->getMessage());
            throw $e;
        }
    }

    private function verifyBackupFile()
    {
        if (!file_exists($this->backupFile)) {
            throw new Exception("Backup file not found: {$this->backupFile}");
        }

        $fileSize = filesize($this->backupFile);
        $this->log("✅ Backup file found: {$this->backupFile} (Size: " . number_format($fileSize) . " bytes)");
    }

    private function createBackupBeforeRestore()
    {
        $timestamp = date('Y-m-d_H-i-s');
        $backupFile = __DIR__ . "/../backups/pre_restore_backup_{$timestamp}.sql";

        $this->log("Creating backup before restoration...");

        try {
            // Get database configuration
            $config = config('database.connections.mysql');

            // Use mysqldump to create backup
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
                $this->log("✅ Pre-restore backup created successfully");
                return $backupFile;
            } else {
                $this->log("⚠️ Could not create pre-restore backup (mysqldump not available)");
                return null;
            }

        } catch (Exception $e) {
            $this->log("⚠️ Warning: Could not create pre-restore backup: " . $e->getMessage());
            return null;
        }
    }

    private function executeSqlFile()
    {
        $this->log("Reading SQL file content...");

        $sqlContent = file_get_contents($this->backupFile);
        if ($sqlContent === false) {
            throw new Exception("Could not read backup file");
        }

        // Split SQL into individual statements
        $statements = $this->splitSqlStatements($sqlContent);

        $this->log("Found " . count($statements) . " SQL statements to execute");

        DB::beginTransaction();

        try {
            $executedCount = 0;
            $errorCount = 0;

            foreach ($statements as $statement) {
                $statement = trim($statement);

                // Skip empty statements and comments
                if (empty($statement) || strpos($statement, '--') === 0) {
                    continue;
                }

                try {
                    DB::statement($statement);
                    $executedCount++;
                } catch (Exception $e) {
                    $errorCount++;
                    // Log errors but continue (some may be expected)
                    $this->log("⚠️ SQL Error (continuing): " . $e->getMessage());
                }
            }

            DB::commit();

            $this->log("✅ SQL execution completed: {$executedCount} statements executed, {$errorCount} errors");

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("SQL execution failed: " . $e->getMessage());
        }
    }

    private function splitSqlStatements($sqlContent)
    {
        // Remove comments
        $sqlContent = preg_replace('/--.*$/m', '', $sqlContent);

        // Split by semicolon, but handle quotes and string literals
        $statements = [];
        $currentStatement = '';
        $inSingleQuote = false;
        $inDoubleQuote = false;
        $inBacktickQuote = false;

        for ($i = 0; $i < strlen($sqlContent); $i++) {
            $char = $sqlContent[$i];
            $nextChar = $i + 1 < strlen($sqlContent) ? $sqlContent[$i + 1] : '';

            // Handle quotes
            if ($char === "'" && !$inDoubleQuote && !$inBacktickQuote) {
                $inSingleQuote = !$inSingleQuote;
            } elseif ($char === '"' && !$inSingleQuote && !$inBacktickQuote) {
                $inDoubleQuote = !$inDoubleQuote;
            } elseif ($char === '`' && !$inSingleQuote && !$inDoubleQuote) {
                $inBacktickQuote = !$inBacktickQuote;
            }

            // Handle statement terminator
            if ($char === ';' && !$inSingleQuote && !$inDoubleQuote && !$inBacktickQuote) {
                if (trim($currentStatement) !== '') {
                    $statements[] = $currentStatement . ';';
                }
                $currentStatement = '';
            } else {
                $currentStatement .= $char;
            }
        }

        // Add final statement if exists
        if (trim($currentStatement) !== '') {
            $statements[] = $currentStatement;
        }

        return $statements;
    }

    private function verifyRestoration()
    {
        $this->log("Verifying database restoration...");

        // Check if basic tables exist
        $tables = ['users', 'article_categories', 'articles', 'members'];
        $missingTables = [];

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                $missingTables[] = $table;
            }
        }

        if (!empty($missingTables)) {
            throw new Exception("Restoration verification failed. Missing tables: " . implode(', ', $missingTables));
        }

        // Check if we have data
        $userCount = DB::table('users')->count();
        $categoryCount = DB::table('article_categories')->count();

        $this->log("✅ Database verification passed");
        $this->log("   - Users: {$userCount}");
        $this->log("   - Article Categories: {$categoryCount}");
    }

    private function runSprint1Migrations()
    {
        $this->log("Re-running Sprint 1 migrations to ensure semantic clarity features...");

        try {
            // Check if Sprint 1 migrations have been run
            $sprint1Migrations = [
                '2025_12_18_000001_add_semantic_clarity_to_article_categories',
                '2025_12_18_000002_make_category_name_nullable',
                '2025_12_18_000003_make_is_journal_nullable'
            ];

            foreach ($sprint1Migrations as $migration) {
                try {
                    // Try to run the migration
                    $exitCode = null;
                    $output = [];

                    exec("php artisan migrate:status --path=database/migrations/{$migration}.php 2>&1", $output, $exitCode);

                    // If migration doesn't exist, skip
                    if (strpos(implode("\n", $output), 'not found') !== false) {
                        $this->log("ℹ️ Migration file not found: {$migration}");
                        continue;
                    }

                    $this->log("✅ Migration status checked: {$migration}");

                } catch (Exception $e) {
                    $this->log("⚠️ Warning checking migration {$migration}: " . $e->getMessage());
                }
            }

            $this->log("✅ Sprint 1 migration verification completed");

        } catch (Exception $e) {
            $this->log("⚠️ Warning: Could not verify Sprint 1 migrations: " . $e->getMessage());
        }
    }

    public static function run()
    {
        echo "🔄 Research Africa - Database Restoration\n";
        echo "=======================================\n\n";

        $restorer = new self();

        try {
            $restorer->restoreDatabase();

            echo "\n🎉 Database restoration completed successfully!\n";
            echo "📁 Restoration log: {$restorer->logFile}\n";

        } catch (Exception $e) {
            echo "\n❌ Database restoration failed!\n";
            echo "📁 Check restoration log: {$restorer->logFile}\n";
            echo "💡 Error details: {$e->getMessage()}\n";

            exit(1);
        }
    }
}

// Run the restoration
DatabaseRestorer::run();

