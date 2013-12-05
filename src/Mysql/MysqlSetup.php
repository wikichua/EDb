<?php

// use PDO;

class MysqlSetup extends MysqlBuilder implements DBSetupInterface
{
	protected $user;
	protected $password;
	protected $host;
	protected $database;
	protected $options;
	protected static $connection = null;
	protected $table = "";

	function __construct() {
	}

	public function setTable($table)
	{
		$this->table = $table;
	}

	public function getConnection()
	{
		return static::$connection;
	}

	protected function configuration()
	{
		$this->user = 'test';
		$this->password = 'test';
		$this->database = 'test';
		$this->host = 'localhost';
		return $this;
	}

	public function connect()
	{
		$this->configuration()->connecting();
		return $this;
	}

	public function take($value=1)
	{
		$this->take = $value;
		return $this;
	}

	public function skip($value=0)
	{
		$this->skip = $value;
		return $this;
	}

	public function get()
	{
		$sqls[] = "SELECT " . $this->select;
		$sqls[] = "FROM " . $this->table;
		$sqls[] = $this->joining();
		$sqls[] = $this->generateWheres();
		if(($this->skip + $this->take) > 0)
		{
			$limit = '';
			if($this->skip >= 0)
			{
				$limit .= "{$this->skip}";
			}
			if($this->take > 0)
			{
				$limit .= ",{$this->take}";
			}
			$sqls[] = "LIMIT " . $limit;
		}

		return $this->query(implode(' ', $sqls));
	}

	public function insert($sets)
	{
		$this->sets($sets)->inserting();
		return $this;
	}

	public function delete()
	{
		$this->deleting();
		return $this;
	}

	public function update($sets)
	{
		$this->sets($sets)->updating();
		return $this;
	}

	public function where($where, $operator = '=', $what='')
	{
		if(!is_callable($where))
		{
			if(!preg_match('/^\d*\d$/i', $what))
				$what = '"' . $what. '"';
			$this->wheres[] = $where . $operator . $what;
		} else {
			$Query = new MysqlNestedQuery;
			call_user_func($where, $Query);
			$this->wheres[] = $Query->generateWheres();
		}

		return $this;
	}

	public function orWhere($where, $operator = '=', $what)
	{
		if(!is_callable($where))
		{
			if(!preg_match('/^\d*\d$/i', $what))
				$what = '"' . $what. '"';
			$this->orWheres[] = ' OR ' . $where . $operator . $what;
		} else {
			$Query = new MysqlNestedQuery;
			call_user_func($where, $Query);
			$this->orWheres[] = $Query->generateWheres();
		}
		
		return $this;
	}

	public function debug()
	{
		$this->debug = true;
		return $this;
	}

	public function join($table, $leftField, $operator = '=', $rightField = '')
	{
		if(!is_callable($leftField))
		{
			$this->joins[$table] = "JOIN {$table} ON {$leftField} {$operator} {$rightField}";
		} else {
			$Query = new MysqlNestedQuery;
			call_user_func($leftField, $Query);
			$this->joins[$table] = "JOIN {$table} ON ". $Query->generateOns();
		}
		return $this;
	}

	public function leftJoin($table, $leftField, $operator = '=', $rightField = '')
	{
		if(!is_callable($leftField))
		{
			$this->joins[$table] = "LEFT JOIN {$table} ON {$leftField} {$operator} {$rightField}";
		} else {
			$Query = new MysqlNestedQuery;
			call_user_func($leftField, $Query);
			$this->joins[$table] = "LEFT JOIN {$table} ON ". $Query->generateOns();
		}

		return $this;
	}

	public function rightJoin($table, $leftField, $operator = '=', $rightField = '')
	{
		if(!is_callable($leftField))
		{
			$this->joins[$table] = "RIGHT JOIN {$table} ON {$leftField} {$operator} {$rightField}";
		} else {
			$Query = new MysqlNestedQuery;
			call_user_func($leftField, $Query);
			$this->joins[$table] = "RIGHT JOIN {$table} ON ". $Query->generateOns();
		}

		return $this;
	}

}
