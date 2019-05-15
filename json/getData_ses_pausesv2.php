<?php
exit;
include("../connectMYSQLI.php");
date_default_timezone_set('America/Bogota');

$mxzone = 'America/Mexico_City';
$cunzone = new DateTimeZone('America/Bogota');

function tzChange($time, $date, $oirg, $new){
	global $cunzone;
	$original= new DateTime("2016-10-30 07:00:00 America/Mexico_City");
	$original -> setTimezone($cunzone);

	return $original->format('Y-m-d H:i:s');
}



if($_GET['date']==NULL){$date=date('Y-m-d');}else{$date=date('Y-m-d',strtotime($_GET['date']));}
$user=$_GET['usuario'];

$query="SELECT id FROM userDB a, Asesores b WHERE a.username=b.Usuario AND userid='$user'";
$result=$connectdb->query($query);
$fila=$result->fetch_assoc();
$asesor=$fila['id'];

//get horario
$query="SELECT Fecha, `jornada start`, `jornada end`, `extra1 start`, `extra1 end`, `extra2 start`, `extra2 end`, `comida start`, `comida end`, LogAsesorTD(Fecha,asesor,'in') as login, LogAsesorTD(Fecha,asesor,'out') as logout FROM `Historial Programacion` WHERE (Fecha='$date' AND asesor='$asesor')";
if($result=$connectdb->query($query)){
	while($fila=$result->fetch_assoc()){
		$time = new DateTime($fila['Fecha']." ".$fila['jornada start']." America/Mexico_City");
		$time -> setTimezone($cunzone);
		$js = $time->format('Y-m-d H:i:s');

		if(date('H:i:s',strtotime($fila['jornada end']))<'04:00:00'){
			$time = new DateTime(date('Y-m-d', strtotime($fila['Fecha'].' +1 days'))." ".$fila['jornada end']." America/Mexico_City");
			$time -> setTimezone($cunzone);
			$je = $time->format('Y-m-d H:i:s');
		}else{
			$time = new DateTime($fila['Fecha']." ".$fila['jornada end']." America/Mexico_City");
			$time -> setTimezone($cunzone);
			$je = $time->format('Y-m-d H:i:s');
			if(date('H:i:s',strtotime($je))<'04:00:00'){
				$je = date('Y-m-d H:i:s',strtotime($je.' +1 days'));
			}
		}

		//Comidas
		$time = new DateTime($fila['Fecha']." ".$fila['comida start']." America/Mexico_City");
		$time -> setTimezone($cunzone);
		$cs = $time->format('Y-m-d H:i:s');
		if(date('H:i:s',strtotime($fila['comida end']))<'04:00:00'){
			$time = new DateTime(date('Y-m-d', strtotime($fila['Fecha'].' +1 days'))." ".$fila['comida end']." America/Mexico_City");
			$time -> setTimezone($cunzone);
			$ce = $time->format('Y-m-d H:i:s');
		}else{
			$time = new DateTime($fila['Fecha']." ".$fila['comida end']." America/Mexico_City");
			$time -> setTimezone($cunzone);
			$ce = $time->format('Y-m-d H:i:s');

		}

		//Extras
		if($fila['extra1 start']!=NULL && $fila['extra1 start']!=$fila['extra1 end']){
			$time = new DateTime($fila['Fecha']." ".$fila['extra1 start']." America/Mexico_City");
			$time -> setTimezone($cunzone);
			$x1s = $time->format('Y-m-d H:i:s');
			if(date('H:i:s',strtotime($fila['extra1 end']))<'04:00:00'){
				$time = new DateTime(date('Y-m-d', strtotime($fila['Fecha'].' +1 days'))." ".$fila['extra1 end']." America/Mexico_City");
				$time -> setTimezone($cunzone);
				$x1e = $time->format('Y-m-d H:i:s');
			}else{
				$time = new DateTime($fila['Fecha']." ".$fila['extra1 end']." America/Mexico_City");
				$time -> setTimezone($cunzone);
				$x1e = $time->format('Y-m-d H:i:s');
			}
		}else{
			$x1s=NULL;
		}

		if($fila['extra2 start']!=NULL && $fila['extra2 start']!=$fila['extra2 end']){
			$time = new DateTime($fila['Fecha']." ".$fila['extra2 start']." America/Mexico_City");
			$time -> setTimezone($cunzone);
			$x2s = $time->format('Y-m-d H:i:s');
			if(date('H:i:s',strtotime($fila['extra2 end']))<'04:00:00'){
				$time = new DateTime(date('Y-m-d', strtotime($fila['Fecha'].' +1 days'))." ".$fila['extra2 end']." America/Mexico_City");
				$time -> setTimezone($cunzone);
				$x2e = $time->format('Y-m-d H:i:s');
			}else{
				$time = new DateTime($fila['Fecha']." ".$fila['extra2 end']." America/Mexico_City");
				$time -> setTimezone($cunzone);
				$x2e = $time->format('Y-m-d H:i:s');
			}
		}else{
			$x2s=NULL;
		}


		//Logueo
		$time = new DateTime($fila['Fecha']." ".$fila['login']." America/Mexico_City");
		$time -> setTimezone($cunzone);
		$login = $time->format('Y-m-d H:i:s');
		if($fila['logout']==NULL){
			$logout=NULL;
		}else{
			if(date('H:i:s',strtotime($fila['logout']))<'04:00:00'){
				$time = new DateTime(date('Y-m-d', strtotime($fila['Fecha'].' +1 days'))." ".$fila['logout']." America/Mexico_City");
				$time -> setTimezone($cunzone);
				$logout = $time->format('Y-m-d H:i:s');
			}else{
				$time = new DateTime($fila['Fecha']." ".$fila['logout']." America/Mexico_City");
				$time -> setTimezone($cunzone);
				$logout = $time->format('Y-m-d H:i:s');

			}
		}
	}
}
unset($result);

