<?php
include("connectDB.php");
$reg=$_GET['reg'];
$dia=$_GET['dia'];
$dateC=$_GET['date'];
$Day=$_GET['day'];
$Month=$_GET['month'];
$Year=$_GET['year'];
$table="`Historial Llamadas`";
$lupdate="LastUpdate='$dateC',";

switch ($reg){
	case 12:
		$i=1;
		break;
	case 24:
		$i=13;
		break;
	case 36:
		$i=25;
		break;
	case 48:
		$i=37;
		break;
}

$filas=$i+12;


switch ($dia){
	case "y":
		$dia="y";
		break;
	case "lw":
		$dia="lw";
		break;
	case "t":
		$dia="t";
		break;
	case "f":
		$dia="forecast";
		break;
	case "previo":
		$action="previos";
		break;
	case "forecast":
		$action="previos";
		$table="`Forecast Llamadas`";
		$lupdate="";
		break;
}

//Verifica si existe el registro en SQL

$queryChk="SELECT * FROM $table WHERE (Dia='$Day' AND Mes='$Month' AND Anio='$Year' AND Skill = 'Ventas')";
$resultChk=mysql_query($queryChk);
$rowsChk=mysql_numrows($resultChk);

// New Table
	if ($rowsChk == 0){
		$query2="INSERT INTO $table (id,Dia,Mes,Anio,Skill) Values (NULL,$Day,$Month,$Year,'Ventas')";
		mysql_query($query2);
	}


//Fill Tables
while ($i<=$filas) {

	$calls[$i]=$_GET['c'.$i];
	if ($action!='previos'){
	$query="UPDATE `Comportamiento Hora` SET $dia='$calls[$i]', fecha='$dateC' WHERE id='$i'";
	mysql_query($query);}
	
	
	$query2="UPDATE $table SET $lupdate `$i`='$calls[$i]' WHERE (Dia='$Day' AND Mes='$Month' AND Anio='$Year' AND Skill = 'Ventas')";
	mysql_query($query2);
$i++;
}



$temp=mysql_result($resultChk,0,"Dia");
mysql_close();
echo "Record Updated // ROWS".$queryChk;

?>