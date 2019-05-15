<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){echo "status- DISC -status msg- Tu sesion ha expirado. Por favor da click en el menu para volver a loguearte. -msg"; exit;}
$credential="asesor_formularios_bo";
$menu_asesores="class='active'";


include("../../connectDB.php");
header("Content-Type: text/html;charset=utf-8");
$sel_actividad=$_POST['actividad'];
if(isset($_POST['seguimiento'])){$sel_seguimiento="'".$_POST['seguimiento']."'";}else{$sel_seguimiento="NULL";}
echo "RANGO: ".$_POST['rango'];
if($_POST['rango']!=""){
    $tmp_cases=str_replace("Caso",'',$_POST['rango']);
    //$tmp_cases=str_replace("Case",'',$tmp_cases);
    $casos=explode(' ',$tmp_cases);
    $flag=1;

}else{
    if($_POST['em']!=""){$sel_caso="'".$_POST['em']."'";}else{$sel_caso="NULL";}
}
if($_POST['localizador']!=""){$sel_loc="'".$_POST['localizador']."'";}else{$sel_loc="NULL";}
if($_POST['fcc']!=""){$sel_fcc="'".$_POST['fcc']."'";}else{$sel_fcc="NULL";}
if($_POST['fecha_asignacion']!=""){$sel_fecha="'".date('Y-m-d',strtotime($_POST['fecha_asignacion']))."'";}else{$sel_fecha="NULL";}
$sel_hora=$_POST['hr_asignacion'];
$sel_minuto=$_POST['min_asignacion'];
$hora=str_pad((int) $sel_hora,2,"0",STR_PAD_LEFT).":"
          .str_pad((int) $sel_minuto,2,"0",STR_PAD_LEFT).":00";

if($_POST['hr_asignacion']!=""){$sel_hora="'".date('H:i:s',strtotime($hora))."'";}else{$sel_hora="NULL";}

$sel_user=$_SESSION['id'];


//Query
 if($flag==1){
        foreach($casos as $key => $case){
            $tmp=str_replace(' ','',$case);
            if($tmp!=""){
            $resultados.="$tmp,";
            $query="INSERT INTO bo_mailing
            (actividad, tipo_seguimiento, em,fecha_recepcion, hora_recepcion,user)
            VALUES
            ($sel_actividad,$sel_seguimiento,$tmp,$sel_fecha,$sel_hora,'$sel_user')";
            mysql_query($query);
            if(mysql_errno()){

                     $err_count++;
			}else{ $regs_count++;}
            }
        }
        if($err_count>0){
                echo "status- ERROR -status msg- Error al cargar el caso $sel_caso / $sel_actividad / $sel_fecha / $sel_hora / $sel_minuto -msg";
        }else{
             echo "status- OK -status msg- Registro Exitoso de rango de $regs_count casos -msg";

        }
    }else{
    $query="INSERT INTO bo_mailing
    (actividad, tipo_seguimiento, em,fecha_recepcion, hora_recepcion,user)
    VALUES
    ($sel_actividad,$sel_seguimiento,$sel_caso,$sel_fecha,$sel_hora,'$sel_user')";
    mysql_query($query);
    $querymsg=str_replace("\'","",$query);
    if(mysql_errno()){
			    echo "status- ERROR -status msg- Error al cargar el caso $sel_caso / $sel_actividad / $sel_fecha / $sel_hora / $sel_minuto -msg";
                }else{
                    echo "status- OK -status msg- Registro Exitoso de EM $sel_caso -msg";}
}


?>