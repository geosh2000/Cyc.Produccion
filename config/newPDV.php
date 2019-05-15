<?php
include("../modules/modules.php");

$connectdb=Connection::mysqliDB('CC');
timeAndRegion::setRegion('Cun');

  $timepat='/^\d\d[:]\d\d\Z/';

    //SAVE
    $pdv=utf8_decode(strtoupper($_POST['pdv']));
    $dir=utf8_decode(ucwords(strtolower($_POST['dir'])));
    $ciudad=$_POST['ciudad'];
    $corp=utf8_decode(ucwords(strtolower($_POST['corp'])));
    $nse=ucwords(strtolower($_POST['nse']));
    $tipo=strtoupper($_POST['tipo']);
    $open=strtoupper($_POST['open']);
    $hap=date('H:i:s', strtotime($_POST['hap'].":00"));
    $hc=date('H:i:s', strtotime($_POST['hc'].":00"));

    if(!preg_match($timepat,$_POST['hap']) || !preg_match($timepat,$_POST['hc'])){
      $connectdb->close();
      $td['status']=0;
      $td['msg']=utf8_encode('Los formatos de hora de apertura y/o cierre no coinciden con el formato ##:## en 24 hrs');

      echo json_encode($td,JSON_PRETTY_PRINT);

      exit;

    }


    if($_POST['pdv_rrhh']==""){
      $pdv_rrhh='NULL';
    }else{
      $pdv_rrhh="'".utf8_decode($_POST['pdv_rrhh'])."'";
    }

    IF($_POST['branch']==''){
      $branch="NULL";;
    }else{
      $branch="'".$_POST['branch']."'";
    }

    IF($_POST['tel']==''){
      $tel="NULL";;
    }else{
      $tel="'".$_POST['tel']."'";
    }

    if($_POST['ext']==""){
      $ext='NULL';
    }else{
      $ext="'".$_POST['ext']."'";
    }

    if($_POST['fap']==""){
      $fap='NULL';
    }else{
      $fap="'".date('Y-m-d', strtotime($_POST['fap']))."'";
    }

    if($_POST['fc']==""){
      $fc='NULL';
    }else{
      $fc="'".date('Y-m-d', strtotime($_POST['fc']))."'";
    }


    if($_POST['activo']=='true'){
      $activo=1;
    }else{
      $activo=0;
    }


    $query="INSERT INTO PDVs (PDV, PDV_rrhh, branchid, direccion, ciudad, corporativo, nse, tipo, extension, tel_fijo, hora_apertura, hora_cierre, dias_open, apertura, cierre, Activo)
            VALUES ('$pdv',$pdv_rrhh,$branch,'$dir',$ciudad,'$corp','$nse','$tipo',$ext,$tel,'$hap', '$hc', '$dias', $fap, $fc, $activo)";
    if($result=$connectdb->query($query)){
      $td['status']=1;
      $td['msg']=utf8_encode('Registro Exitoso');

    }else{
      $flag=false;
      $td['status']=0;
      $td['msg']=utf8_encode("Error -> ".$connectdb->error." ON $query");
    }


$connectdb->close();

echo json_encode($td,JSON_PRETTY_PRINT);

?>
