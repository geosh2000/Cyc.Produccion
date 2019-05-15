<?php
include("../connectDB.php");
date_default_timezone_set("America/Bogota");
$type=$_GET['type'];
$dep=$_GET['dep'];
$hoy=date('Y-m-d');
$now=intval(date('H',strtotime('-1 hours')))*2+6;
if(intval(date('i'))>=30){$now=$now+1;}
//echo "$now<br>";

switch($dep){
    case "ventas":
        $data=  number_format($ventas);
        $label="Ventas";
        $time=20;
        break;
    case "sc":
        $data=  number_format($sc);
        $label="Servicio a Cliente";
        $time=30;
        break;
    case "corp":
        $data=  number_format($corp);
        $label="Corporativo";
        $time=30;
        break;
    case "tmp":
        $data=  number_format($tmp);
        $label="Trafico MP";
        $time=30;
        break;
    case "tmt":
        $data=  number_format($tmt);
        $label="Trafico MT";
        $time=30;
        break;
    case "ag":
        $data=  number_format($ag);
        $label="Soporte Agencias";
        $time=30;
        break;
}

$query="SELECT * FROM `Historial Llamadas` WHERE Dia='".date('d')."' AND Mes='".date('m')."' AND Anio='".date('Y')."' AND Skill='$label'";
//echo "$query<br>";
$result=mysql_query($query);
$num=mysql_numrows($result);
$id=mysql_result($result,0,'id');
$i=6;
while($i<=$now){
    if(mysql_result($result,0,$i)==NULL){$temp=0;}else{$temp=mysql_result($result,0,$i);}
     $totalcalls=$totalcalls+$temp;

$i++;
}

$tc[1]=mysql_result($result,0,($now-3));
$tc[2]=mysql_result($result,0,($now-2));
$tc[3]=mysql_result($result,0,($now-1));
$tc[4]=mysql_result($result,0,($now));

$query="SELECT * FROM `Historial Llamadas SLA` WHERE id=$id AND time='$time'";
$result=mysql_query($query);
$i=2;
while($i<=$now-4){
    if(mysql_result($result,0,$i)==NULL){$temp=0;}else{$temp=mysql_result($result,0,$i);}
     $totalsla=$totalsla+$temp;


$i++;
}

$ts[1]=mysql_result($result,0,($now-4-3));
$ts[2]=mysql_result($result,0,($now-4-2));
$ts[3]=mysql_result($result,0,($now-4-1));
$ts[4]=mysql_result($result,0,($now-4));

if($tc[1]==0){$tsla[1]=100;}else{$tsla[1]=intval(($ts[1]/$tc[1])*100);}
if($tc[1]==0){$tsla[2]=100;}else{$tsla[2]=intval(($ts[2]/$tc[2])*100);}
if($tc[1]==0){$tsla[3]=100;}else{$tsla[3]=intval(($ts[3]/$tc[3])*100);}
if($tc[1]==0){$tsla[4]=100;}else{$tsla[4]=intval(($ts[4]/$tc[4])*100);}

if(($now/2) % 2 != 0){$temp=":30";}else{$temp=":00";}
$title[1]=date('H:i',strtotime('-90 minutes'));
$title[2]=date('H:i',strtotime('-60 minutes'));
$title[3]=date('H:i',strtotime('-30 minutes'));
$title[4]=date('H:i');


if($totalcalls==0){$sla=100;}else{$sla=intval($totalsla/$totalcalls*100);}

//echo "$query<br>Now: $now<br>Llamadas: $totalcalls<br>SLA: $totalsla<br>%: $sla<br>";

//JSON

if($type==1){
$a = array();
       $cols = array();
       $rows = array();
       $cols[] = array("id"=>"","label"=>"Label","pattern"=>"","type"=>"string");
       $cols[] = array("id"=>"","label"=>"Value","pattern"=>"","type"=>"number");
       $rows[] = array("c"=>array(array("v"=>"Dia","f"=>null),array("v"=>$sla,"f"=>null)));
       $a = array("cols"=>$cols,"rows"=>$rows);
}else{
$a = array();
       $cols = array();
       $rows = array();
       $cols[] = array("id"=>"","label"=>"Label","pattern"=>"","type"=>"string");
       $cols[] = array("id"=>"","label"=>"Value","pattern"=>"","type"=>"number");
       $rows[] = array("c"=>array(array("v"=>$title[1],"f"=>null),array("v"=>$tsla[1],"f"=>null)));
       $rows[] = array("c"=>array(array("v"=>$title[2],"f"=>null),array("v"=>$tsla[2],"f"=>null)));
       $rows[] = array("c"=>array(array("v"=>$title[3],"f"=>null),array("v"=>$tsla[3],"f"=>null)));
       $rows[] = array("c"=>array(array("v"=>$title[4],"f"=>null),array("v"=>$tsla[4],"f"=>null)));
       $a = array("cols"=>$cols,"rows"=>$rows);
}

//echo "$query<br>";
//print_r($ts);
//print_r($tc);




echo  json_encode($a);



?>