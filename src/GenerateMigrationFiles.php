<?php

namespace Shakogegia\Dbtomigration;

use \Symfony\Component\Console\Output\ConsoleOutput;

use DB;
use File;

class GenerateMigrationFiles
{
	private $tables = [];
	private $columns = [];

	private $dbSchema = array();

	public function __construct()
	{
		$this->start();
	}

	public function showInfo($info='')
    {
		$output = new ConsoleOutput();
		$output->writeln("<info>{$info}</info>");
    }

    public function start()
    {
        $this->showInfo('Generating migrations...');
        
        $this->fetchAllColumns();
    }

    /*public function fetchTables()
    {
        $tables = DB::select('SHOW TABLES');
        
        foreach ($tables as $table) {
            foreach ($table as $key => $value){
                $this->tables[] = $value;
            }
        }

        $this->fetchAllColumns();
    }*/


    public function fetchAllColumns()
    {
    	$dbname = env('DB_DATABASE');
	 	
	 	$columns = DB::select('SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? ', [$dbname]);
      
        $this->columns = $columns;

        $this->dbSchema();
    }


    public function dbSchema()
    {
	 	foreach ($this->columns as $value) {
	 		if (!isset($this->dbSchema[ $value->TABLE_NAME ])){
	 			$this->dbSchema[ $value->TABLE_NAME ] = [];
	 		}

	 		$this->dbSchema[ $value->TABLE_NAME ][] = $value;
	 	}

	 	$this->generateMigrationFiles();
    }

    public function generateMigrationFiles()
    {
    	$sample = file_get_contents(__DIR__ .'/migration');

    	/*echo "<pre>";
    	print_r($this->dbSchema);
    	echo "</pre>";
    	exit;*/

    	foreach ($this->dbSchema as $table => $columns) {

    		$classStr = str_replace("{class_name}", "Create".ucfirst($table)."Table", $sample);
    		
    		$tableStr = str_replace("{table_name}", "{$table}", $classStr);

    		$columnsStr = $this->generateColumns($columns);

    		$migrationStr = str_replace("{columns}", "{$columnsStr}", $tableStr);

	    	$this->saveFile($migrationStr, $table);
    	}
    }

    public function generateColumns($columns=[])
    {
    	$columnsStr = '';

    	foreach ($columns as $key => $column) {
    		$str = $this->generateColumn($column);

			$tab = '';
			if ($key>0) {
				$tab = "\t\t\t";
			}

    		$columnsStr .= "{$tab}{$str}";
    	}

    	return $columnsStr;
    }

    public function generateColumn($column)
    {
    	$str = '$table->';

		$type = 'string';

		if ($column->COLUMN_KEY == 'PRI')
			$type = 'increments';

		if ($column->COLUMN_TYPE == 'datetime')
			$type = 'dateTime';

		if ($column->COLUMN_TYPE == 'text')
			$type = 'text';
		
		if ($column->COLUMN_TYPE == 'tinyint')
			$type = 'tinyInteger';

		if ($column->COLUMN_TYPE == 'timestamp')
			$type = 'timestamp';
		
		$str .= $type;

		$str .= "('{$column->COLUMN_NAME}');\n";
		
		return $str;
    }

    public function saveFile($migrationStr='', $table)
    {
    	$file_name = date('Y').'_'.date('m').'_'.date('d').'_'.date('His').'_create_'.$table.'_table';

    	$path = base_path("database/migrations/{$file_name}.php");
    	
    	$bytes_written = File::put($path, $migrationStr);
		
		if ($bytes_written === false)
		{
        	$this->showInfo('Error writing to file!');
        	die();
		}
    }
}