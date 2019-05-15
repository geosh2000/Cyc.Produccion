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

createPost('asesor');
createPost('area');
createPost('canal');
createPost('localizador');
createPost('motivo');
createPost('nombre');
createPost('tel');
createPost('tipo');



//Query

switch($_POST['area']){
	case 'llamadas':
		$query="INSERT INTO "
				."trfMP_tipificacion "
				."(asesor,canal,motivo,tipo_reserva,nombre,telefono, localizador) VALUES ("
				.$data['asesor'].","
				.$data['canal'].","
				.$data['motivo'].","
				.$data['tipo'].","
				.$data['nombre'].","
				.$data['tel'].","
				.$data['localizador']
				.")";
		break;
	case 'funciones':
		
		break;
}

mysql_query($query);
	
if(mysql_errno()){
	echo "status- ERROR -status msg- Error al Guardar Registro(s) ".mysql_error()." on $query -msg";
}else{
    echo "status- OK -status msg- Registro(s) Exitoso(s) -msg";
}





?>