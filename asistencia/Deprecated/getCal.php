<?php
include("../connectDB.php");

//Build Info
$year=$_POST['year'];
$skill=$_POST['skill'];

$query="SELECT Fecha, abierto, espacios FROM ausentismos_calendario WHERE Departamento=$skill AND YEAR(Fecha))=$year";
if($result=$connectdb->query($query)){
	while($fila = $result -> fetch_assoc()){
		$data[date('m',strtotime($fila['Fecha']))][date('d',strtotime($fila['Fecha']))]['abierto']=$fila['abierto'];
		$data[date('m',strtotime($fila['Fecha']))][date('d',strtotime($fila['Fecha']))]['espacios']=$fila['espacios'];
	}
}

print json_encode($data,JSON_PRETTY_PRINT);
?>


