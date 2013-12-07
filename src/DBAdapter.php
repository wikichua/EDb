<?php namespace EDb;

abstract class DBAdapter
{
	public $connection = null;
	public $driver = null;
	public $default_id = 'id';

	function __construct($driver = null) {
		if(is_null($driver))
		{
			$this->connection = Config::get('database.default');
			$driver = ucfirst(Config::get('database.connections.'.$this->connection.'.driver'));
		}
		$this->driver = $driver;
		$this->connect();
	}

	public function connect()
	{
		$this->driver = is_null($this->driver)? 'MysqlSetup': str_replace("Setup", '', $this->driver) . 'Setup';
		$driver = "EDb\\$this->driver";
		$this->Server = (new $driver)->connect($this->connection);
		return $this;
	}

	public function table($table,$called_class='')
	{
		$this->Server->setTable($table,$called_class);
		
		return $this->Server;
	}

}