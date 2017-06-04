<?php 

namespace App\Libraries;

use Phalcon\Db\Adapter\Pdo\Mysql;

class ConfigDatabase {

	public static function db(){
		
		return new Mysql([
		  'host'     => 'localhost',
		  'username' => 'root',
		  'password' => '',
		  'dbname'   => 'control_expedientes'		 
	  	]);	  
	}


}