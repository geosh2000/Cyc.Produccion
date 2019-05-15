<?php
include_once("../modules/modules.php");

$connectdb=Connection::mysqliDB('CC');

//If tipo== 5 (descanso pendiente) save record on "Dias pendientes Redimidos"
//If tipo== 12 (falta justificada) delete regs from PyA Exceptions and Copy Comments

$asesor=$_POST['asesor'];
$tipo=$_POST['tipo'];
$motivo=$_POST['motivo'];
$descansos=$_POST['descansos'];
$beneficios=$_POST['beneficios'];
$comments=utf8_decode($_POST['comments']);
$moper=$_POST['moper'];
$caso=$_POST['caso'];
$isMoper=$_POST['isMoper'];
$user=$_POST['user'];
$dias=$_POST['dias'];

if($isMoper==''){$isMoper=0;}
if($moper==''){$moper='NULL';}
if($caso==''){$caso='NULL';}
if($descansos==''){$descansos=0;}
if($beneficios==''){$beneficios=0;}

$inicio=date('Y-m-d', strtotime($_POST['inicio']));
$fin=date('Y-m-d', strtotime($_POST['fin']));

//Check existing registries
	$query="SELECT * FROM Ausentismos WHERE asesor=$asesor AND (((Inicio<='$inicio' AND Fin>='$fin') OR  (Inicio<='$inicio' AND Fin>='$fin')) OR (Inicio>'$inicio' AND Fin<'$fin'))";
	if($result=$connectdb->query($query)){
		$regs=$result->num_rows;
	}
	
	if($regs==0){
		
		//UPDATE DB PyA
		switch($tipo){
			case 12:
				$query="SELECT id FROM `Historial Programacion` WHERE asesor='$asesor' AND Fecha BETWEEN '$inicio' AND '$fin' ORDER BY Fecha";
				if($result=$connectdb->query($query)){
					while($fila=$result->fetch_assoc()){
						$tmp_id[]=$fila['id'];
					}
				}
				
				$tmpIds=implode(",", $tmp_id);
				
				$query="SELECT * FROM PyA_Exceptions WHERE horario_id IN ($tmpIds)";
				if($result=$connectdb->query($query)){
					while($fila=$result->fetch_assoc()){
						$tmp_pya[]="(".$fila['Last Update'].") -> ".$fila['Nota'];
					}
				}
				
				$query="DELETE FROM PyA_Exceptions WHERE horario_id IN ($tmpIds)";
				if($result=$connectdb->query($query)){
					$data['pya']['status']=1;
				}else{
					$data['pya']['status']=0;
					$data['pya']['error']=$connectdb->error." ON $query";
				}
				
				break;
		}
		
		//INSERT DB Ausentismos
		if($tipo==12){
			$pyaComm=implode(" || ",$tmp_pya);
			$comments.=" $pyaComm";
		}
		
		if(($tipo==12 && $data['pya']['status']==1) || $tipo!=12){
			$query="INSERT INTO Ausentismos (asesor,tipo_ausentismo,Inicio,Fin,Descansos,Beneficios,User,caso,Moper,ISI,Comments) VALUES ($asesor,$tipo,'$inicio','$fin',$descansos,$beneficios,$user,$caso,$moper,$isMoper,'$comments')";
			if($result=$connectdb->query($query)){
				$data['ausentismo']['status']=1;
				$data['ausentismo']['id']=$connectdb->insert_id;
			}else{
				$data['ausentismo']['status']=0;
				$data['ausentismo']['error']=$connectdb->error." ON $query";
			}
		
		

			//UPDATE DB Pendientes Redimidos
			if($data['ausentismo']['status']==1){
				switch($tipo){
					case 5:
						$query="INSERT INTO `Dias Pendientes Redimidos` (id, dias, fecha, motivo, caso, User, id_ausentismo) VALUES ($asesor, $dias, '$inicio', '$motivo', $caso, $user, ".$data['ausentismo']['id'].")";
						if($result=$connectdb->query($query)){
							$data['dpr']['status']=1;
							$data['dpr']['id']=$connectdb->insert_id;
						}else{
							$data['dpr']['status']=0;
							$data['dpr']['error']=$connectdb->error." ON $query";
						}
						break;
				}
			}
		}else{
			$data['ausentismo']['status']=0;
		}
        
	}else{
		$data['ausentismo']['status']=10;
	}

print json_encode($data,JSON_PRETTY_PRINT);

$connectdb->close();
?>


