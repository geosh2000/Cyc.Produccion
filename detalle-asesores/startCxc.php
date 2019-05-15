<?php

include_once('../modules/modules.php');

session_start();

$id=$_POST['id'];
$quincenas=$_POST['quincenas'];
$monto=$_POST['monto'];
$fq=$_POST['fq'];

$connectdb=Connection::mysqliDB('CC');

$flag=true;

for($i=1;$i<=$quincenas;$i++){
    $query="INSERT INTO rrhh_pagoCxC (cxc, n_pago, quincena, monto, created_by) VALUES ($id, $i, ".($fq+($i-1)).", $monto, '".$_SESSION['asesor_id']."')";
    if($result=$connectdb->query($query)){
        $insert[]=$connectdb->insert_id;
    }else{
        $flag=false;
        $td['errores'][]=utf8_encode("ERROR -> ".$connectdb->error);
    }
}

if(!$flag){
    foreach($insert as $index => $info){
        $query="DELETE FROM rrhh_pagoCxC WHERE id=$info";
        $connectdb->query($query);
    }
    $td['status']=0;
    $td['msg']='Error en 1 o mas queries';
}else{
    IF(isset($insert)){
        $td['status']=1;
    }else{
        $td['status']=0;
        $td['msg']='No se creo ningun registro';
    }
    
    
    $query="UPDATE asesores_cxc SET status=2 WHERE id=$id";
    $connectdb->query($query);
}

$connectdb->close();

$data['status']=1;

print json_encode($td,JSON_UNESCAPED_UNICODE);