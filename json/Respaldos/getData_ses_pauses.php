<?
include("../DBCallsHoraV.php");
include("../connectDB.php");
include("../common/erlangC.php");
date_default_timezone_set('America/Bogota');
$mxzone = new DateTimeZone('America/Mexico_City');
$cunzone = new DateTimeZone('America/Bogota');

function tzMextoCun($time,$date=NULL){
    global $mxzone, $cunzone;
    $horamx= new DateTime("$date $time America/Mexico_City");
    $mxtime= $horamx->format('H:i:s');
    $mxdate= $horamx->format('Y-m-d');
    $horamx->setTimezone($cunzone);
    $cuntime=$horamx->format('H:i:s');
    $cundate= $horamx->format('Y-m-d');

    return array ('mxtime'=>$mxtime, 'mxdate'=>$mxdate, 'cuntime'=>$cuntime, 'cundate'=>$cundate);
}

if($_GET['date']==NULL){$date=date('Y/m/d');}else{$date=date('Y/m/d',strtotime($_GET['date']));}
$user=$_GET['usuario'];

$query="SELECT * FROM userDB a, Asesores b WHERE a.username=b.Usuario AND userid='$user'";
$asesor=mysql_result(mysql_query($query),0,'id');

//get horario
$query="SELECT * FROM `Historial Programacion` WHERE (Fecha='$date' AND asesor='$asesor')";
$result=mysql_query($query);
$num=mysql_numrows($result);
if($num!=0){
    $jstart=mysql_result($result,0,'jornada start');
    $jend=mysql_result($result,0,'jornada end');
    $d_s=date('d');
    $m_s=date('m');
    $y_s=date('Y');
    if(date('H',strtotime($jend))<4){
        $d_e=date('d',strtotime('+1 days'));
        $m_e=date('m',strtotime('+1 days'));
        $y_e=date('Y',strtotime('+1 days'));
    }else{
        $d_e=date('d');
        $m_e=date('m');
        $y_e=date('Y');
    }
    $cstart=mysql_result($result,0,'comida start');
    $cend=mysql_result($result,0,'comida end');
    $h_js=date('H', strtotime($jstart));
    $m_js=date('i', strtotime($jstart));
    $s_js=date('s', strtotime($jstart));
    $h_je=date('H', strtotime($jend));
    $m_je=date('i', strtotime($jend));
    $s_je=date('s', strtotime($jend));
    $h_cs=date('H', strtotime($cstart));
    $m_cs=date('i', strtotime($cstart));
    $s_cs=date('s', strtotime($cstart));
    $h_ce=date('H', strtotime($cend));
    $m_ce=date('i', strtotime($cend));
    $s_ce=date('s', strtotime($cend));
}

$queryP="SELECT
            *
            FROM
                Comidas a, Tipos_pausas b
            WHERE
                a.tipo=b.pausa_id AND
                Fecha='$date' AND
                asesor='$asesor'";

$resultP=mysql_query($queryP);
$numP=mysql_numrows($resultP);
//echo "$queryT<br>$queryF<br><br>";

$i=0;
while($i<$numP){
    $inicio[$i]=mysql_result($resultP,$i,'Inicio');
    $fin[$i]=mysql_result($resultP,$i,'Fin');
    $temp=tzMextoCun($inicio[$i]); $inicio[$i]=$temp['cuntime'];
    $temp=tzMextoCun($fin[$i]); $fin[$i]=$temp['cuntime'];
    $h_pi[$i]=date('H',strtotime($inicio[$i]));
    $m_pi[$i]=date('i',strtotime($inicio[$i]));
    $s_pi[$i]=date('s',strtotime($inicio[$i]));
    $h_pf[$i]=date('H',strtotime($fin[$i]));
    $m_pf[$i]=date('i',strtotime($fin[$i]));
    $s_pf[$i]=date('s',strtotime($fin[$i]));
    $tipo[$i]=mysql_result($resultP,$i,'Pausa');
$i++;
}

