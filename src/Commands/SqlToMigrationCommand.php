<?php

namespace Shakogegia\Laramigrations\Commands;

use Illuminate\Console\Command;

use Shakogegia\Laramigrations\GenerateMigrationFiles;

class SqlToMigrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asd:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
