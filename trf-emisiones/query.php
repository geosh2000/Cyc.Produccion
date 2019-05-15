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

createPost('gds');
createPost('pnr');
createPost('datec');
createPost('tipo');
$data['datec']="'".date('Y-m-d H:i:s',strtotime($_POST['datec'].':00'))."'";
createPost('status');
createPost('asesor');



//Query

$query="INSERT INTO trfMT_emisiones (GDS,date_recieved,PNR,status,asesor,tipo) VALUES (".$data['gds'].",".$data['datec'].",".$data['pnr'].",".$data['status'].",".$data['asesor'].",".$data['tipo'].")";
mysql_query($query);

if(mysql_errno()){
    echo "status- ERROR -status msg- Error al Guardar Registro ".mysql_error()." -msg";
}else{
    echo "status- OK -status msg- Registro Exitoso -msg";
}



?>