$query="SELECT
            *
            FROM
                Comidas a, Tipos_pausas b
            WHERE
                a.tipo=b.pausa_id AND
                Fecha='$date' AND
                asesor='$asesor'";
if($result=$connectdb->query($query)){
	$i=0;
	while($fila=$result->fetch_assoc()){
			$time = new DateTime($fila['Fecha']." ".$fila['Inicio']." America/Mexico_City");
			$time -> setTimezone($cunzone);
			$pausas[$i]['inicio'] = $time->format('Y-m-d H:i:s');
			$time = new DateTime($fila['Fecha']." ".$fila['Fin']." America/Mexico_City");
			$time -> setTimezone($cunzone);
			$pausas[$i]['fin'] = $time->format('Y-m-d H:i:s');
			$pausas[$i]['tipo']=$fila['tipo'];
			$pausas[$i]['pausa']=$fila['Pausa'];
			$i++;

	}
}

//JSON
$a = array();
       $cols = array();
       $rows = array();
       $cols[] = array("id"=>"Sesion","label"=>"Sesion","pattern"=>"","type"=>"string");
       $cols[] = array("id"=>"Tipo","label"=>"Tipo","pattern"=>"","type"=>"string");
       $cols[] = array("id"=>"Start","label"=>"Start","pattern"=>"","type"=>"date");
       $cols[] = array("id"=>"End","label"=>"End %","pattern"=>"","type"=>"date");
       $rows[] = array("c"=>array(array("v"=>"Horario","f"=>null),array("v"=>"Horario","f"=>null),array("v"=>"Date(".date('Y,m,d,H,i,s)',strtotime($js)),"f"=>null),array("v"=>"Date(".date('Y,m,d,H,i,s)',strtotime($je)),"f"=>null)));
	   if($x1s!=NULL){
	   		$rows[] = array("c"=>array(array("v"=>"Horario","f"=>null),array("v"=>"Extra","f"=>null),array("v"=>"Date(".date('Y,m,d,H,i,s)',strtotime($x1s)),"f"=>null),array("v"=>"Date(".date('Y,m,d,H,i,s)',strtotime($x1e)),"f"=>null)));
	   }

		if($x2s!=NULL){
	   		$rows[] = array("c"=>array(array("v"=>"Horario","f"=>null),array("v"=>"Extra","f"=>null),array("v"=>"Date(".date('Y,m,d,H,i,s)',strtotime($x2s)),"f"=>null),array("v"=>"Date(".date('Y,m,d,H,i,s)',strtotime($x2e)),"f"=>null)));
		}
        if($logout!=NULL){
            $rows[] = array("c"=>array(array("v"=>"Sesion","f"=>null),array("v"=>"Logueo","f"=>null),array("v"=>"Date(".date('Y,m,d,H,i,s)',strtotime($login)),"f"=>null),array("v"=>"Date(".date('Y,m,d,H,i,s)',strtotime($logout)),"f"=>null)));
        }
        if(date('H:i:s',strtotime($cs)) == date('H:i:s',strtotime($ce))){}else{
        $rows[] = array("c"=>array(array("v"=>"Horario","f"=>null),array("v"=>"Horario Alimentos","f"=>null),array("v"=>"Date(".date('Y,m,d,H,i,s)',strtotime($cs)),"f"=>null),array("v"=>"Date(".date('Y,m,d,H,i,s)',strtotime($ce)),"f"=>null)));
        }


if(count($pausas)>0){

       foreach($pausas as $key => $info){
       	if(date('H',strtotime($info['inicio']))>7 && date('H')<4){
           		$pausa_inicio=date('Y-m-d H:i:s',strtotime($info['inicio'].' -1 days'));
           }else{
                $pausa_inicio=date('Y-m-d H:i:s',strtotime($info['inicio']));
           }
		   if(date('H',strtotime($info['fin']))>7 && date('H')<4){
               $pausa_fin=date('Y-m-d H:i:s',strtotime($info['fin'].' -1 days'));
           }else{
               $pausa_fin=date('Y-m-d H:i:s',strtotime($info['fin']));
           }
           $rows[] = array("c"=>array(array("v"=>"Sesion","f"=>null),array("v"=>$info['pausa'],"f"=>null),array("v"=>"Date(".date('Y,m,d,H,i,s)',strtotime($pausa_inicio)),"f"=>null),array("v"=>"Date(".date('Y,m,d,H,i,s)',strtotime($pausa_fin)),"f"=>null)));
       }
}

       $a = array("cols"=>$cols,"rows"=>$rows);




$connectdb->close();


echo  json_encode($a);

//echo  "HOLA ".$js;








?>
