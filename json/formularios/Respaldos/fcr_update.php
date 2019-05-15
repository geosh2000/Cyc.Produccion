<?php

include("../../connectDB.php");



$id=$_GET['id'];

$field=$_GET['field'];

$newVal="'".$_GET['newVal']."'";
if($_GET['newVal']==""){$newVal="NULL";}

if($field=='fcr'){
    $query="UPDATE fcr SET `$field`=$newVal, motivo=NULL WHERE id='$id'";
}else{
    $query="UPDATE fcr SET `$field`=$newVal WHERE id='$id'";
}


echo "$query<br>";

mysql_query($query);

if(mysql_errno()){

			    echo "status- ERROR -status msg- Error al actualizar id $id -msg";

                }else{
                    if($field=="Activo"){$query="UPDATE userDB SET active='$nweval' WHERE asesor_id=$id"; mysql_query($query);}
                    echo "status- OK -status msg- Registro Exitoso -msg";}







?>