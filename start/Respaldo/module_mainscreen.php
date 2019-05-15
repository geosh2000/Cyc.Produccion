<?php

class mainScreen{
	public $asesor;
	public $esquema;
	public $Nombre;
	public $num_colaborador;
	public $id;
	public $RFC;
	public $tel1;
	public $tel2;
	public $correo;
	public $pasaporte;
	public $visa;
	public $locker;
	public $departamento;

	public function __construct($asesor){
		$this->asesor=$asesor;

		$query="SELECT
            Nombre, Esquema, num_colaborador, id, RFC, telefono1, telefono2, correo_personal, Vigencia_Pasaporte, Vigencia_Visa, Locker, getDepartamento(a.id,CURDATE()) as departamento, 
            IF(c.comida IS NULL,1,c.comida) as comida, WEEKDAY(ADDDATE(CURDATE(),-1))+1 as DOW
        FROM
            Asesores a
        LEFT JOIN
        		Lockers b ON a.id=b.asesor
      	LEFT JOIN
      		horarios_position_select c ON a.id=c.asesor AND WEEK(ADDDATE(CURDATE(),-1))+1=c.semana AND YEAR(CURDATE())=c.year
        WHERE
            id=".$this->asesor;

		if($result=Queries::query($query)){
			$fila=$result->fetch_assoc();
			$this->Nombre=utf8_encode($fila['Nombre']);
			$this->num_colaborador=utf8_encode($fila['num_colaborador']);
			$this->id=utf8_encode($fila['id']);
			$this->RFC=utf8_encode($fila['RFC']);
			$this->tel1=utf8_encode($fila['telefono1']);
			$this->tel2=utf8_encode($fila['telefono2']);
			$this->correo=utf8_encode($fila['correo_personal']);
			$this->pasaporte=utf8_encode($fila['Vigencia_Pasaporte']);
			$this->visa=utf8_encode($fila['Vigencia_Visa']);
			$this->locker=utf8_encode($fila['Locker']);
			$this->esquema=utf8_encode($fila['Esquema']);
			$this->departamento=utf8_encode($fila['departamento']);
			$this->diadema=utf8_encode($fila['diadema']);
			$this->comida=$fila['comida'];
			
			if($fila['DOW']>=4){
        $this->comidaEnable="disabled";
			}else{
        $this->comidaEnable="";
			}

		}else{
			echo Queries::error();
		}
	}

	public function startScripts(){
		echo "<link href='".MODULE_PATH."mainscreen.css' rel='stylesheet'>";
		echo "<script> path='".MODULE_PATH."'; thisasesor=".$this->asesor."; esquema=".$this->esquema."; nameasesor='".$this->Nombre."'; </script>";
		echo "<script type='text/javascript' src='/js/tablesorter/js/widgets/widget-editable.js'></script>";
		echo "<script type='text/javascript' src='".MODULE_PATH."mainscreen.js'></script>";

		echo "<div id='dialog-loader' title='Loading...' style='text-align: center'>
				<div id='progressbarloader'></div>
			</div>";


	}

	public function print_AsesorDetails(){
		echo "<table class='tablesorter' style='text-align:center; width:1200px; margin: auto'>
				<thead>
				    <tr>
				    <th>Nombre</th>
				    <th>Colaborador</th>
				    <th>RFC</th>
				    <th>Tel 1</th>
				    <th>Tel 2</th>
				    <th>Correo Personal</th>
				    <th>Vigencia Pasaporte</th>
				    <th>Vigencia Visa</th>
				    <th>Locker</th>
				    <th>Diadema</th>
				    <th title='Selecciona si quieres o no tiempo de comida para la proxima semana'>Comida<br><span style='font-size: 10px'>(Siguiente Semana)</span></th>
				    </tr>
				</thead>
				<tbody>
				    <tr id='".$this->id."'>

				        <td col='Nombre'>".$this->Nombre."</td>
				        <td col='num_colaborador'>".$this->num_colaborador."</td>
				        <td col='RFC'>".$this->RFC."</td>
				        <td col='telefono1' style='background:white'>".$this->tel1."</td>
				        <td col='telefono2' style='background:white'>".$this->tel2."</td>
				        <td col='correo_personal' style='background:white'>".$this->correo."</td>
				        <td col='Vigencia_Pasaporte'><input type='text' value='".$this->pasaporte."' id='pasaporte' name='pasaporte' col='Vigencia_Pasaporte' index='".$this->id."' style='width:84px; text-align:center'></td>
				        <td col='Vigencia_Visa'><input type='text' value='".$this->visa."' id='visa' name='visa' col='Vigencia_Visa' index='".$this->id."' style='width:84px; text-align:center'></td>
				        <td col='locker'>".$this->locker."</td>
				        <td col='diadema'>".$this->diadema."</td>
				        <td col='comida'><label for='comida'>Comida</label><input title='Selecciona si quieres o no tiempo de comida para la proxima semana' type='checkbox' value='".$this->comida."' class='comida' name='comida' id='comida' ".$this->comidaEnable."></td>
				    </tr>
				</tbody>
				</table>";
	}

