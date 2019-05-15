<?php

include("../../connectDB.php");



$id=$_GET['id'];
$asesor=$_GET['asesor'];




$query="UPDATE `Historial Programacion` SET `asesor`=$asesor WHERE asesor='$id' AND Fecha>='2016-04-04' AND Fecha<='2016-07-03'";



echo "$query<br>";

mysql_query($query);

if(mysql_errno()){

			    echo "status- ERROR -status msg- Error al actualizar id $id -msg";

                }else{

                    echo "status- OK -status msg- Validacion Exitosa de registro $id -msg";}







?>