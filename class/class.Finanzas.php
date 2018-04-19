<?php 


/**
* 
*/
class Finanzas
{
	private $_data;

	function __construct()
	{
		$this->_data = Secure::peticionRequest();
	}

	public function ingresos()
	{
		CRUD::insert('ingresos',$_data);
	}

}