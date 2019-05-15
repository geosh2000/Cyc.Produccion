<?php
include("DBCallsHoraV.php");
include("DBHistorialLlamadas.php");
include("DBForecastLlamadas.php");
include("connectDB.php");

$day=$_GET['d'];
$month=$_GET['m'];
$year=$_GET['y'];
$skill=$_GET['skill'];

if($month==1){
	$m=12;}else{
	$m=$month-1;
	}

switch ($m){
	case 1:
	case 3:
	case 5:
	case 7:
	case 8:
	case 10:
	case 12:
		$dias=31;
		break;
	case 2:
		if ($year==2016 || $year==2020){
		$dias=29;}else{$dias=28;}
		break;
	default:
		$dias=30;
		break;
}

$dy=$day-1;
$my=$month;
$yy=$year;
$dlw=$day-7;
$mlw=$month;
$ylw=$year;
if ($dy<1) { 
	$dy=$dias+$dy;
	$my=$my-1;
	if ($my<1){
		$my=12;
		$yy=$yy-1;
	}
}
if ($dlw<1) { 
	$dlw=$dias+$dlw;
	$mlw=$mlw-1;
	if ($mlw<1){
		$mlw=12;
		$ylw=$ylw-1;
	}
}



//get ids
$queryT="SELECT * FROM `Historial Llamadas` WHERE (Dia='$day' AND Mes='$month' AND Anio='$year' AND Skill='$skill')";
$queryY="SELECT * FROM `Historial Llamadas` WHERE (Dia='$dy' AND Mes='$my' AND Anio='$yy' AND Skill='$skill')";
$queryLW="SELECT * FROM `Historial Llamadas` WHERE (Dia='$dlw' AND Mes='$mlw' AND Anio='$ylw' AND Skill='$skill')";
$queryF="SELECT * FROM `Historial Llamadas Forecast` WHERE `id`='$td')";
$resultT=mysql_query($queryT);
$resultY=mysql_query($queryY);
$resultLW=mysql_query($queryLW);
$resultF=mysql_query($queryF);

$td=mysql_result($resultT,0,"id");
$yd=mysql_result($resultY,0,"id");
$lwd=mysql_result($resultLW,0,"id");
$fd=$td;


//JSON
$a = array();
       $cols = array();
       $rows = array();
       $cols[] = array("id"=>"","label"=>"Hora","pattern"=>"","type"=>"number");
       $cols[] = array("id"=>"","label"=>"Today","pattern"=>"","type"=>"number"); 

       $cols[] = array("id"=>"","label"=>"Forecast","pattern"=>"","type"=>"number"); 
       $cols[] = array("id"=>"","label"=>"Precision %","pattern"=>"","type"=>"number"); 
       $cols[] = array("id"=>"","label"=>"Top H Prec.","pattern"=>"","type"=>"number"); 
       $cols[] = array("id"=>"","label"=>"Top L Prec.","pattern"=>"","type"=>"number");  
       $i=1;
       while ($i<=48){
       	switch ($i){
		case 47:
			
			$forecast= $FLLc[$fd][1];
			if ($FLLc[$fd][1]!=0){
			$pres[$i]=($HLLc[$td][$i]/$FLLc[$fd][1]);
			}else{$pres[$i]=1;}
			break;
			
		case 48:
			$forecast= $FLLc[$fd][2];
			if ($FLLc[$fd][2]!=0){
			$pres[$i]=($HLLc[$td][$i]/$FLLc[$fd][2]);
			}else{$pres[$i]=1;}
			break;
		default:
			$forecast= $FLLc[$fd][$i+2];
			if ($FLLc[$fd][$i+2]!=0){
			$pres[$i]=($HLLc[$td][$i]/$FLLc[$fd][$i+2]);
			}else{$pres[$i]=1;}
			break;
	}
	if ($pres[$i]>2){$prn=2;}else{$prn=$pres[$i];}
	if ($prn<0){$prn=0;}
	if ($i>45 or $i<17){$prn=NULL;}
	if ($HLLc[$td][$i]==0){$today=NULL;}else{$today=$HLLc[$td][$i];}
          $rows[] = array("c"=>array(array("v"=>$CVHora[$i-1],"f"=>null),array("v"=>$today,"f"=>null),array("v"=>$forecast,"f"=>null),array("v"=>$prn,"f"=>null),array("v"=>1.15,"f"=>null),array("v"=>0.85,"f"=>null)));
          $i++;
       }
       $a = array("cols"=>$cols,"rows"=>$rows);
      


       



echo  json_encode($a);






?>