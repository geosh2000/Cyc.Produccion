<?php
include("../modules/modules.php");

session_start();

if(!isset($_SESSION['id'])){
  
  $td['status']=0;
  $td['msg']=utf8_encode('Sesion expirada, por favor inicia sesion nuevamente');
  
  echo json_encode($td,JSON_PRETTY_PRINT);
  
  exit;
}

$connectdb=Connection::mysqliDB('CC');
timeAndRegion::setRegion('Cun');

  $timepat='/^\d\d[:]\d\d\Z/';

    //SAVE
    $cantidad=$_POST['cantidad'];
    $ciudad=$_POST['ciudad'];
    $dep=$_POST['departamento'];
    $puesto=$_POST['puesto'];
    $esquema=$_POST['esquema'];
    $inicio="'".date('Y-m-d',strtotime($_POST['inicio']))."'";


    if($_POST['pdv']==""){
      $pdv='NULL';
    }else{
      $pdv="'".utf8_decode($_POST['pdv'])."'";
    }

    if($_POST['fin']!="" && $_POST['fin']!="NULL"){
			$fin="'".date('Y-m-d',strtotime($_POST['fin']))."'";
		}else{
			$fin="'2030-01-01'";
		}

    if($_POST['activo']=='true'){
      //$activo=1;
      $activo=0;
    }else{
      $activo=0;
    }

$regs=0;
$errs=0;

for($i=1;$i<=$cantidad;$i++){
    $query="INSERT INTO asesores_plazas (departamento, puesto, oficina, ciudad, inicio, fin, activo, esquema, created_by)
            VALUES ($dep,$puesto,$pdv,$ciudad,$inicio,$fin, 1, $esquema, ".$_SESSION['asesor_id'].")";
    if($result=$connectdb->query($query)){

      $inserted=$connectdb->insert_id;

      $query="INSERT INTO asesores_movimiento_vacantes (vacante, fecha_out) VALUES ($inserted, $inicio)";
      if($result=$connectdb->query($query)){
        $regs++;
        $td['status']=1;
        $td['msg']=utf8_encode("Registro(s) Exitoso(s): $regs || Registro(s) con error: $errs");
      }else{
        $errs++;
        $flag=false;
        $td['status']=0;
        $td['msg']=utf8_encode("Registro(s) Exitoso(s): $regs || Registro(s) con error: $errs || Error -> ".$connectdb->error." ON $query");
      }
    }else{
      $errs++;
      $flag=false;
      $td['status']=0;
      $td['msg']=utf8_encode("Registro(s) Exitoso(s): $regs || Registro(s) con error: $errs || Error -> ".$connectdb->error." ON $query");
    }
}


$connectdb->close();

echo json_encode($td,JSON_PRETTY_PRINT);

if($td['status']==1){
  if($_POST['nomail']==1){
  
  }else{
    include_once("../mailing/vacantes.php");
  }
}

?>
