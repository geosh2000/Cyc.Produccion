<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){echo "status- DISC -status msg- Tu sesion ha expirado. Por favor da click en el menu para volver a loguearte. -msg"; exit;}

include("../connectDB.php");
header("Content-Type: text/html;charset=utf-8");

//Variables

function createPost($name){
    global $data;
    if($_POST[$name]==''){
        $data[$name]='NULL';
    }else{
        $data[$name]="'".utf8_decode($_POST[$name])."'";
    }
}

foreach($_POST as $key => $info){
	createPost($key);
}


//Query

$query="INSERT INTO "
				."trfMP_funciones (";
				foreach($_POST as $key => $info){
					$query.=str_replace("f_", "", $key).",";
				}
				$query=substr($query,0,-1).") VALUES (";
				foreach($_POST as $key => $info){
					$query.=$data[$key].",";
				}
				$query=substr($query,0,-1).")";

mysql_query($query);
	
if(mysql_errno()){
	echo "status- ERROR -status msg- ERROR al Guardar Registro(s) ".mysql_error()." -msg";
}else{
    echo "status- OK -status msg- Registro(s) Exitoso(s) -msg";
}





?>