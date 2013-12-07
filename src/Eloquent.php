<?php
 
class Eloquent extends \EDb\EloquentBuilder
{
	use \EDb\EloquentMagic;
    
	protected static $DbTable = null;
	protected static $forUpdateDbTable = null;

	protected $table = '';
	protected $primaryKey = 'id';
	private $called_class = null;
	public $timestamps = true;

	function __construct() {
		static::$DbTable = null;
		static::$forUpdateDbTable = null;
	}

	function boot() 
	{
		$this->called_class = get_called_class();
		$this->table = empty($this->table)? strtolower(get_called_class().'s'):$this->table;
		static::$DbTable = DB::table($this->table,get_called_class());
	}

	protected static function bootup()
	{		
		$class = get_called_class();
		$self = new $class;
		$self->boot();
		return $self;
	}

	public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        return $this->$name;
    }
}