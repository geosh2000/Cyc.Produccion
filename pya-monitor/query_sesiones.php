<?
include("../connectDB.php");

//Funciones
	function time_to_sec($dato){
		
		if($dato==NULL){ return NULL;}
		
		$dato = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $dato);
		sscanf($dato, "%d:%d:%d", $hours, $minutes, $seconds);
		$total= $hours * 3600 + $minutes * 60 + $seconds;
		
		return $total;
	}

//Factor conversion segundos a pixeles
	$factor_pix=57.6;
	
//Ancho de sesion del dia en pixeles: 1500

//Sesiones In
	$query="SELECT asesor, MIN(Hora) as Entrada, TIME_TO_SEC(MIN(Hora))/$factor_pix as inicio_pix FROM Sesiones WHERE Fecha=CURDATE() AND Hora>'05:00:00' GROUP BY asesor";
	
	if ($result=$connectdb->query($query)) {
		while ($fila = $result->fetch_assoc()) {
			$data[$fila['asesor']]['entrada']=$fila['Entrada'];
			$data[$fila['asesor']]['entrada_pix']=$fila['inicio_pix'];
		}
	}else{
		echo $connectdb->error."<br> ON <br>$query<br>";
	}
	unset($result);
	
//Sesiones OUT
	$query="SELECT asesor, MAX(Hora_out) as Salida, TIME_TO_SEC(MAX(Hora_out))/$factor_pix as salida_pix FROM Sesiones WHERE Fecha=CURDATE() AND Hora_out>'05:00:00' GROUP BY asesor";
	
	if ($result=$connectdb->query($query)) {
		while ($fila = $result->fetch_assoc()) {
			$data[$fila['asesor']]['salida']=$fila['Salida'];
			$data[$fila['asesor']]['salida_pix']=$fila['salida_pix'];
			$data[$fila['asesor']]['duracion_pix']=$data[$fila['asesor']]['salida_pix']-$data[$fila['asesor']]['entrada_pix'];
		}
	}else{
		echo $connectdb->error."<br> ON <br>$query<br>";
	}
	unset($result);


//Horarios
	$query="SELECT asesor, 
			if(`jornada end`<='06:00:00','23:59:59',`jornada end`) as 'jornada end', 
			if(`extra1 end`<='06:00:00','23:59:59',`extra1 end`) as 'extra1 end',
			if(`extra2 end`<='06:00:00','23:59:59',`extra2 end`) as 'extra2 end',
			 `jornada start`, `extra1 start`, `extra2 start`, `comida start`, `comida end` FROM `Historial Programacion` WHERE Fecha=CURDATE()";
	
	if ($result=$connectdb->query($query)) {
		while ($fila = $result->fetch_assoc()) {
			$data[$fila['asesor']]['inicio_j']=$fila['jornada start'];
			$data[$fila['asesor']]['inicio_c']=$fila['comida start'];
			$data[$fila['asesor']]['inicio_x1']=$fila['extra1 start'];
			$data[$fila['asesor']]['inicio_x2']=$fila['extra2 start'];
			$data[$fila['asesor']]['fin_j']=$fila['jornada end'];
			$data[$fila['asesor']]['fin_c']=$fila['comida end'];
			$data[$fila['asesor']]['fin_x1']=$fila['extra1 end'];
			$data[$fila['asesor']]['fin_x2']=$fila['extra2 end'];
			if(isset($data[$fila['asesor']]['inicio_j'])){
				$data[$fila['asesor']]['inicio_j_pix']=time_to_sec($fila['jornada start'])/$factor_pix;
				$data[$fila['asesor']]['fin_j_pix']=time_to_sec($fila['jornada end'])/$factor_pix;
				$data[$fila['asesor']]['duracion_j_pix']=$data[$fila['asesor']]['fin_j_pix']-$data[$fila['asesor']]['inicio_j_pix'];
			}
			if(isset($data[$fila['asesor']]['inicio_c'])){
				$data[$fila['asesor']]['inicio_c_pix']=time_to_sec($fila['comida start'])/$factor_pix;
				$data[$fila['asesor']]['fin_c_pix']=time_to_sec($fila['comida end'])/$factor_pix;
				$data[$fila['asesor']]['duracion_c_pix']=$data[$fila['asesor']]['fin_c_pix']-$data[$fila['asesor']]['inicio_c_pix'];
			}
			if(isset($data[$fila['asesor']]['inicio_x1'])){
				$data[$fila['asesor']]['inicio_x1_pix']=time_to_sec($fila['extra1 start'])/$factor_pix;
				$data[$fila['asesor']]['fin_x1_pix']=time_to_sec($fila['extra1 end'])/$factor_pix;
				$data[$fila['asesor']]['duracion_x1_pix']=$data[$fila['asesor']]['fin_x1_pix']-$data[$fila['asesor']]['inicio_x1_pix'];
			}
			if(isset($data[$fila['asesor']]['inicio_x2'])){
				$data[$fila['asesor']]['inicio_x2_pix']=time_to_sec($fila['extra2 start'])/$factor_pix;
				$data[$fila['asesor']]['fin_x2_pix']=time_to_sec($fila['extra2 end'])/$factor_pix;
				$data[$fila['asesor']]['duracion_x2_pix']=$data[$fila['asesor']]['fin_x2_pix']-$data[$fila['asesor']]['inicio_x2_pix'];
			}
		}
	}else{
		echo $connectdb->error."<br> ON <br>$query<br>";
	}
	unset($result);

