<?php
include_once("../modules/modules.php");

session_start();

if($_SESSION['dep']==NULL || $_SESSION['dep']==1 || $_SESSION['dep']==47 || $_SESSION['id']==0){
	$data['status']=0;
	print json_encode($data,JSON_PRETTY_PRINT);
	exit;
}

$connectdb=Connection::mysqliDB('CC');

$asesor=$_POST['asesor'];
$Fecha=date('Y-m-d',strtotime($_POST['fecha']));

if(isset($_GET['asesor'])){
	$asesor=$_GET['asesor'];
	$Fecha=date('Y-m-d',strtotime($_GET['fecha']));
}

$query="SELECT
			a.id, b.id, `N Corto` as Nombre, Esquema, c.Fecha,
			`jornada start` as js, `jornada end` as je,
			`comida start` as cs, `comida end` as ce,
			`extra1 start` as x1s, `extra1 end` as x1e,
			`extra2 start` as x2s, `extra2 end` as x2e,
			getAusentismo(b.id,c.Fecha,1) as Ausentismo
		FROM
			Fechas c
		JOIN
			Asesores b
		LEFT JOIN
			`Historial Programacion` a ON a.asesor=b.id AND a.Fecha=c.Fecha
		WHERE
			c.Fecha IN ('$Fecha', '".date('Y-m-d',strtotime($Fecha.' +1 day'))."') AND b.id=$asesor ORDER BY Fecha";
			//echo "$query";
if($result=$connectdb->query($query)){
	$x=0;
	$fields=$result->fetch_fields();
	while($fila=$result->fetch_row()){
		for($i=0;$i<$result->field_count;$i++){
			switch($fields[$i]->name){
				case 'js':
				case 'je':
				case 'cs':
				case 'ce':
				case 'x1s':
				case 'x1e':
				case 'x2s':
				case 'x2e':
					$time = new DateTime(date('Y-m-d', strtotime($fila[4])).' '.$fila[$i].' America/Mexico_City');
					$time -> setTimezone($cun_time);
					$datos = $time->format('H:i');
					unset($time);
					break;
				default:
					$datos=utf8_encode($fila[$i]);
					break;
			}
			$data[$fila[4]][$fields[$i]->name]=$datos;
		}
		$x++;
	}
}

if(isset($data)){
	$x=0;
	foreach($data as $date => $info){
		$td["fecha$x"]['fecha']=utf8_encode(date('D d-m-Y', strtotime($date)));
		if($info['js']==$info['je']){
			$horario="<p>Descanso</p><p>".$info['Ausentismo']."</p>";
		}else{
			$horario="<p>Jornada: ".$info['js']." - ".$info['je']."</p>";

			if($info['cs']==$info['ce']){
				$horario.="<p>Sin Comida</p>";
			}else{
				$horario.="<p>Comida: ".$info['cs']." - ".$info['ce']."</p>";
			}

			if($info['x1s']!=$info['x1e']){
				$horario.="<p>Extra1: ".$info['x1s']." - ".$info['x1e']."</p>";
			}

			if($info['x2s']!=$info['x2e']){
				$horario.="<p>Extra2: ".$info['x2s']." - ".$info['x2e']."</p>";
			}
			$horario.="<p>".$info['Ausentismo']."</p>";
		}
		$td["fecha$x"]['horario']=utf8_encode($horario);
		$x++;
	}
}

$td['status']=1;

$connectdb->close();

print json_encode($td,JSON_PRETTY_PRINT);
