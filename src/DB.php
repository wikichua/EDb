<?php namespace EDb;

class DB
{
	use \McCade\McCade;

	public function __construct()
	{
		$DBFluent = new DBFluent();
		$this->load($DBFluent);
	}
}