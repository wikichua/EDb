<?php
require_once './bootstrap.php';
/**
 * ConnectTest
 *
 * @group group
 */
class ConnectTest extends \PHPUnit_Framework_TestCase
{

    public function testConnect()
    {
    	$DB = new DB;

    	$DB->connection = "Mysql";

    	$this->assertSame('MysqlSetup',$DB->connect()->connection);
    }

    public function testConnectAlternative()
    {
    	$DB = new DB("Mysql");

    	$this->assertSame('MysqlSetup',$DB->connect()->connection);
    }

    public function testDelete()
    {
    	$DB = (new DB)->table('tests')->where('name','=','alice')->orWhere('name','=','john')->orWhere('name','=','jim')->delete();
    }
    
    public function testInsert()
    {
    	$DB = (new DB)->table('tests')->insert(
		    array('name' => 'alice', 'state' => 3278)
		);
    }

    public function testInsertJohn()
    {
    	$DB = (new DB)->table('tests')->insert(
		    array('name' => 'john', 'state' => 0)
		);
    }

    public function testInsertJim()
    {
    	$DB = (new DB)->table('tests')->insert(
		    array('name' => 'jim', 'state' => 0)
		);
    }


    public function testUpdate()
    {
    	$DB = (new DB)->table('tests')->where('name','=','john')->update(['state'=>'999']);
    }

    public function testSetAndGetTable()
    {
    	$tests = (new DB)->table('tests')->get();
    	$this->assertCount(3, $tests);
    }


    public function testClosureWhere()
    {
    	$tests = (new DB)->table('tests')->where(function($query){
    		$query->where('state','>=',0)->where('state','<',1000);
    	})->get();
		$this->assertCount(2, $tests);
    }


    public function testJoin()
    {
    	$tests = (new DB)->table('tests')->join('test1','tests.name','=','test1.name')->rightJoin('test2','tests.name','=','test2.name')->get();
    	$this->assertCount(2, $tests);
    }

    public function testClosureJoin()
    {
    	$tests = (new DB)->table('tests')->join('test1',function($join){
    		$join->on('tests.name','=','test1.name')->orOn('tests.name','=','test1.name');
    	})->get();
    	var_dump($tests);
    }

}

