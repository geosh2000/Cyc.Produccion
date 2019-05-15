<?php
include_once('../modules/modules.php');
session_start();
timeAndRegion::setRegion('Cun');
if($_SESSION['monitor_pya_exceptions']!='1'){ echo "Your profile is not allowed to change this section. Nothing was modified"; exit;}

$idusr=$_SESSION['id'];
$usr=$_SESSION['asesor_id'];
$except=$_POST['excep'];
$asesor=$_POST['asesor'];
$hid=$_POST['h'];
$caso=$_POST['caso1'];
$notas=utf8_decode($_POST['notes']);
$date=date('Y-m-d',strtotime($_POST['fecha']));
if(isset($_POST['notes'])){$update_notas1="Nota,"; $update_notas2="'$notas',"; $set_notas="Nota='$notas',";}
$sa=$_POST['sa'];
if($sa==NULL){$sa=0;}

if(isset($_POST['excep']) && isset($_POST['asesor']) && isset($_POST['h'])){
	$query="SELECT * FROM PyA_Exceptions WHERE asesor='$asesor' AND horario_id='$hid' AND SalidaAnticipada='$sa'";

	if($result=Queries::query($query)){
		$num=$result->num_rows;

		if($except==6){
			$query="DELETE FROM PyA_Exceptions WHERE asesor='$asesor' AND horario_id='$hid' AND SalidaAnticipada='$sa'";

			if($result=Queries::query($query)){
				$query="SELECT * FROM Ausentismos WHERE asesor='$asesor' AND tipo_ausentismo IN (12,15,16) AND Inicio='$date' AND Fin='$date'";

				if($result_delete=Queries::query($query)){
					$fila=$result_delete->fetch_assoc();
					if($result_delete->num_rows>0){
							$query="DELETE FROM Ausentismos WHERE ausent_id='".$fila['ausent_id']."'";
							Queries::query($query);
					}
				}

				echo "Excepcion Eliminada";
			}
		}else{

			if($num>0){

				$query="UPDATE PyA_Exceptions SET $set_notas tipo='$except', caso='$caso', changed_by='$idusr' WHERE asesor='$asesor' AND horario_id='$hid' AND SalidaAnticipada='$sa'";
				Queries::query($query);

			}else{

				if($except==8){
					$val1="(asesor,horario_id,tipo,changed_by,caso,$update_notas1 SalidaAnticipada)";
	      }else{
					$val1="(asesor,horario_id,tipo,changed_by,caso,$update_notas1 SalidaAnticipada)";
				}

				$query="INSERT INTO PyA_Exceptions $val1 VALUES ('$asesor','$hid','$except','$idusr','$caso',$update_notas2 '$sa')";
				Queries::query($query);
			}

			if($except==12 || $except==15){
	    	$query="SELECT * FROM Ausentismos WHERE asesor='$asesor' AND tipo_ausentismo IN (12,15,16) AND Inicio='$date' AND Fin='$date'";

				if($result_delete=Queries::query($query)){
					$fila=$result_delete->fetch_assoc();
					if($result_delete->num_rows==0){
						$query="INSERT INTO Ausentismos (asesor,tipo_ausentismo,Inicio,Fin,Descansos,Beneficios,caso,ISI,Comments,User) VALUES ('$asesor',$except,'$date','$date','0','0','$caso','0','$notas','$idusr')";
						Queries::query($query);
					}else{
		 				 $query="UPDATE Ausentismos SET tipo_ausentismo=$except, User=$idusr, caso='$caso', Comments='$notas' WHERE asesor=$asesor AND Inicio = '$date' AND Fin='$date'";
		 				 Queries::query($query);

	 			 }

					$query="DELETE FROM PyA_Exceptions WHERE horario_id='$hid'";
					Queries::query($query);

				}
			}

			$query="SELECT * FROM `Tipos Excepciones` WHERE exc_type_id='$except'";
			if($result=Queries::query($query)){
				$fila=$result->fetch_assoc();
				echo "Aplicado: ".$fila['Excepcion'];
			}

	 }

	}

}else{
	echo "Error en aplicacion";
}

?>
