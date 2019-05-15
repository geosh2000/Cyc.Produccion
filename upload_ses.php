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
		$query="SELECT * FROM Sesiones WHERE asesor='$asesorOK' AND Fecha='$s_fecha_inicio[$key]' AND Hora='$s_hora_inicio[$key]' AND Skill='$s_skill[$key]'";

		$result=mysql_query($query);
		$num=mysql_numrows($result);
		
		if(mysql_numrows($result)==0){
			$query="INSERT INTO Sesiones (asesor,Fecha,Hora,Fecha_out, Hora_out,Skill) VALUES ('$asesorOK','$s_fecha_inicio[$key]','$s_hora_inicio[$key]','$s_fecha_fin[$key]','$s_hora_fin[$key]','$s_skill[$key]')";

			//echo "$query<br>";
			//$lastid_in=mysql_insert_id();
			//$query="INSERT INTO Sesiones (asesor,Fecha,Hora,Tipo,Skill,relatedId) VALUES ('$asesorOK','$s_fecha_fin[$key]','$s_hora_fin[$key]','out','$s_skill[$key]','$lastid_in')";
			//mysql_query($query);
			//echo "$query<br>";
			//$lastid_out=mysql_insert_id();
			//$query="UPDATE Sesiones SET relatedId='$lastid_out' WHERE SesionId='$lastid_in'";
			//mysql_query($query);
			$flag=1;
		}else{
			$lastid_in=mysql_result($result,0,'SesionId');
            $query="UPDATE Sesiones SET Hora_out='$s_hora_fin[$key]', Fecha_out='$s_fecha_fin[$key]' WHERE SesionId='$lastid_in'";
        }
		mysql_query($query);
        if(mysql_errno()){
		    echo "$key1 // $key_xfered MySQL error ".mysql_errno().": "
		         .mysql_error()."\n<br>When executing <br>\n$query\n<br><br>";
            $qerror="INSERT INTO Errores (site, error, consulta,string) VALUES ('pt/upload_ses.php','".mysql_errno()."','$query','".$_SERVER["QUERY_STRING"]."')";
            mysql_query($qerror);
		}
					
				
					
						
	}
}

echo "Updated!";

?>