if(intval(date('H'))<4){
    $querySS="SELECT MIN(Hora) as Hora FROM Sesiones WHERE asesor='$asesor' AND Hora>'17:00:00' AND Fecha='".date('Y-m-d',strtotime('-1 days'))."'";
    $querySE="SELECT MAX(Hora_out) as Hora FROM Sesiones WHERE asesor='$asesor' AND Fecha='".date('Y-m-d')."'";
}else{
    $querySS="SELECT MIN(Hora) as Hora FROM Sesiones WHERE asesor='$asesor' AND Hora>'04:00:00' AND Fecha='".date('Y-m-d')."'";
    $querySE="SELECT MAX(Hora_out) as Hora FROM Sesiones WHERE asesor='$asesor' AND Fecha='".date('Y-m-d')."'";
}
//echo "$querySS<br>$querySE<br>";
$resultSS=mysql_query($querySS);
$numSS=mysql_numrows($resultSS);
$resultSE=mysql_query($querySE);
if(mysql_numrows($resultSS)!=0){
    $sstart=mysql_result($resultSS,0,'Hora');
    $send=mysql_result($resultSE,0,'Hora');
    $temp=tzMextoCun($sstart); $sstart=$temp['cuntime'];
    $temp=tzMextoCun($send); $send=$temp['cuntime'];
    $h_ss=date('H', strtotime($sstart));
    $m_ss=date('i', strtotime($sstart));
    $s_ss=date('s', strtotime($sstart));
    $h_se=date('H', strtotime($send));
    $m_se=date('i', strtotime($send));
    $s_se=date('s', strtotime($send));
}

//echo "$queryT<br>$queryF<br><br>";




//JSON
$a = array();
       $cols = array();
       $rows = array();
       $cols[] = array("id"=>"Sesion","label"=>"Sesion","pattern"=>"","type"=>"string");
       $cols[] = array("id"=>"Tipo","label"=>"Tipo","pattern"=>"","type"=>"string");
       $cols[] = array("id"=>"Start","label"=>"Start","pattern"=>"","type"=>"date");
       $cols[] = array("id"=>"End","label"=>"End %","pattern"=>"","type"=>"date");
       $rows[] = array("c"=>array(array("v"=>"Horario","f"=>null),array("v"=>"Horario","f"=>null),array("v"=>"Date($y_s,$m_s,$d_s,$h_js,$m_js,$s_js)","f"=>null),array("v"=>"Date($y_e,$m_e,$d_e,$h_je,$m_je,$s_je)","f"=>null)));
        if(mysql_result($resultSS,0,'Hora')!=NULL){
            $rows[] = array("c"=>array(array("v"=>"Sesion","f"=>null),array("v"=>"Logueo","f"=>null),array("v"=>"Date($y_s,$m_s,$d_s,$h_ss,$m_ss,$s_ss)","f"=>null),array("v"=>"Date($y_s,$m_s,$d_s,$h_se,$m_se,$s_se)","f"=>null)));
        }
        if($h_cs==0 && $h_ce==0){}else{
        $rows[] = array("c"=>array(array("v"=>"Horario","f"=>null),array("v"=>"Horario Alimentos","f"=>null),array("v"=>"Date($y_s,$m_s,$d_s,$h_cs,$m_cs,$s_cs)","f"=>null),array("v"=>"Date($y_s,$m_s,$d_s,$h_ce,$m_ce,$s_ce)","f"=>null)));
        }


if($numP!=0){
       foreach($tipo as $key => $type){
           if($h_pi[$key]>7 && date('H')<4){
               $day_pi=date('d',strtotime('-1 days'));
               $year_pi=date('Y',strtotime('-1 days'));
               $month_pi=date('m',strtotime('-1 days'));
           }else{
                $day_pi=date('d');
               $year_pi=date('Y');
               $month_pi=date('m');
           }
           if($h_pf[$key]>7 && date('H')<4){
               $day_pf=date('d',strtotime('-1 days'));
               $year_pf=date('Y',strtotime('-1 days'));
               $month_pf=date('m',strtotime('-1 days'));
           }else{
                $day_pf=date('d');
               $year_pf=date('Y');
               $month_pf=date('m');
           }
           $rows[] = array("c"=>array(array("v"=>"Sesion","f"=>null),array("v"=>"$tipo[$key]","f"=>null),array("v"=>"Date($year_pi,$month_pi,$day_pi,$h_pi[$key],$m_pi[$key],$s_pi[$key])","f"=>null),array("v"=>"Date($year_pf,$month_pf,$day_pf,$h_pf[$key],$m_pf[$key],$s_pf[$key])","f"=>null)));
       }
}

       $a = array("cols"=>$cols,"rows"=>$rows);







echo  json_encode($a);

//echo"<br>$sstart";







?>