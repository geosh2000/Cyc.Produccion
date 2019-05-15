<?php
include_once("../modules/modules.php");

$module=$_GET['module'];


$moduleget=Connection::mysqliDB('CC');

if(isset($_GET['module'])){
	$query="SELECT path, modulename FROM modules WHERE module='$module'";
	if($result=$moduleget->query($query)){
		$fila=$result->fetch_assoc();
		define(MODULE_PATH, $fila['path']);
		include_once("..".MODULE_PATH.$fila['modulename']);
	}else{
		header('Location: '.$_SERVER['HTTP_REFERER']);
	}
}else{
	define(MODULE_PATH, '/start/');
	include_once("..".MODULE_PATH."main.php");
}

$moduleget->close();

?>


