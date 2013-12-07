<?php namespace EDb;

class EloquentBuilder
{
	public static function where($where, $operator = '=', $what = '')
    {
    	$self = static::bootup();
		return DB::table($self->table,get_called_class())->where($where, $operator, $what);
    }

	public static function all()
	{
		$self = static::bootup();
		$result = DB::table($self->table,get_called_class())->get();
		return isset($result)? $result:Null;
	}

	public static function delete()
	{
		$self = static::bootup();
		return DB::table($self->table,get_called_class())->delete();
	}

	public static function find($id)
	{
		$self = static::bootup();
		static::$forUpdateDbTable = static::$DbTable;
		$DbTable = static::$forUpdateDbTable->where($self->primaryKey,'=',$id);
		$result = $DbTable->take(1)->get();
		return isset($result[0])? Cast::toObject($self,$result[0]):Null;
	}

	public function save()
	{
		$options = [];
		$blacklisted = ['primaryKey','table','called_class','timestamps'];
		foreach ($this as $key => $value) {
			if ( !in_array($key,$blacklisted) ) {
				$options[$key] = $value;
			}
		}
		if(is_null(static::$forUpdateDbTable) )
		{
			return static::create($options);
		} else {
			return static::update($options);
		}
	}

	public static function create(array $options = [])
	{
		$self = static::bootup();

		return static::$DbTable->insert($options);
	}

	public static function update(array $options = [])
	{
		return static::$forUpdateDbTable->update($options);
	}
}