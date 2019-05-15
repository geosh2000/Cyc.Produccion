<?php

include_once('../modules/modules.php');

$connectdb=Connection::mysqliDB('CC');

$flag=true;

foreach($_POST as $var => $info){
  if($var!='undefined'){

    if($info==""){
      @$variables.="NULL, ";
    }else{
      if($var=='cita'){
        @$variables.="'".str_replace("10:00:00","00:00:00",$info)."', ";
      }else{
        @$variables.="'$info', ";
      }
    }
    
    if(($var=='nombre' OR $var=='localizador') AND $info==''){
      $flag=false;
    }
    
        
    @$campos.="$var, ";
  }
}

$campos=substr($campos,0,-2);
$variables=substr($variables,0,-2);

if(isset($_POST['nombre']) && $flag){
  $query="INSERT INTO outlet_citas ($campos) VALUES ($variables)";
  if($result=$connectdb->query($query)){
    $data['status']=1;
    $data['id']=$connectdb->insert_id;
  }else{
    $data['status']=0;
    $data['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
  }
}else{
  $data['status']=0;
  $data['msg']=utf8_encode("No se recibió correctamente la DATA || $variables");
}

echo json_encode($data,JSON_PRETTY_PRINT);

$connectdb->close();
?>