<?php

abstract class MysqlBuilder
{
	protected $from;
	protected $select = "*";
	protected $sets = [];
	protected $skip = 0;
	protected $take = 0;
	protected $debug = false;
	protected $wheres = [];
	protected $orWheres = [];
	protected $joins = [];

	public function select($select = "*")
	{
		$this->$select = $select;
		return $this;
	}

	public function sets(array $sets = [])
	{
		$this->sets = $sets;
		return $this;
	}

	protected function connecting()
	{
		try {
			if(static::$connection == null)
			{
			    static::$connection = new PDO('mysql:host='.$this->host.';dbname='.$this->database, $this->user, $this->password,$this->options);
			    static::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
		} catch(PDOException $e) {
		    echo 'ERROR: ' . $e->getMessage();
		}
		return $this;
	}

	protected function query($sql,array $map = [])
	{
		try {
			$conn = static::$connection;
			$stmt = $conn->prepare($sql);
			if($this->debug) die(var_dump($stmt->queryString));
			$stmt->execute($map);		 
			$result = $stmt->fetchAll(PDO::FETCH_OBJ); 
			return $result;
		} catch(PDOException $e) {
		    echo 'ERROR: ' . $e->getMessage();
		    return False;
		}

		return $this;
	}

	protected function inserting()
	{
		$sqls[] = "INSERT INTO " . $this->table . "(".implode(',',array_keys($this->sets)).")";
		$sqls[] = "VALUES(:".implode(',:',array_keys($this->sets)).")";
		$sql = implode(' ', $sqls);
		$pdo = static::$connection;
		try {
		  	$pdo->beginTransaction();
		  	$stmt = $pdo->prepare($sql);
			if($this->debug) die(var_dump($stmt->queryString));
			$stmt->execute($this->sets);
			$pdo->commit();
		} catch(PDOException $e) {
			$pdo->rollback();
		  	echo 'Error: ' . $e->getMessage();
		  	return False;
		}
		
		return $this;
	}

	protected function deleting()
	{
		$bindWheres = [];
		$sqls[] = "DELETE FROM " . $this->table;
		$sqls[] = $this->generateWheres();
		$sql = implode(' ', $sqls);
		$pdo = static::$connection;		 
		try {
			$pdo->beginTransaction();
		  	$stmt = $pdo->prepare($sql);
			if($this->debug) die(var_dump($stmt->queryString));
		  	$stmt->execute();
		    $pdo->commit();
		} catch(PDOException $e) {
			$pdo->rollback();
		  	echo 'Error: ' . $e->getMessage();
		}
	}

	protected function updating()
	{
		$bindWheres = [];
		$sqls[] = "UPDATE " . $this->table;
		foreach($this->sets as $key => $val)
		{
			$sets[] = $key . ' = "' . $val . '"';
		}
		$sqls[] = "SET " . implode(',', $sets);
		$sqls[] = $this->generateWheres();
		$sql = implode(' ', $sqls);
		$pdo = static::$connection;	
		try {
		  	$pdo->beginTransaction();	 
		  	$stmt = $pdo->prepare($sql);
			if($this->debug) die(var_dump($stmt->queryString));
		  	$stmt->execute();
		  	$pdo->commit();
		} catch(PDOException $e) {
			$pdo->rollback();
		  	echo 'Error: ' . $e->getMessage();
		}
	}

	protected function generateWheres()
	{
		$WHERE = [];
		if(count($this->wheres) > 0)
		{
			$WHERE[] = implode(' AND ',$this->wheres);
		}
		if(count($this->orWheres) > 0)
		{
			$WHERE[] = implode('',$this->orWheres);
		}

		return count($WHERE)>0? "WHERE " . implode('',$WHERE):'';
	}

	protected function joining()
	{
		if(count($this->joins) > 0)
			return implode(' ',$this->joins);
		return '';
	}
}

class MysqlNestedQuery
{
	protected $wheres = [];
	protected $orWheres = [];
	protected $joins = [];
	protected $orJoins = [];

	public function where($where, $operator = ' = ', $what='')
	{
		if(!preg_match('/^\d*\d$/i', $what))
				$what = '"' . $what. '"';
		$this->wheres[] = $where . $operator . $what;
		return $this;
	}

	public function orWhere($where, $operator = ' = ', $what)
	{
		if(!preg_match('/^\d*\d$/i', $what))
				$what = '"' . $what. '"';
		$this->orWheres[] = ' OR ' . $where . $operator . $what;
		return $this;
	}

	public function on($leftField, $operator = '=', $rightField = '')
	{
		$this->joins[] = "{$leftField} {$operator} {$rightField}";
		return $this;
	}

	public function orOn($leftField, $operator = '=', $rightField = '')
	{
		$this->orJoins[] = " OR {$leftField} {$operator} {$rightField}";
		return $this;
	}

	public function generateOns()
	{
		$JOINS = [];
		if(count($this->joins) > 0)
		{
			$JOINS[] = implode(' AND ',$this->joins);
		}
		if(count($this->orJoins) > 0)
		{
			$JOINS[] = implode(' ',$this->orJoins);
		}

		return count($JOINS)>0? '('. implode('',$JOINS) . ')':'';
	}

	public function generateWheres()
	{
		$WHERE = [];
		if(count($this->wheres) > 0)
		{
			$WHERE[] = implode(' AND ',$this->wheres);
		}
		if(count($this->orWheres) > 0)
		{
			$WHERE[] = implode('',$this->orWheres);
		}

		return count($WHERE)>0? '('. implode('',$WHERE) . ')':'';
	}
	
}