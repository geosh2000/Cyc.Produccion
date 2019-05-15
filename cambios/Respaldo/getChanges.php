<?php
include_once("../modules/modules.php");

$connectdb=Connection::mysqliDB('CC');


$asesor1=$_POST['asesor1'];
$asesor2=$_POST['asesor2'];
$Fecha1=date('Y-m-d',strtotime($_POST['fecha1']));
$Fecha2=date('Y-m-d',strtotime($_POST['fecha2']));

if(isset($_GET['asesor1'])){
	$asesor1=$_GET['asesor1'];
	$asesor2=$_GET['asesor2'];
	$Fecha1=date('Y-m-d',strtotime($_GET['fecha1']));
	$Fecha2=date('Y-m-d',strtotime($_GET['fecha2']));
}

$query="SELECT id, fecha, NombreAsesor(id_asesor,2) as Asesor1, NombreAsesor(`id_asesor 2`,2) as Asesor2, "
		."IF(tipo=1,'Turno',if(tipo=2,'Descanso','Ajuste0')) as Tipo, " 
		."caso, `Last Update` as Capturado FROM `Cambios de Turno` WHERE id_asesor IN ($asesor1, $asesor2) AND tipo IN (1,2) AND MONTH(Fecha) = MONTH('$Fecha1')";
			//echo "$query";
if($result=$connectdb->query($query)){
	$fields=$result->fetch_fields();
	while($fila=$result->fetch_row()){
		for($i=0;$i<$result->field_count;$i++){
			switch($fields[$i]->name){
				case 'jornada start old':
				case 'jornada end old':
				case 'comida start old':
				case 'comida end old':
				case 'extra1 start old':
				case 'extra1 end old':
				case 'extra2 start old':
				case 'extra2 end old':
				case 'jornada start new':
				case 'jornada end new':
				case 'comida start new':
				case 'comida end new':
				case 'extra1 start new':
				case 'extra1 end new':
				case 'extra2 start new':
				case 'extra2 end new':
					$time = new DateTime(date('Y-m-d', strtotime($fila[4])).' '.$fila[$i].' America/Mexico_City');
					$time -> setTimezone($cun_time);
					$datos = $time->format('H:i');
					unset($time);
					break;
				default:
					$datos=utf8_encode($fila[$i]);
					break;
			}
			$data[$fila[0]][$fields[$i]->name]=$datos;
			//$data[$fila[1]][$fila[4]][$fields[$i]->name]=$datos;
		}
	}
}else{
	$error=$connectdb->error;
}
?>
<table class='t2' style='margin: auto; width: 850px;'>
	<tr class='title'>
		<?php
			if(count($data)>0){
				foreach($data as $id => $info){
					foreach($info as $title => $info2){
						echo "<th>$title</th>\n\t";
					}
					break;
				}
			}
		?>
	</tr>
	<?php
		if(count($data)>0){
			foreach($data as $id => $info){
				echo "<tr class='pair' style='text-align: center'>";
				foreach($info as $title => $info2){
					echo "<td>$info2</td>\n\t";
				}
				echo "</tr>\n\t";
			}
		}
	?>
</table>
<?php 

if(isset($error)){echo "$error<br>ON<br>$query";}

$connectdb->close();

?>
