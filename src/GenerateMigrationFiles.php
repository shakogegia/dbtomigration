<?php

namespace Shakogegia\Dbtomigration;

use \Symfony\Component\Console\Output\ConsoleOutput;

use DB;
use File;

class GenerateMigrationFiles
{
	private $columns = [];

	private $dbSchema = array();
	
	private $types = [
		'int' => 'integer',
		'smallint' => 'smallInteger',
		'mediumint' => 'mediumInteger',
		'bigint' => 'bigInteger',
		'tinyint' => 'tinyInteger',
		'varchar' => 'string',
		'text' => 'text',
		'smalltext' => 'smallText',
		'mediumtext' => 'mediumText',
		'longtext' => 'longText',
		'datetime' => 'dateTime',
		'date' => 'date',
		'time' => 'time',
		'timestamp' => 'timestamp',
	];

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

    public function fetchAllColumns()
    {
    	$dbname = env('DB_DATABASE');
	 	
	 	$columns = DB::select('SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? ORDER BY TABLE_NAME, ORDINAL_POSITION ', [$dbname]);
      
        $this->columns = $columns;

        $this->fetchDbSchema();
    }


    public function fetchDbSchema()
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


    	foreach ($this->dbSchema as $table => $columns) {

	        $this->showInfo("Generating migration for: {$table}");

    		$classStr = str_replace("{class_name}", "Create".ucfirst($table)."Table", $sample);
    		
    		$tableStr = str_replace("{table_name}", "{$table}", $classStr);

    		$columnsStr = $this->generateColumns($columns);

    		$migrationStr = str_replace("{columns}", "{$columnsStr}", $tableStr);

	    	$this->saveFile($migrationStr, $table);
    	}

        $this->showInfo("Finished!");
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

		if ($column->COLUMN_KEY == 'PRI'){
			$type = 'increments';
		} else if(isset($this->types[$column->DATA_TYPE])) {
			$type = $this->types[$column->DATA_TYPE];
		}
		
		$str .= $type;

		$param = $this->columnParam($column, $type);
		$default = $this->columnDefault($column, $type);
		$comment = $this->columnComment($column, $type);

		$str .= "('{$column->COLUMN_NAME}'{$param}){$default}{$comment};\n";
		
		return $str;
    }

    public function columnParam($column, $type)
    {
    	$str = '';

    	if ($column->DATA_TYPE == 'varchar') {
    		$str = " , {$column->CHARACTER_MAXIMUM_LENGTH}";
    	}
    	
    	return $str;
    }

    public function columnDefault($column, $type)
    {
    	$str = '';

    	if ( isset($column->COLUMN_DEFAULT) ) {
    		$quote = !is_numeric($column->COLUMN_DEFAULT) ? "'" : '';
    		$str = "->default({$quote}{$column->COLUMN_DEFAULT}{$quote})";
    	}
    	
    	return $str;
    }

    public function columnComment($column, $type)
    {
    	$str = '';

    	if ( isset($column->COLUMN_COMMENT) && !empty($column->COLUMN_COMMENT) ) {
    		$str = "->comment('{$column->COLUMN_COMMENT}')";
    	}
    	
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