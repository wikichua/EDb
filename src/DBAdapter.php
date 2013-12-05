<?php

abstract class DBAdapter
{
	public $connection = null;
	public $default_id = 'id';

	function __construct($connection = null) {
		$this->connection = $connection;
		$this->connect();
	}

	public function connect()
	{
		$this->connection = is_null($this->connection)? 'MysqlSetup': str_replace("Setup", '', $this->connection) . 'Setup';
		$this->Server = (new $this->connection)->connect();
		return $this;
	}

	public function table($table)
	{
		$this->Server->setTable($table);
		return $this->Server;
	}

}