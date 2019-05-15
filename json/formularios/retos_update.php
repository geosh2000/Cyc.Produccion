<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){echo "status- DISC -status msg- Tu sesion ha expirado. Por favor da click en el menu para volver a loguearte. -msg"; exit;}
include("../../connectDB.php");
header("Content-Type: text/html;charset=utf-8");


$id=$_GET['id'];
$id_reto=$_GET['idreto'];
$tipo=$_GET['tipo'];

$field=$_GET['field'];
$order=$_GET['order'];

$newVal="'".$_GET['newVal']."'";
if($_GET['newVal']==""){$newVal="NULL";}
$nombre="'".$_GET['nombre']."'";
if($_GET['nombre']==""){$nombre="NULL";}
$descripcion="'".$_GET['descripcion']."'";
if($_GET['descripcion']==""){$descripcion="NULL";}
$inicio="'".date('Y-m-d',strtotime($_GET['inicio']))."'";
if($_GET['inicio']==""){$inicio="NULL";}
$fin="'".date('Y-m-d',strtotime($_GET['fin']))."'";
if($_GET['fin']==""){$fin="NULL";}
$status="'".$_GET['status']."'";
if($_GET['status']==""){$status="NULL";}

if($field=="Nombre" || $field=="Descripcion" || $field=="Inicio" || $field=="Fin" || $field=="Status"){
    switch($tipo){
        case "new":
            $query="INSERT INTO retos_base (Nombre, Descripcion, Inicio, Fin, Status, created_by) VALUES ($nombre,$descripcion,$inicio,$fin,$status,".$_SESSION['asesor_id'].")";
            break;
        case "update":
            $query="UPDATE retos_base SET $field=$newVal WHERE id=$id_reto";
            break;
    }

}else{
    switch($tipo){
        case "new":
            $query="INSERT INTO retos_parametros (reto_id,`order`,created_by,last_modification) VALUES ('$id_reto',$order,".$_SESSION['asesor_id'].",".$_SESSION['asesor_id'].")";
            break;
        case "update":
            $query="UPDATE retos_parametros SET $field=$newVal, last_modification=".$_SESSION['asesor_id']." WHERE id=$id";
            break;
        case "delete":
            $query="DELETE FROM retos_parametros WHERE id=$id";
            break;
    }
}




echo "$query<br>";

mysql_query($query);
$generated_id=mysql_insert_id();

if(mysql_errno()){

			    echo "status- ERROR -status msg- Error al actualizar id $id -msg ".mysql_error();

                }else{
                   echo "status- OK -status msg- Validacion Exitosa de registro $id -msg id- $generated_id -id";}

?>