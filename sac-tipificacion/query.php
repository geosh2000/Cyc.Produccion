<?php
include_once("../modules/modules.php");

timeAndRegion::setRegion('Cun');

ini_set('session.gc_maxlifetime', 28800);
session_start();

$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){echo "status- DISC -status msg- Tu sesion ha expirado. Por favor da click en el menu para volver a loguearte. -msg"; exit;}

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

createPost('canal');
createPost('producto');
createPost('localizador');
createPost('mg');
createPost('me');
createPost('detalle');
createPost('asesor');
createPost('otro');

$connectdb=Connection::mysqliDB('CC');

//Query
$query="INSERT INTO sac_tipificacion (canal,producto,motivo_general,motivo_especifico,detalle,asesor,otro,localizador) VALUES (".$data['canal'].",".$data['producto'].",".$data['mg'].",".$data['me'].",".$data['detalle'].",".$data['asesor'].",".$data['otro'].",".$data['localizador'].")";
if($result=$connectdb->query($query)){
  $id=$connectdb->insert_id;
  $query="SELECT a.Fecha, a.asesor, a.Calls as llamadas, b.Registros  FROM
            (SELECT * FROM d_PorCola WHERE asesor=".$data['asesor']." AND Fecha=CURDATE() AND Skill=4 ) a
            LEFT JOIN
            (SELECT CAST(Last_Update as DATE) as Fecha, COUNT(id) as Registros, asesor FROM sac_tipificacion WHERE CAST(Last_Update as DATE)=CURDATE() AND asesor=".$data['asesor'].") b
            ON a.Fecha=b.Fecha AND a.asesor=b.asesor";
  if($resultado=$connectdb->query($query)){
    $fila=$resultado->fetch_assoc();
    $regs=$fila['Registros'];
	  $calls=$fila['llamadas'];
  }

  $query="SELECT Last_Update as Fecha, Localizador FROM sac_tipificacion WHERE id=$id";
  if($resultado=$connectdb->query($query)){
    $fila=$resultado->fetch_assoc();
    $lr_reg=$fila['Fecha'];
  	$lr_loc=$fila['Localizador'];
  }

  echo "regs- $regs -regs calls- $calls -calls lrreg- $lr_reg -lrreg lrloc- $lr_loc -lrloc";
  echo "status- OK -status msg- Registro Exitoso -msg";
}else{
  echo "status- ERROR -status msg- Error al Guardar Registro ".$connectdb->error." -msg";
}

$connectdb->close();


?>
