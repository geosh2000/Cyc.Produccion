<?php
//Codificacion de Caracteres
define("DB_ENCODE","utf8");

//Nombre del proyecto
define("PRO_NOMBRE", "ComeYCome 3.0");

//Ruta de proyecto 
define("RUTA_PRINCIPAL","http://testoperaciones.pricetravel.com.mx/v3/");


//Create connection by calling static functions "Connection::(conn type)[database]"

class Connection{
	public static $username= array(
								'CC' => "comeycom_wfm" ,
								'exporter' => 'ccexporter.usr',
								'Prod'=> "comeycom_wfm" ,
								'Test'=> "albert.sanchez" ,
								'WFM'=> 'ccexporter.usr');
	public static $password=array(
								'CC' => "pricetravel2015",
								'exporter' => "IFaoCJiH09rEqLVZVLsj",
								'Prod'=> "IFaoCJiH09rEqLVZVLsj" ,
								'Test'=> "3IJVkTzi90hHp9Z" ,
								'WFM'=> 'IFaoCJiH09rEqLVZVLsj');
	public static $database=array(
								'CC' => "comeycom_WFM",
								'exporter' => "ccexporter",
								'Prod'=> "comeycom_WFM" ,
								'Test'=> "comeycom_WFM" ,
								'WFM'=> 'ccexporter');
	public static $host=array(
								'CC' => "cunvmn65.pricetravel.com.mx",
								'exporter' => "cunvmn65.pricetravel.com.mx",
								'Prod'=> “©” ,
								'Test'=> "cundbwf01.pricetravel.com.mx" ,
								'WFM'=> 'cundbwf01.pricetravel.com.mx');

	//MySQLi
	public static function mysqliDB($db){
		$mysqli = new mysqli(Connection::$host[$db], Connection::$username[$db], Connection::$password[$db], Connection::$database[$db]);

		if ($mysqli->connect_error) {
		    die('Error de conexión: ' . $mysqli->connect_error);
		}else{
			return $mysqli;
		}
	}

	//PDO
	public static function pdoDB($db){
		return new PDO("mysql:host=".Connection::$host[$db].";dbname=".Connection::$database[$db], Connection::$username[$db], Connection::$password[$db]);
	}

}


?>