	public function showFilter(){

		echo "<div style='background:#99bfe6; height: 50px; margin: 0;'><form action='".$_SERVER['REQUEST_URI']."' method='POST'>";
		echo "<table style='width:600px; margin: auto; text-align: center;'><tr><th><input type='text' id='name' placeholder='Nombre del asesor' size=50><input type='hidden' name='asesor' id='asesorID'></th><th><button class='button button_red_w' id='search'>Ver</button></th></tr></table></form></div>";

	}

	public function printHorarios(){

		//Horarios
		$query="SELECT
			f.Fecha, `jornada start`, `jornada end`, `comida start`, `comida end`, `extra1 start`, `extra1 end`, `extra2 start`, `extra2 end`, getAusentismo(asesor,f.Fecha,1) as Ausentismo
		FROM
			Fechas f
		LEFT JOIN
			(SELECT * FROM `Historial Programacion` WHERE asesor=".$this->asesor." AND Fecha BETWEEN CURDATE() AND DATE_ADD(CURDATE(),INTERVAL +6 DAY)) a ON f.Fecha=a.Fecha
		WHERE
			f.Fecha BETWEEN CURDATE() AND DATE_ADD(CURDATE(),INTERVAL +6 DAY)";


		if($result=Queries::query($query)){
			$num_fields=$result->field_count;
			$fields=$result->fetch_fields();
			$x=0;
			while($fila=$result->fetch_row()){
				for($i=0;$i<$num_fields;$i++){
					$data_horarios[$x][$fields[$i]->name]=$fila[$i];
				}
				$x++;
			}
		}

		foreach($data_horarios as $index => $info){
			if($info['jornada start']==$info['jornada end']){
				if($info['jornada start']==NULL){
					$horarios[$info['Fecha']]="No Capturado";
				}else{
					$horarios[$info['Fecha']]="Descanso";
				}
			}else{
				$js = new DateTime(date('Y-m-d', strtotime($info['Fecha'].' +0 day')).' '.$info['jornada start'].' America/Mexico_City');
				$js -> setTimezone($GLOBALS['cun_time']);
				$je = new DateTime(date('Y-m-d', strtotime($info['Fecha'].' +0 day')).' '.$info['jornada end'].' America/Mexico_City');
				$je -> setTimezone($GLOBALS['cun_time']);

				$horarios[$info['Fecha']]=$js->format('H:i')." - ".$je->format('H:i');
				$horarios_in[$info['Fecha']]=$js->format('H:i:s');
				$horarios_out[$info['Fecha']]=$je->format('H:i:s');
				$ausentismo[$info['Fecha']]=$info['Ausentismo'];
			}

			if($info['comida start']==$info['comida end']){
				if($info['comida start']==NULL){
					$comidas[$info['Fecha']]="NA";
				}else{
					$comidas[$info['Fecha']]="NA";
				}
			}else{
				$cs = new DateTime(date('Y-m-d', strtotime($info['Fecha'].' +0 day')).' '.$info['comida start'].' America/Mexico_City');
				$cs -> setTimezone($GLOBALS['cun_time']);
				$ce = new DateTime(date('Y-m-d', strtotime($info['Fecha'].' +0 day')).' '.$info['comida end'].' America/Mexico_City');
				$ce -> setTimezone($GLOBALS['cun_time']);

				$comidas[$info['Fecha']]=$cs->format('H:i')." - ".$ce->format('H:i');
			}

			if($info['extra1 start']==$info['extra1 end']){
				if($info['jornada start']==NULL){
					$x1[$info['Fecha']]="NA";
				}else{
					$x1[$info['Fecha']]="NA";
				}
			}else{
				$x1s = new DateTime(date('Y-m-d', strtotime($info['Fecha'].' +0 day')).' '.$info['extra1 start'].' America/Mexico_City');
				$x1s -> setTimezone($GLOBALS['cun_time']);
				$x1e = new DateTime(date('Y-m-d', strtotime($info['Fecha'].' +0 day')).' '.$info['extra1 end'].' America/Mexico_City');
				$x1e -> setTimezone($GLOBALS['cun_time']);

				$x1[$info['Fecha']]=$x1s->format('H:i')." - ".$x1e->format('H:i');
			}

			if($info['extra2 start']==$info['extra2 end']){
				if($info['extra2 start']==NULL){
					$x2[$info['Fecha']]="NA";
				}else{
					$x2[$info['Fecha']]="NA";
				}
			}else{
				$x2s = new DateTime(date('Y-m-d', strtotime($info['Fecha'].' +0 day')).' '.$info['extra2 start'].' America/Mexico_City');
				$x2s -> setTimezone($GLOBALS['cun_time']);
				$x2e = new DateTime(date('Y-m-d', strtotime($info['Fecha'].' +0 day')).' '.$info['extra2 end'].' America/Mexico_City');
				$x2e -> setTimezone($GLOBALS['cun_time']);

				$x2[$info['Fecha']]=$x2s->format('H:i')." - ".$x2e->format('H:i');
			}

		}


		echo "<table style='width:80%; margin: auto; text-align:center' class='t2'>"
			    ."<tr class='title'><th colspan=100>Horarios de la Semana</th></tr>"
			    ."<tr class='title'>";

			   foreach($horarios as $date => $info){
			        		echo "<th>".date('l',strtotime($date))."<br>$date</th>";
			        	}

	   echo "</tr><tr class='odd' id='horarios'>";
				foreach($horarios as $date => $info){
	        		echo "<td>J: $info<br>";

	        		if($comidas[$date]!="NA"){
	        			echo "C: ".$comidas[$date]."<br>";
	        		}

					if($x1[$date]!="NA"){
	        			echo "X1: ".$x1[$date]."<br>";
	        		}

					if($x2[$date]!="NA"){
	        			echo "X2: ".$x2[$date]."<br>";
	        		}

					echo $ausentismo[$date]."</td>";
	        	}

		echo "</tr></table>";
	}

