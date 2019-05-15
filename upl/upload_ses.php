<?php
include("pt/connectDB.php");
date_default_timezone_set('America/Bogota');

$i=1;
while($i<=10){
	$s_asesor[$i]=$_GET["a$i"];
	$s_fecha_inicio[$i]=$_GET["fi$i"];
	$s_hora_inicio[$i]=$_GET["hi$i"];
	$s_fecha_fin[$i]=$_GET["ff$i"];
	$s_hora_fin[$i]=$_GET["hf$i"];
	$s_skill[$i]=$_GET["s$i"];
$i++;
}

foreach($s_asesor as $key => $asesorOK){
	$flag=0;
	if($asesorOK!=NULL){
		//GET id de asesor
		$query="SELECT id FROM Asesores WHERE `N Corto`='$asesorOK'";
		$result=mysql_query($query);
		$s_id[$key]=mysql_result($result,0,'id');
		//Check if IN registry already exists
		$query="SELECT * FROM Sesiones WHERE asesor='$s_id[$key]' AND Fecha='$s_fecha_inicio[$key]' AND Hora='$s_hora_inicio[$key]' AND Skill='$s_skill[$key]' AND Tipo='in'";
		
		$result=mysql_query($query);
		$num=mysql_numrows($result);
		
		if(mysql_numrows($result)==0){
			$query="INSERT INTO Sesiones (asesor,Fecha,Hora,Tipo,Skill) VALUES ('$s_id[$key]','$s_fecha_inicio[$key]','$s_hora_inicio[$key]','in','$s_skill[$key]')";
			mysql_query($query);
			$lastid_in=mysql_insert_id();
			$query="INSERT INTO Sesiones (asesor,Fecha,Hora,Tipo,Skill,relatedId) VALUES ('$s_id[$key]','$s_fecha_fin[$key]','$s_hora_fin[$key]','out','$s_skill[$key]','$lastid_in')";
			mysql_query($query);
			$lastid_out=mysql_insert_id();
			$query="UPDATE Sesiones SET relatedId='$lastid_out' WHERE SesionId='$lastid_in'";
			mysql_query($query);
			$flag=1;
		}else{
			$lastid_in=mysql_result($result,0,'SesionId');
		}
		//UPDATE existing registry
			$query="UPDATE Sesiones SET Hora='$s_hora_fin[$key]' WHERE relatedId='$lastid_in'";
			mysql_query($query);
					
				
					
						
	}
}

echo "Updated!";

?>