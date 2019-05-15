<?php
include("DBCallsHoraV.php");
include("DBHistorialSLA.php");
include("DBHistorialLlamadas.php");
include("connectDB.php");

$day=$_GET['d'];
$month=$_GET['m'];
$year=$_GET['y'];
$sla_type=$_GET['slat'];
$sla_perc=$_GET['slap'];
$skill=$_GET['skill'];


//get ids
$queryT="SELECT * FROM `Historial Llamadas` WHERE (Dia='$day' AND Mes='$month' AND Anio='$year' AND Skill='$skill')";
$resultT=mysql_query($queryT);
$id=mysql_result($resultT,0,"id");



//JSON
$a = array();
       $cols = array();
       $rows = array();
       $cols[] = array("id"=>"","label"=>"Hora","pattern"=>"","type"=>"number");
       $cols[] = array("id"=>"","label"=>"SLA","pattern"=>"","type"=>"number");
       $cols[] = array("id"=>"","label"=>"Top H","pattern"=>"","type"=>"number"); 
       $cols[] = array("id"=>"","label"=>"Top L","pattern"=>"","type"=>"number");  
	
	$i=1;
       while ($i<=48){
       		$toph=$sla_perc+10;
       	  			$topl=$sla_perc-5;
       		switch($sla_type){
       			case 20:
       	  			if($HLLc[$id][$i]==0){$sla="NULL";}else{$sla=$SLA20c[$id][$i]/$HLLc[$id][$i]*100;}
       	  			
       	  			break;										
			case 30:
	       	  		if($HLLc[$id][$i]==0){$sla="NULL";}else{$sla=$SLA30c[$id][$i]/$HLLc[$id][$i]*100;}
	       	  		break;
	       	  	default:
	       	  		if($HLLc[$id][$i]==0){$sla="NULL";}else{$sla=$SLA20c[$id][$i]/$HLLc[$id][$i]*100;}
       	  			break;
       	  	}
          $rows[] = array("c"=>array(array("v"=>$CVHora[$i-1],"f"=>null),array("v"=>$sla,"f"=>null),array("v"=>$toph,"f"=>null),array("v"=>$topl,"f"=>null)));
          $i++;
       }
       $a = array("cols"=>$cols,"rows"=>$rows);
      


       



echo  json_encode($a);






?>