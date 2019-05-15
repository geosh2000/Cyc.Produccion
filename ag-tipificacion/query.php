<?php
include_once("../modules/modules.php");

header("Content-Type: text/html;charset=utf-8");

//Variables

function createPost($name){
    global $data;
    if($_POST[$name]==''){
        $data[$name]='NULL';
    }else{
        $data[$name]="'".utf8_decode(strtoupper($_POST[$name]))."'";
    }
}

createPost('asesor');
createPost('agencia');
createPost('localidad');
createPost('canal');
createPost('motivo');
createPost('tipo');
createPost('soporte');
createPost('localizador');
createPost('nombre');

//Query

$query="INSERT INTO ag_tipificacion (canal,motivo,tipo,soporte,nombre_agencia,localidad_agencia,localizador, asesor,nombre_cliente) VALUES (".$data['canal'].",".$data['motivo'].",".$data['tipo'].",".$data['soporte'].",".$data['agencia'].",".$data['localidad'].",".$data['localizador'].",".$data['asesor'].",".$data['nombre'].")";
if($result=Queries::query($query)){
	echo "status- OK -status msg- Registro Exitoso -msg";
}else{
	echo "status- ERROR -status msg- Error al Guardar Registro ".$connectdb->error." -msg";
}





?>