//Detalles Asesor
	$query="SELECT a.id, Departamento, `N Corto`, Activo FROM Asesores a LEFT JOIN PCRCs b ON a.`id Departamento`=b.id";
	
	if ($result=$connectdb->query($query)) {
		while ($fila = $result->fetch_assoc()) {
			$data[$fila['id']]['Departamento']=utf8_encode($fila['Departamento']);
			$data[$fila['id']]['Nombre']=utf8_encode($fila['N Corto']);
			$data[$fila['id']]['Activo']=$fila['Activo'];
		}
	}else{
		echo $connectdb->error."<br> ON <br>$query<br>";
	}
	unset($result);

//Ausentismos
	$query="SELECT Ausentismo, Comments, caso, username FROM Ausentismos a LEFT JOIN `Tipos Ausentismos` b ON a.tipo_ausentismo=b.id LEFT JOIN userDB c ON a.User=c.userid WHERE CURDATE() BETWEEN Inicio AND Fin";
	
	if ($result=$connectdb->query($query)) {
		while ($fila = $result->fetch_assoc()) {
			$data[$fila['asesor']]['Ausentismo']=utf8_encode($fila['Ausentismo']);
			$data[$fila['asesor']]['Aus_Comm']=utf8_encode($fila['Comments']);
			$data[$fila['asesor']]['Aus_caso']=utf8_encode($fila['caso']);
			$data[$fila['asesor']]['Aus_User']=utf8_encode($fila['username']);
		}
	}else{
		echo $connectdb->error."<br> ON <br>$query<br>";
	}
	unset($result);


//Excepciones PYA
	$query="SELECT Excepcion, Nota, caso, username  FROM PyA_Exceptions a LEFT JOIN `Tipos Excepciones` b ON a.tipo = b.exc_type_id LEFT JOIN userDB c ON a.changed_by=c.userid LEFT JOIN `Historial Programacion` d ON a.horario_id=d.id WHERE Fecha=CURDATE()";
	
	if ($result=$connectdb->query($query)) {
		while ($fila = $result->fetch_assoc()) {
			$data[$fila['asesor']]['Excepcion']=utf8_encode($fila['Excepcion']);
			$data[$fila['asesor']]['Exc_Comm']=utf8_encode($fila['Nota']);
			$data[$fila['asesor']]['Exc_caso']=utf8_encode($fila['caso']);
			$data[$fila['asesor']]['Exc_User']=utf8_encode($fila['username']);
		}
	}else{
		echo $connectdb->error."<br> ON <br>$query<br>";
	}
	unset($result);


//Evaluate Retardos
	foreach($data as $id => $info){
		if(isset($info['entrada'])){	
			if(date('H:i:s', strtotime($info['entrada']))>date('H:i:s',strtotime($info['inicio_j'].' +13 min'))){
				$data[$id]['Retardo']="B";
			}else{
				if(date('H:i:s', strtotime($info['entrada']))>=date('H:i:s',strtotime($info['inicio_j'].' +1 min'))){
					$data[$id]['Retardo']="A";
				}else{
					if(date('H:i:s', strtotime($info['entrada']))<=date('H:i:s',strtotime($info['inicio_j'].' -30 min'))){
						if(date('H:i:s', strtotime($info['inicio_x1']))<date('H:i:s',strtotime($info['inicio_j']))){
							if(date('H:i:s', strtotime($info['entrada']))<=date('H:i:s',strtotime($info['inicio_x1'].' -30 min'))){
								$data[$id]['Retardo']="Erroneo";
							}else{
								$data[$id]['Retardo']="NA";
							}
						}elseif(date('H:i:s', strtotime($info['inicio_x2']))<date('H:i:s',strtotime($info['inicio_j']))){
							if(date('H:i:s', strtotime($info['entrada']))<=date('H:i:s',strtotime($info['inicio_x2'].' -30 min'))){
								$data[$id]['Retardo']="Erroneo";
							}else{
								$data[$id]['Retardo']="NA";
							}
						}else{
							$data[$id]['Retardo']="Erroneo";
						}
					}else{
						$data[$id]['Retardo']="NA";
					}
				}	
			}
		}else{
			if(date('H:i:s')>=date('H:i:s',strtotime($info['inicio_j']))){
				$data[$id]['Retardo']="Pendiente";
			}
		}
	}
	

print json_encode($data,JSON_PRETTY_PRINT);

//print_r($data);

?>
