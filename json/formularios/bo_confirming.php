<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){echo "status- DISC -status msg- Tu sesion ha expirado. Por favor da click en el menu para volver a loguearte. -msg"; exit;}
$credential="asesor_formularios_bo";
$menu_asesores="class='active'";


include("../../connectDB.php");
header("Content-Type: text/html;charset=utf-8");
//GET variables
$sel_actividad=$_GET['actividad'];
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

$query="INSERT INTO bo_confirming
    (actividad, tipo_seguimiento, em, localizador, fecha_recepcion, hora_recepcion, first_call_confirmation,user)
    VALUES
    ($sel_actividad,$sel_seguimiento,$sel_caso,$sel_loc,$sel_fecha,$sel_hora,$sel_fcc,'$sel_user')";
    //echo $query;
    mysql_query($query);
    $querymsg=str_replace("\'","",$query);
    if(mysql_errno()){
			    echo "status- ERROR -status msg- Error al cargar el caso $sel_caso / $sel_actividad / $sel_fecha / $sel_hora / $sel_minuto -msg";
                }else{
                    echo "status- OK -status msg- Registro Exitoso de EM $sel_caso -msg";}

?>