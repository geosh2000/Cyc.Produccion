<?php
include_once('../modules/modules.php');

session_start();

$connectdb=Connection::mysqliDB('CC');

function postData($info, $title){
  global $data, $_POST;

  if($_POST[$info]==''){
    $data[$title]='NULL';
  }else{
    if(substr($title,0,5)=='fecha'){
      $data[$title]="'".date('Y-m-d',strtotime($_POST[$info]))."'";
    }else{
      $data[$title]="'".utf8_decode($_POST[$info])."'";
    }
  }

}

postData('asesor','asesor');
postData('loc','loc');
postData('monto','monto');
postData('f_cxc','fecha_cxc');
postData('f_ap','fecha_ap');
postData('comments','comments');
postData('status','status');
postData('tipo','tipo');

if($_POST['firmado']=='true'){
  $firmado=1;
}else{
  $firmado=0;
}

$user=$_SESSION['asesor_id'];

if(!isset($_SESSION['asesor_id'])){
  $td['status']=0;
  $td['msg']=utf8_encode("ERROR! -> Sesión Expidada. Por favor vuelve a iniciar sesión");
}else{

  $query="INSERT asesores_cxc (asesor,localizador,monto,fecha_cxc,fecha_aplicacion,firmado,comments,created_by,tipo,status) VALUES (".$data['asesor'].",".$data['loc'].",".$data['monto'].",".$data['fecha_cxc'].",".$data['fecha_ap'].",$firmado,".$data['comments'].",$user,".$data['tipo'].",".$data['status'].")";
  if($resultado=$connectdb->query($query)){
    $td['status']=1;
  }else{
    $td['status']=0;
    $td['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
  }

}

$connectdb->close();

//Print JSON
print json_encode($td,JSON_UNESCAPED_UNICODE);
 ?>
