<?php
//Connect to DB
include("connectDB.php");

//Configure Time

	date_default_timezone_set('America/Bogota');
	$lupdate=date('H\:i \h\r\s\.   d\-m\-Y');
	
//Get variables
	$day=$_GET['d'];
	$month=$_GET['m'];
	$year=$_GET['y'];
    $okdate1=$year."-".$month."-".$day;
    $okdate=date('Y-m-d',strtotime($okdate1));
    $sk=$_GET['s'];
	$sla_time=$_GET['sla'];
	$asesor=$_GET['a'];
	$horario_in=$_GET['in'];
	$horario_out=$_GET['out'];
	switch ($sk){
		case "V":
			$skill="Ventas";
			break;
		case "SC":
			$skill="Servicio a Cliente";
			break;
		case "MP":
			$skill="Trafico MP";
			break;
		case "MT":
			$skill="Trafico MT";
			break;
		case "A":
			$skill="Soporte Agencias";
			break;
        case "C":
			$skill="Corporativo";
			break;
		default:
			$ErrorMsg=" // Nothing Modified -- No Skill Defined or Bad Skill";
			goto GETError;
			break;
	}
	$i=1;
	
	while($i<=48){
		$calls[$i]=$_GET["c$i"];
		$sla[$i]=$_GET["s$i"];
		$aht[$i]=$_GET["a$i"];
		$forecast[$i]=$_GET["f$i"];
	$i++;
	}


//Evaluate if REG exists

	//Create Queries
		$queryCalls="SELECT `id` FROM `Historial Llamadas` WHERE `Skill`='$skill' AND `Dia`='$day' AND `Mes`='$month' AND `Anio`='$year'";
		$resultCalls=mysql_query($queryCalls);
		$id=mysql_result($resultCalls,0,'id');
		if($id==NULL){
			$query="INSERT INTO `Historial Llamadas` (`Dia`, `Mes`, `Anio`, `Skill`, Fecha) VALUES ('$day', '$month', '$year', '$skill','$okdate')";
			mysql_query($query);
			$resultCalls=mysql_query($queryCalls);
			$id=mysql_result($resultCalls,0,'id');
			echo "<br>Row added to table Calls, with id:$id";
		}
		$queryAHT="SELECT `id` FROM `Historial Llamadas AHT` WHERE `id`='$id'";
		$querySLA="SELECT `id` FROM `Historial Llamadas SLA` WHERE `id`='$id' AND `time`='$sla_time'";
		$queryForecast="SELECT `id` FROM `Historial Llamadas Forecast` WHERE `id`='$id'";
		$queryHorarios="SELECT `id` FROM `Historial Programacion` WHERE `id`='$id' AND `asesor`=$asesor";
	
	//Run Queries
		$resultAHT=mysql_query($queryAHT);
		$resultSLA=mysql_query($querySLA);
		$resultForecast=mysql_query($queryForecast);
		$resultHorarios=mysql_query($queryHorarios);
	
	
	//Get id and Create Missing Rows
		$id=mysql_result($resultCalls,0,'id');
		if($id==NULL){
			$query="INSERT INTO `Historial Llamadas` (`Dia`, `Mes`, `Anio`, `Skill`, Fecha) VALUES ('$day', '$month', '$year', '$skill','$okdate')";
			mysql_query($query);
			$resultCalls=mysql_query($queryCalls);
			$id=mysql_result($resultCalls,0,'id');
			echo "<br>Row added to table Calls, with id:$id";
		}
		function insertRowDB($DB){
			global $id, $sla_time, $asesor;
			switch($DB){
				case "SLA":
					if($sla_time==NULL){
						$query="";
					}else{		
						$query="INSERT INTO `Historial Llamadas $DB` (`id`, `time`) VALUES ('$id', '$sla_time')";
					}
					break;
				case "Horarios":
					if($asesor==NULL){
						$query="";
					}else{		
						$query="INSERT INTO `Historial Programacion` (`id`, `asesor`) VALUES ('$id', '$asesor')";
					}
					break;
				default:
					$query="INSERT INTO `Historial Llamadas $DB` (`id`) VALUES ('$id')";
					break;
			}
			mysql_query($query);
            if(mysql_errno()){
		    echo "$key1 // $key_xfered MySQL error ".mysql_errno().": "
		         .mysql_error()."\n<br>When executing <br>\n$query\n<br><br>";
            $qerror="INSERT INTO Errores (site, error, query,string) VALUES ('pt/update_Calls.php','".mysql_errno()."','$query','".$_SERVER["QUERY_STRING"]."')";
            mysql_query($qerror);
		}
			echo "<br>Row added to table $DB, with id:$id";
		}
		$numAHT=mysql_numrows($resultAHT);
        $numSLA=mysql_numrows($resultSLA);
        $numForecast=mysql_numrows($resultForecast);
        $numHorarios=mysql_numrows($resultHorarios);
		if($numAHT==0){ insertRowDB('AHT');}
		if($numSLA==0){ insertRowDB('SLA');}
		if($numForecast==0){ insertRowDB('Forecast');}
		if($numHorarios==0){ insertRowDB('Horarios');}

//Update Registries

	function updateRowDB($column,$DB){
		global $calls, $sla, $aht, $forecast, $id, $lupdate, $sla_time, $okdate;

		switch ($DB){
			case "Calls":
				$value=$calls[$column];
				$query="UPDATE `Historial Llamadas` SET `$column`='$value',Fecha='$okdate' WHERE `id`='$id'";
				break;
			case "AHT":
				$value=$aht[$column];
				$query="UPDATE `Historial Llamadas AHT` SET `$column`='$value', `LastUpdate`='$lupdate' WHERE `id`='$id'";
				break;
			case "SLA":
				$value=$sla[$column];
				$query="UPDATE `Historial Llamadas SLA` SET `$column`='$value', `LastUpdate`='$lupdate' WHERE `id`='$id' AND `time`='$sla_time'";
				break;
			case "Forecast":
				$value=$forecast[$column];
				$query="UPDATE `Historial Llamadas Forecast` SET `$column`='$value', `LastUpdate`='$lupdate' WHERE `id`='$id'";
				break;
		}
		
		mysql_query($query);

        if(mysql_errno()){
		    echo "$key1 // $key_xfered MySQL error ".mysql_errno().": "
		         .mysql_error()."\n<br>When executing <br>\n$query\n<br><br>";
            $qerror="INSERT INTO Errores (site, error, consulta, string) VALUES ('pt/update_Calls.php','".mysql_error()."','$query','".$_SERVER["QUERY_STRING"]."')";
            mysql_query($qerror);
		}
        $qdb="INSERT INTO Queries (query) VALUES ('$query')";
        mysql_query($qdb);
		echo "<br>$DB.$column on $id UPDATED to value: $value";
	}
	
	$i=1;
	while($i<=48){
		if($calls[$i]!=NULL){updateRowDB($i,'Calls');}
		if($aht[$i]!=NULL && $aht[$i]!=(-1)){updateRowDB($i,'AHT');}
		if($sla[$i]!=NULL && $sla[$i]!=(-1)){updateRowDB($i,'SLA');}
		if($forecast[$i]!=NULL){updateRowDB($i,'Forecast');}
	$i++;
	}

SLAError:
GETError:	
echo "<br><br>Code Finished: $lupdate $ErrorMsg";

?>