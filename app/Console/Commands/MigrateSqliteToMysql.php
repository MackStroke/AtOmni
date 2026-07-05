<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateSqliteToMysql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:migrate-sqlite {--path=database/database.sqlite : Path to SQLite file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate data from SQLite database to active MySQL database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sqlitePath = base_path($this->option('path'));
        
        if (!file_exists($sqlitePath)) {
            $this->error("SQLite database not found at {$sqlitePath}");
            return Command::FAILURE;
        }

        // Temporarily configure the sqlite connection to point to the file
        config(['database.connections.sqlite_temp' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => $sqlitePath,
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ]]);

        $this->info("Connecting to SQLite database...");
        
        try {
            DB::connection('sqlite_temp')->getPdo();
        } catch (\Exception $e) {
            $this->error("Could not connect to SQLite: " . $e->getMessage());
            return Command::FAILURE;
        }

        $schemaTables = Schema::connection('sqlite_temp')->getTables();
        $tables = array_map(function ($t) { return $t['name']; }, $schemaTables);
        
        // Exclude system tables that shouldn't be migrated this way
        $tablesToSkip = ['migrations', 'sessions', 'password_reset_tokens', 'sqlite_sequence'];
        
        $this->info("Found " . count($tables) . " tables in SQLite. Starting migration...");
        
        // Disable foreign key checks on MySQL during migration
        Schema::disableForeignKeyConstraints();

        $migratedCount = 0;

        // Truncate non-skipped tables first to avoid conflicts
        foreach ($tables as $table) {
            if (in_array($table, $tablesToSkip)) continue;
            
            if (Schema::hasTable($table)) {
                $this->line("Truncating {$table} on MySQL...");
                DB::table($table)->truncate();
            }
        }

        foreach ($tables as $table) {
            if (in_array($table, $tablesToSkip)) continue;

            if (!Schema::hasTable($table)) {
                $this->warn("Table {$table} does not exist in MySQL. Skipping.");
                continue;
            }

            $count = DB::connection('sqlite_temp')->table($table)->count();
            
            if ($count === 0) {
                $this->line("Table {$table} is empty. Skipping.");
                continue;
            }

            $this->line("Migrating {$count} rows from {$table}...");
            
            // Chunking to handle potentially large tables without blowing up memory
            DB::connection('sqlite_temp')->table($table)->orderBy('id')->chunk(500, function ($rows) use ($table, &$migratedCount) {
                $insertData = [];
                foreach ($rows as $row) {
                    $insertData[] = (array) $row;
                }
                
                if (!empty($insertData)) {
                    DB::table($table)->insert($insertData);
                    $migratedCount += count($insertData);
                }
            });
        }

        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();

        $this->info("Migration complete! Transferred {$migratedCount} rows across all tables.");
        
        return Command::SUCCESS;
    }
}