	public function printGraphs(){
	 	$query="SELECT Meta_Individual, Meta_Diaria, Meta_Diaria_Total FROM metas WHERE mes=".date('m')." AND anio=".date('Y')." AND skill=".$this->departamento;
		if($result=Queries::query($query)){
			$fila=$result->fetch_assoc();
			$meta=$fila['Meta_Individual'];
            $metad=$fila['Meta_Diaria'];
            $metadt=$fila['Meta_Diaria_Total'];
		}

		$querymonto="query.php?&dep=".$this->departamento."&type=montos&asesor=".$this->asesor."&fechai=".date('Y-m').'-01'."&fechaf=".date('Y-m-d',strtotime(date('Y-m',strtotime($startmonth.' +1 month'))."-01 -1 days"))."&mdt=$metadt&mt=$meta";
		$queryfc="query.php?type=fc&asesor=".$this->asesor."&fechai=".date('Y-m').'-01'."&dep=".$this->departamento."&fechaf=".date('Y-m-d',strtotime(date('Y-m',strtotime($startmonth.' +1 month'))."-01 -1 days"));
		$vars="fechai='".date('Y-m').'-01'."'; dep= '".$this->departamento."'; fechaf='".date('Y-m-d',strtotime(date('Y-m',strtotime($startmonth.' +1 month'))."-01 -1 days"))."'; mdt=$metadt; mt= $meta;";
		echo "<script> metamax=".($meta*1.5)."; metamonto=$meta; querymonto='".MODULE_PATH."$querymonto'; queryfc='".MODULE_PATH."$queryfc'; query='".MODULE_PATH."query.php'; $vars </script>";
		echo "<script src='/js/highcharts/highcharts.js'></script><script src='/js/highcharts/modules/exporting.js'></script>";
		echo "<link href='".MODULE_PATH."mainscreen_graphs.css' rel='stylesheet'>";
		echo "<script type='text/javascript' src='".MODULE_PATH."mainscreen_graphs.js'></script>";

		echo "<div id='carousel_container'><div id='carousel_inner'>
		        <div id='left_scroll' style='float: left;position: relative;width: 30px;height: 0;background: navy;top: 150px;z-index: 1000;'><img src='/images/left_arrow.png' /></div>
		        <div id='right_scroll' style='float: right;position: relative;width: 30px;height: 0;background: navy;top: 150px;z-index: 1000;'><img src='/images/right-arrow.png' /></div>
		        <ul id='carousel_ul'><li  id='monto'></li><li  id='fc'></li></ul></div>
				</div>";

	}

	public function printSesiones(){


		echo "<table style='width:80%; margin: auto' class='t2'>
			    <tr class='title'>
			        <th colspan=100>Sesion del dia</th>
			    </tr>
			    <tr class='title'>
			        <td>Esquema</td>
			        <td>Total de<br>Pausas No Productivas</td>
			        <td>Tiempo total en<br>Pausa no Productiva</td>
			        <td>Tiempo restante para<br>Pausas No Productivas</td>
			        <td>Tiempo Excedido de<br>Pausas No Productivas</td>
			    </tr>
			    <tr class='odd' id='tiempos'>
					<td>".$this->esquema."</td>
					<td id='ses_totalPNP'></td>
					<td id='ses_ttPNP'></td>
					<td id='ses_trPNP'></td>
					<td id='ses_tePNP'></td>
			    </tr>
			    <tr class='pair'>
			        <td colspan=100 id='timeline' style='height:250px'></td>
			    </tr>
			</table>";
	}
}
