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
createPost('base');
createPost('nombre');
createPost('telefono');
createPost('localizador');
createPost('destino');
createPost('original');
createPost('motivo');
createPost('acompanantes');
createPost('auto');
createPost('certificado');
createPost('tour');
createPost('traslado');
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

convertServ('auto');
convertServ('certificado');
convertServ('tour');
convertServ('traslado');



//Query

$query="INSERT INTO us_servicios_adicionales ("
		."asesor,Base,Nombre_Cliente,Telefono_Cliente,Localizador,Destino,Servicio_Original,Motivo_Viaje,Cliente_Viaja,"
		."Servicio_ofertado_Auto,Servicio_ofertado_Certificado,Servicio_ofertado_Tour,Servicio_ofertado_Traslado,"
		."Resolucion, New_Loc, Objecion) VALUES ("
		.$data['asesor'].","
		.$data['base'].","
		.$data['nombre'].","
		.$data['telefono'].","
		.$data['localizador'].","
		.$data['destino'].","
		.$data['original'].","
		.$data['motivo'].","
		.$data['acompanantes'].","
		.$data['auto'].","
		.$data['certificado'].","
		.$data['tour'].","
		.$data['traslado'].","
		.$data['resolucion'].","
		.$data['newloc'].","
		.$data['objecion'].")";
mysql_query($query);

if(mysql_errno()){
    echo "status- ERROR -status msg- Error al Guardar Registro ".mysql_error()." -msg <br> $query";
}else{
    $query="SELECT COUNT(id) as Registros FROM us_servicios_adicionales WHERE CAST(Last_Update as DATE)=CURDATE() AND asesor=".$data['asesor'];
    $result=mysql_query($query);
    echo "regs- ".mysql_result($result,0,'Registros')." -regs ";
    echo "status- OK -status msg- Registro Exitoso -msg";
}



?>