<?php
include("../connectDB.php");
session_start();
$asesor=$_GET['asesor'];
$super="'".$_GET['super']."'";
$fecha=date('Y-m-d', strtotime($_GET['fecha']));
$pcrc=$_GET['dep'];
$user=$_SESSION['id'];

if($super=="'undefined'"){
    $super="NULL";
}

$query="INSERT INTO Supervisores (Fecha,asesor,supervisor,user) VALUES
    ('$fecha','$asesor',$super,'$user')";
$update="UPDATE Supervisores SET supervisor=$super, user='$user'
    WHERE asesor='$asesor' AND Fecha='$fecha'";

mysql_query($query);
if(mysql_errno()){
    mysql_query($update);
    if(mysql_errno()){echo "status- ERROR -status msg- Error al actualizar Supervisor -msg";}else{
        echo "status- OK -status msg- Supervisor Actualizado -msg";
    }
}else{
echo "status- OK -status msg- Supervisor Asignado -msg";
}


?>