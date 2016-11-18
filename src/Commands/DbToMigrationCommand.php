<?php

namespace Shakogegia\Dbtomigration\Commands;

use Illuminate\Console\Command;

use Shakogegia\Dbtomigration\GenerateMigrationFiles;

class DbToMigrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dbtomigration:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate migration files from database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        return new GenerateMigrationFiles();
    }

}
