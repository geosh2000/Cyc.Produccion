<?php
include_once('../modules/modules.php');
$connectdb=Connection::mysqliDB('CC');
timeAndRegion::setRegion('Cun');

//POST DATA

$asesor=$_POST['asesor'];
$horario_id=$_POST['horario_id'];
$tipo=$_POST['tipo'];
$caso=$_POST['caso'];
$nota=utf8_decode($_POST['nota']);
$aplica=$_POST['aplica'];
$delete=$_POST['delete'];
$fecha=date('Y-m-d',strtotime($_POST['fecha']));
$name_excep=$_POST['name_excep'];

if($tipo==6){
	$query="DELETE FROM PyA_Exceptions WHERE asesor='$asesor' AND horario_id='$horario_id'";
	if($result=$connectdb->query($query)){
		$flag=1;
	}else{
		$error="Error al eliminar Excepcion: ".$connectdb->error."<br>";
	}

	$query="DELETE FROM Ausentismos WHERE asesor='$asesor' AND tipo_ausentismo IN (12,15,16) AND Inicio='$fecha' AND Fin='$fecha'";
    if($result=$connectdb->query($query)){
		if($flag==1){
			echo "Success";
		}else{
			echo "Error 6";
		}
	}else{
		if($flag==1){
			echo "Error 5";
		}else{
			echo "Error 4";
		}
	}
}else{
	$query="INSERT INTO PyA_Exceptions (asesor, horario_id, tipo, caso, Nota, changed_by) VALUES ('$asesor','$horario_id','$tipo','$caso','$nota',$aplica)";
	 if($result=$connectdb->query($query)){
	 	echo "Success";
	 }else{
	 	$query="UPDATE PyA_Exceptions SET tipo='$tipo', caso='$caso', Nota='$nota', changed_by='$aplica' WHERE asesor='$asesor' AND horario_id='$horario_id'";
		if($result2=$connectdb->query($query)){
			echo "Success";
		}else{
			echo "Error 3";
		}
	 }

	 if($tipo==12 || $tipo==15){
		 $query="SELECT * FROM Ausentismos WHERE asesor='$asesor' AND tipo_ausentismo IN (12,15,16) AND Inicio='$fecha' AND Fin='$fecha'";

		 if($result_delete=$connectdb->query($query)){ 
			 $fila=$result_delete->fetch_assoc();
			 if($result_delete->num_rows==0){
				 $query="INSERT INTO Ausentismos (asesor,tipo_ausentismo,Inicio,Fin,Descansos,Beneficios,caso,ISI,Comments,User) VALUES ('$asesor',$tipo,'$fecha','$fecha','0','0','$caso','0','$nota','$aplica')";
				 if($result=$connectdb->query($query)){
					 echo "";
				 }else{
					 echo "Error 1 ".$connectdb->error."<br>$query";
					}
			 }else{
				 $query="UPDATE Ausentismos SET tipo_ausentismo=$tipo, User=$aplica, caso='$caso', Comments='$nota' WHERE asesor=$asesor AND Inicio = '$fecha' AND Fin='$fecha'";
				 if($result=$connectdb->query($query)){
					 echo "";
				 }else{
					 echo "Error 1 ".$connectdb->error."<br>$query";
				 }
			 }

			 $query="DELETE FROM PyA_Exceptions WHERE horario_id='$horario_id'";
			 if($result=$connectdb->query($query)){
				 echo "";
			 }else{
				 echo "Error 2 ".$connectdb->error."<br>$query";
			 }

		 }else{
			 echo "Error 2 ".$connectdb->error."<br>$query";
		 }
	 }



}
$connectdb->close();

?>
