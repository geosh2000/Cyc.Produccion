<?php
include("../modules/modules.php");

//Build Info
$year=$_POST['year'];
$skill=$_POST['skill'];

if(isset($_GET['year'])){
	$year=$_GET['year'];
	$skill=$_GET['skill'];
}

$query="SELECT Fecha, abierto, espacios FROM ausentismos_calendario WHERE Departamento=$skill AND YEAR(Fecha)=$year";
if($result=Queries::query($query)){
	while($fila = $result -> fetch_assoc()){
		$data['cal'][date('n',strtotime($fila['Fecha']))][date('j',strtotime($fila['Fecha']))]['abierto']=$fila['abierto'];
		$data['cal'][date('n',strtotime($fila['Fecha']))][date('j',strtotime($fila['Fecha']))]['espacios']=$fila['espacios'];
	}
}

$query="SELECT Fecha, Inicio, Fin, b.tipo_ausentismo, NombreAsesor(asesor,1) as Name "
		."FROM Fechas a LEFT JOIN Ausentismos b ON a.Fecha BETWEEN Inicio AND Fin "
		."LEFT JOIN Asesores c ON b.asesor=c.id LEFT JOIN `Tipos Ausentismos` d ON b.tipo_ausentismo=d.id "
		."WHERE `id Departamento`=$skill AND (YEAR(Inicio)=$year OR Year(Fin)=$year) AND showcal=1";
if($result=Queries::query($query)){
	while($fila = $result -> fetch_assoc()){
		$data['cal'][date('n',strtotime($fila['Fecha']))][date('j',strtotime($fila['Fecha']))]['espacios']--;
		$data['asig'][date('n',strtotime($fila['Fecha']))][date('j',strtotime($fila['Fecha']))][]=$fila['Name'];
	}
}

print json_encode($data,JSON_PRETTY_PRINT);
?>
