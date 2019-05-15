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
createPost('canal');
createPost('destinos1');
createPost('destinos2');
createPost('destinos3');
createPost('destinos4');
createPost('cotizaciones');
createPost('localizador');
createPost('status');
createPost('ncompra');

//Query

$query="INSERT INTO ventas_tipificacion (asesor,canal,destinos_solicitados1,destinos_solicitados2,destinos_solicitados3,destinos_solicitados4,cotizaciones,localizador,motivo_no_compra) VALUES (".$data['asesor'].",".$data['canal'].",".$data['destinos1'].",".$data['destinos2'].",".$data['destinos3'].",".$data['destinos4'].",".$data['cotizaciones'].",".$data['localizador'].",".$data['ncompra'].")";
mysql_query($query);

if(mysql_errno()){
    echo "status- ERROR -status msg- Error al Guardar Registro ".mysql_error()." -msg";
}else{
    echo "status- OK -status msg- Registro Exitoso -msg";
}



?>