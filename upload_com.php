<?php
include("connectDB.php");
date_default_timezone_set('America/Bogota');

$i=1;
while($i<=500){
	$s_asesor[$i]=$_GET["a$i"];
	$s_fecha_inicio[$i]=$_GET["fi$i"];
	$s_hora_inicio[$i]=$_GET["hi$i"];
	$s_fecha_fin[$i]=$_GET["ff$i"];
	$s_hora_fin[$i]=$_GET["hf$i"];
	$s_skill[$i]=$_GET["s$i"];
    $s_tipo[$i]=$_GET["t$i"];
$i++;
}

foreach($s_asesor as $key => $asesorOK){
	$flag=0;
	if($asesorOK!=NULL){
		//GET id de asesor
		//$query="SELECT id FROM Asesores WHERE `N Corto`='$asesorOK'";
		//$result=mysql_query($query);
		//$s_id[$key]=mysql_result($result,0,'id');
		//Check if IN registry already exists
		$query="SELECT * FROM Comidas WHERE asesor='$asesorOK' AND Fecha='$s_fecha_inicio[$key]' AND Inicio='$s_hora_inicio[$key]' AND Skill='$s_skill[$key]'";
		
		$result=mysql_query($query);
		$num=mysql_numrows($result);
		echo "$query<br>$key: ".mysql_numrows($result)."<br>";
		if(mysql_numrows($result)>0){
			$query="SELECT * FROM Comidas WHERE asesor='$asesorOK' AND Fecha='$s_fecha_inicio[$key]'
				AND Inicio='$s_hora_inicio[$key]' AND Skill='$s_skill[$key]' AND tipo='$s_tipo[$key]' AND Fin='$s_fecha_fin[$key]'";
			$result=mysql_query($query);
			$num=mysql_numrows($result);
				if(mysql_numrows($result)==0){
			
					$query="UPDATE Comidas SET Fin='$s_hora_fin[$key]', tipo='$s_tipo[$key]'  WHERE asesor='$asesorOK'
						AND Fecha='$s_fecha_inicio[$key]'AND Inicio='$s_hora_inicio[$key]' AND Skill='$s_skill[$key]'";
					mysql_query($query);
					//echo "$query<br>";
			
				}
		}else{
		//INSERT new registry
			$query="INSERT INTO Comidas (asesor,Fecha,Inicio,Fin,Skill,tipo)
				VALUES ('$asesorOK','$s_fecha_inicio[$key]','$s_hora_inicio[$key]','$s_hora_fin[$key]','$s_skill[$key]','$s_tipo[$key]')";
			mysql_query($query);
		}
	}
}

echo "Updated!";

?>