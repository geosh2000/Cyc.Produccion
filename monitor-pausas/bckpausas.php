<?php
include("../connectDB.php");

$query="SELECT * FROM t_pausas WHERE pausas_id BETWEEN 90000 AND 150000";
if($result=$connectdb->query($query)){
	$field=$result->fetch_fields();
	while($fila=$result->fetch_array(MYSQLI_BOTH)){
		for($i=0;$i<$result->field_count;$i++){
			$data[$fila['pausas_id']][$field[$i]->name]=$fila[$i];
		}
	}
}

foreach($data as $id => $info){
	$query="INSERT INTO t_pausas_copy (asesor,codigo,Hora_Inicio,Hora_Fin,Fecha,Duracion,Skill,Last_Update) VALUES ("
			."'".$info['asesor']."',"
			."'".$info['codigo']."',"
			."'".$info['Hora_Inicio']."',"
			."'".$info['Hora_Fin']."',"
			."'".$info['Fecha']."',"
			."'".$info['Duracion']."',"
			."'".$info['Skill']."',"
			."'".$info['Last_Update']."')";
	if($result=$connectdb->query($query)){
		echo $id." -> INSERTED<br>";
	}else{
		echo $id." -> ".$connectdb->error." on:<br>$query<br><br>";
	}
}