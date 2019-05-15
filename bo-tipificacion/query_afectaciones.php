<?php

include_once('../modules/modules.php');

$table=$_POST['tabla'];

$arreglo=explode('|',substr(utf8_decode($_POST['datos']),0,-1));

foreach($arreglo as $index => $info){
  $data[substr($info,0,strpos($info,':'))]="'".substr($info,strpos($info,':')+1,100)."'";
  $rows.=substr($info,0,strpos($info,':')).",";
  $values.="'".substr($info,strpos($info,':')+1,100)."',";
}

$rows=substr($rows,0,-1);
$values=substr($values,0,-1);

$connectdb=Connection::mysqliDB('CC');

session_start();

$query="INSERT INTO $table ($rows) VALUES ($values)";

$td['query']=utf8_encode($query);

if($result=$connectdb->query($query)){
  $td['status']=1;
}else{
  $td['status']=0;
  $td['msg']="ERROR! -> ".$connectdb->error." ON ".utf8_encode($query);
}

$query="SELECT id FROM bo_status WHERE area=9 AND sec_id=".$data['status'];
if($result=$connectdb->query($query)){
   $fila=$result->fetch_assoc();
    $status=$fila['id'];
}


if($table=="bo_afectaciones_funciones"){
    $query="INSERT INTO bo_tipificacion 
            (area,asesor,fecha_recepcion,em,status,internal_id) VALUES 
            (9,"
                    .$_SESSION['asesor_id'].","
                    .$data['fecha_recepcion'].","
                    .$data['caso'].","
                    .$status.","
                    ."insertBOID(".$data['caso'].",9))";
    if($result=$connectdb->query($query)){
      $td['status']=1;
      $td['msg']=utf8_encode("Error! -> ".$connectdb->error." ON $query");
    }else{
      $td['status']=0;
      $td['msg']=utf8_encode("Error! -> ".$connectdb->error." ON $query");
    }
}


$query="SELECT COUNT(*) as Regs, MAX(date_created) as Last FROM $table WHERE CAST(date_created as DATE)=CURDATE() AND asesor=".$data['asesor'];

$td['q2']=utf8_encode($query);
if($result=$connectdb->query($query)){
  $fila=$result->fetch_assoc();
  $td['regs']=$fila['Regs'];
  $td['lu']=$fila['Last'];
}


echo json_encode($td);

$connectdb->close();