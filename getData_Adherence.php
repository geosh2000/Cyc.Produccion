<?php
include("DBCallsHoraV.php");
date_default_timezone_set('America/Mexico_City');
include("connectDB.php");

$day=$_GET['d'];
$month=$_GET['m'];
$year=$_GET['y'];
$skill=$_GET['s'];






foreach($CVHora as $key => $hora){
	
	
	$date="$year-$month-$day";
	$verano=date('I', strtotime($date));
	
	if(intval($hora)!=$hora){$min="30"; $hr=intval($hora);}else{$min="00"; $hr=$hora;}
	
	if($verano==0){
		switch($key){
			case 0:
				$key=46;
				break;
			case 1:
				$key=47;
				break;
			default:
				$key=$key-2;
				break;
		}
		
		
	}
	if($key>=46){$x=1; $y=0;}else{$x=0; $y=1;}
	if($hr<10){$hr="0$hr";}
	$query="SELECT count(`Asesores`.`N Corto`) as 'asesores' FROM `Historial Programacion` 
			LEFT JOIN `Asesores` on `Historial Programacion`.asesor=`Asesores`.id 
			WHERE 
				(
					`Asesores`.`id Departamento`=$skill
					AND `Asesores`.Activo=1
					AND `Historial Programacion`.Fecha='".date('Y-m-d',strtotime($date))."' 
					AND `Historial Programacion`.`jornada start`<= '$hr:$min:00'
					AND (`Historial Programacion`.`jornada end`> '$hr:$min:00' OR (`Historial Programacion`.`jornada end` >= '00:00:00' 
					AND `Historial Programacion`.`jornada end` < '02:00:00')) 
					AND (`Historial Programacion`.`jornada start` != '00:00:00' or `Historial Programacion`.`jornada end` != '00:00:00'))"; 
	if(intval($hora)<=2){$query=$query."OR 
				(
					(
					
					`Asesores`.`id Departamento`=$skill
					AND `Asesores`.Activo=1
					AND `Historial Programacion`.`jornada end` >= '$hr:00:00' AND `Historial Programacion`.`jornada end` < '02:00:00')
					AND `Historial Programacion`.Fecha='".date('Y-m-d',strtotime($date.' + 1 days'))."' AND (`Historial Programacion`.`jornada start` != '00:00:00' or `Historial Programacion`.`jornada end` != '00:00:00'))";
	}
	
	
	$result=mysql_query($query);
	$n_asesores[$key]=mysql_result($result,0,'asesores');
	//echo "$hora: ($key) $query<br><br>";
	
	
	
	
}



//JSON
$a = array();
       $cols = array();
       $rows = array();
       $cols[] = array("id"=>"","label"=>"Hora","pattern"=>"","type"=>"number");
       $cols[] = array("id"=>"","label"=>"Programados","pattern"=>"","type"=>"number");
       $cols[] = array("id"=>"","label"=>"Reales","pattern"=>"","type"=>"number");
       $cols[] = array("id"=>"","label"=>"Adherencia","pattern"=>"","type"=>"number");
        
	
	$i=1;
       while ($i<=48){
       	if($n_asesores[$i-1]==0){$adh=NULL;}else{$adh=$n_asesores[$i-1]/$n_asesores[$i-1];}
       		
          $rows[] = array("c"=>array(array("v"=>$CVHora[$i-1],"f"=>null),array("v"=>$n_asesores[$i-1],"f"=>null),array("v"=>$n_asesores[$i-1],"f"=>null),array("v"=>$adh,"f"=>null)));
          $i++;
       }
       $a = array("cols"=>$cols,"rows"=>$rows);
      


       



echo  json_encode($a);







?>