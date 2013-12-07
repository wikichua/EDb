<?php namespace EDb;

trait EloquentMagic
{
	protected static $__instance = NULL;
    protected static $__objectStatic = NULL;
    protected static $__objectNonStatic = NULL;

	public function __call($name, $arguments) 
    {
    	return static::__facadeCaller($name, $arguments);
    }

	public static function __callStatic($name, $arguments) 
	{
		$reflect = new \ReflectionClass(get_called_class());
    	if($reflect->hasMethod('scope'.$name))
    	{
    		$self = static::bootup();
    		$name = 'scope'.$name;
    		$arguments = array_merge_recursive([$self],$arguments);
    	}

        return static::__facadeCaller($name, $arguments);  
	}

	private static function __facadeCaller( $name , $arguments )
    {
        $object = static::__facadeObjectStatic();
        if ( !is_null( $object ) AND method_exists( $object , $name ) )
        {
            return call_user_func_array( [$object , $name] , $arguments );
        }

        $object = static::__facadeObjectNonStatic();
        if ( !is_null( $object ) AND method_exists( $object , $name ) )
        {
            return call_user_func_array( [$object , $name] , $arguments );
        }

        throw new \Exception( "Method do not exist. " . $name );

        return null;
    }

    private static function __facadeObjectStatic()
    {

        return static::$__objectStatic = is_null( static::$__objectStatic ) ?
            (
                is_null( static::$__instance ) ? NULL : static::$__instance
            ) : static::$__objectStatic;
    }

    private static function __facadeObjectNonStatic()
    {
        $instance = get_called_class();
        return static::$__objectNonStatic = is_null(static::$__objectNonStatic)? 
            new $instance():static::$__objectNonStatic;
    }
}