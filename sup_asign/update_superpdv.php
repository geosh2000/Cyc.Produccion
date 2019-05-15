<?php
include_once("../modules/modules.php");

session_start();

$connectdb=Connection::mysqliDB('CC');

$asesor=$_POST['asesor'];
$super="'".$_POST['super']."'";
$fecha=date('Y-m-d', strtotime($_POST['fecha']));
$pcrc=$_POST['dep'];
$user=$_SESSION['asesor_id'];

if($super=="'undefined'" || $super=="''"){
    $super="NULL";
}

$query="INSERT INTO supervisores_pdv (Fecha,pdv,supervisor,user) VALUES
    ('$fecha','$asesor',$super,'$user')";
$update="UPDATE supervisores_pdv SET supervisor=$super, user='$user'
    WHERE pdv='$asesor' AND Fecha='$fecha'";

 if($result=$connectdb->query($query)){
  $data['status']=1;
 }else{
   if($connectdb->errno==1062){
     if($result=$connectdb->query($update)){
      $data['status']=1;
     }else{
       $data['status']=0;
       $data['msg']="ERROR! -> ".$connectdb->error." ON $query";
     }
   }else{
     $data['status']=0;
     $data['msg']="ERROR! -> ".$connectdb->error." ON $query";
   }
 }

 $connectdb->close();

print json_encode($data,JSON_PRETTY_PRINT);


?>
