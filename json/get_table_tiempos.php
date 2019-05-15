<?php
include("../connectDB.php");

//Get Variables
$esquema=$_GET['esquema'];
$asesor=$_GET['asesor'];

$query="SELECT TIME_TO_SEC(Tiempo) as Tiempo FROM PNP_tiempos WHERE Esquema=$esquema";
if($result=$connectdb->query($query)){
	$fila=$result->fetch_assoc();
	$pausa_disponible=$fila['Tiempo'];	
}

$query="SELECT TIME_TO_SEC(Fin)-TIME_TO_SEC(Inicio) as tiempo FROM Comidas a, Tipos_pausas b WHERE a.tipo=b.pausa_id AND asesor=$asesor AND Fecha='".date('Y-m-d')."' AND Seleccionables=1";
if($result=$connectdb->query($query)){
	while($fila=$result->fetch_assoc()){
		$pausas_time+=intval($fila['tiempo']);
	}
}

$pausas_temp="00:00:00";
$pausas_tiempo=date('H:i:s', strtotime($pausas_temp ."+ $pausas_time seconds"));
$pausas_rest_temp=$pausa_disponible-$pausas_time;
if($pausas_rest_temp<0){
    $pausas_restante="00:00:00";
    $pausas_rest_temp*=((-1));
    $pausas_excedido=date('H:i:s', strtotime($pausas_temp ."+ $pausas_rest_temp seconds"));
}else{
    $pausas_excedido="00:00:00";
    $pausas_restante=date('H:i:s', strtotime($pausas_temp ."+ $pausas_rest_temp seconds"));
}



?>

<td><?php echo $esquema; ?></td>
        <td><?php echo $pausas_cant; ?></td>
        <td><?php echo $pausas_tiempo; ?></td>
        <td><?php echo $pausas_restante; ?></td>
        <td><?php echo $pausas_excedido; ?></td>

