<?php
include("DBCallsHoraV.php");
include("DBHistorialAHT.php");
include("connectDB.php");

$day=$_GET['d'];
$month=$_GET['m'];
$year=$_GET['y'];
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
       $cols[] = array("id"=>"","label"=>"AHT","pattern"=>"","type"=>"number");
       $cols[] = array("id"=>"","label"=>"Top H","pattern"=>"","type"=>"number"); 
       $cols[] = array("id"=>"","label"=>"TOP L","pattern"=>"","type"=>"number");  
	
	$i=1;
       while ($i<=48){
       		if($i>=17){
       	  		$aht=$AHTc[$id][$i];											
       	  	}else{
       	  		$aht=NULL;
       	  	}
          $rows[] = array("c"=>array(array("v"=>$CVHora[$i-1],"f"=>null),array("v"=>$aht,"f"=>null),array("v"=>520,"f"=>null),array("v"=>480,"f"=>null)));
          $i++;
       }
       $a = array("cols"=>$cols,"rows"=>$rows);
      


       



echo  json_encode($a);






?>