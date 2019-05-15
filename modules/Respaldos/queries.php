<?php

class Queries{

	public static function query($query){
		$connectdb=Connection::mysqliDB('CC');
		if($result=$connectdb->query($query)){
			$connectdb->close();
			return $result;
		}else{
			$connectdb->close();
			return false;
		}
	}

	public static function error($query){
		$connectdb=Connection::mysqliDB('CC');
		if(!$result=$connectdb->query($query)){
			$error=$connectdb->error;
			$connectdb->close();
			return $error;
		}
	}
}



?>
