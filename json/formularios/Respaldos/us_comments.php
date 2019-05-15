<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){echo "status- DISC -status msg- Tu sesion ha expirado. Por favor da click en el menu para volver a loguearte. -msg"; exit;}
$credential="asesor_formulario_us";
$menu_asesores="class='active'";


include("../../connectDB.php");
header("Content-Type: text/html;charset=utf-8");
//GET variables
$sel_com=$_GET['comment'];
if(isset($_GET['seguimiento'])){$sel_seguimiento="'".$_GET['seguimiento']."'";}else{$sel_seguimiento="NULL";}
if($_GET['em']!=""){$sel_caso="'".$_GET['em']."'";}else{$sel_caso="NULL";}
if($_GET['localizador']!=""){$sel_loc="'".$_GET['localizador']."'";}else{$sel_loc="NULL";}
if($_GET['fcc']!=""){$sel_fcc="'".$_GET['fcc']."'";}else{$sel_fcc="NULL";}
if($_GET['fecha_asignacion']!=""){$sel_fecha="'".date('Y-m-d',strtotime($_GET['fecha_asignacion']))."'";}else{$sel_fecha="NULL";}
$sel_hora=$_GET['hr_asignacion'];
$sel_minuto=$_GET['min_asignacion'];
$hora=str_pad((int) $sel_hora,2,"0",STR_PAD_LEFT).":"
          .str_pad((int) $sel_minuto,2,"0",STR_PAD_LEFT).":00";

if($_GET['hr_asignacion']!=""){$sel_hora="'".date('H:i:s',strtotime($hora))."'";}else{$sel_hora="NULL";}
$sel_user=$_SESSION['id'];

$query="INSERT INTO us_comentarios
    (localizador, comentario, user)
    VALUES
    ($sel_loc,'$sel_com','$sel_user')";
    //echo $query;
    mysql_query($query);
    $querymsg=str_replace("\'","",$query);
    if(mysql_errno()){
			    echo "status- ERROR -status msg- Error al cargar el Localizador $sel_loc -msg";
                }else{
                    echo "status- OK -status msg- Registro Exitoso de Localizador $sel_loc -msg";}

?>