<?php

//Create connection by calling static functions "Connection::(conn type)[database]"

class Connection{
	public static $username= array(
								'CC' => "comeycom_wfm" , 
								'WFM' => 'ccexporter.usr');
	public static $password=array(
								'CC' => "pricetravel2015", 
								'WFM' => "IFaoCJiH09rEqLVZVLsj");
	public static $database=array(
								'CC' => "comeycom_WFM", 
								'WFM' => "ccexporter");
	public static $host=array(
								'CC' => "cundbwf01.pricetravel.com.mx", 
								'WFM' => "cundbwf01.pricetravel.com.mx");
	
	//MySQLi
	public static function mysqliDB($db){
		return new mysqli(Connection::$host[$db], Connection::$username[$db], Connection::$password[$db], Connection::$database[$db]);
	}
	
	//PDO
	public static function pdoDB($db){
		return new PDO("mysql:host=".Connection::$host[$db].";dbname=".Connection::$database[$db], Connection::$username[$db], Connection::$password[$db]);
	}
	
}


?>
