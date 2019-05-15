<?php
ini_set('session.gc_maxlifetime', 28800);
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

createPost('asesor');
createPost('pdv');
createPost('localizador');
createPost('resolucion');
createPost('objecion');
createPost('newloc');

//Servicios
function convertServ($name){
	global $data;
	if($data[$name]=="'false'"){
		$data[$name]='NULL';
	}else{
		$data[$name]=1;
	}
}




//Query

$query="INSERT INTO pdv_registro_llamadas ("
		."asesor,PDV,Localizador,"
		."Resolucion, New_Loc, Objecion) VALUES ("
		.$data['asesor'].","
		.$data['pdv'].","
		.$data['localizador'].","
		.$data['resolucion'].","
		.$data['newloc'].","
		.$data['objecion'].")";
mysql_query($query);

if(mysql_errno()){
    echo "status- ERROR -status msg- Error al Guardar Registro ".mysql_error()." -msg <br> $query";
}else{
    $query="SELECT COUNT(id) as Registros FROM pdv_registro_llamadas WHERE CAST(Last_Update as DATE)=CURDATE() AND asesor=".$data['asesor'];
    $result=mysql_query($query);
    echo "regs- ".mysql_result($result,0,'Registros')." -regs ";
    echo "status- OK -status msg- Registro Exitoso -msg";
}



?>