<?php

include_once('../modules/modules.php');

initSettings::startScreen(false);
initSettings::printTitle('RT Monitor');

timeAndRegion::setRegion("Cun");

$connectdb=Connection::mysqliDB('CC');
$connectdbcc=Connection::mysqliDB('WFM');

$skill=$_GET['skill'];

class liveMonitor{
	public $skill;
	public $dep;
	public $comida;
	public $aht;
	public $q;
	public $asesoresJSON;
	public $zoom;

	public function __construct($skill){

		$this->skill=$skill;

		$tmpconnect=Connection::mysqliDB('CC');

		$query="SELECT Skill, queue FROM Cola_Skill";
		if($result=$tmpconnect->query($query)){
			while($fila=$result->fetch_assoc()){
				$queues[$fila['Skill']][]=$fila['queue'];
			}
		}

		foreach($queues as $sk => $info){
			$qs[$sk]=implode('|',$info);
		}

		$query="SELECT 
            `N Corto`, 
            CASE
              WHEN cc IS NOT NULL THEN 'Apoyo'
              WHEN dep=29 THEN 'PDV'
              ELSE Departamento
            END as Departamento,
            cc
          FROM 
            Asesores a 
          LEFT JOIN 
            daily_dep b ON a.id=b.asesor
          LEFT JOIN
            cc_apoyo c ON a.id=c.asesor AND CURDATE() BETWEEN inicio AND fin
          LEFT JOIN
            PCRCs d ON dep=d.id";
		if($result=$tmpconnect->query($query)){
			while($fila=$result->fetch_assoc()){
				$asesores[utf8_encode($fila['N Corto'])]=utf8_encode($fila['Departamento']);
			}
		}

		$this->asesoresJSON=json_encode($asesores);
		//$this->asesoresJSON="json_encode($asesores)";

		$tmpconnect->close();

		$this->q=$qs[$this->skill];

		switch($this->skill){
		    case 35:
		        //$this->q="207|206|208";
						$this->aht=550;
		        $this->comida=1800;
		        $this->dep="Ventas MP";
		        break;
		    case 3:
		        //$this->q="224|227|232|234|259|";
		        $this->aht=550;
		        $this->comida=1800;
				$this->dep="Ventas MT";
		        break;
		    case 4:
		        //$this->q="226|229|233|235|230|666";
		        $this->aht=700;
		        $this->comida=1800;
		        $this->dep="SAC IN";
		        break;
		    case 7:
		        //$this->q="222|223";
		        $this->aht=550;
		        $this->comida=3600;
		        $this->dep="Agencias";
		        break;
		    case 9:
		        //$this->q="231";
		        $this->aht=500;
		        $this->comida=1800;
		        $this->dep="TMP";
		        break;
				case 8:
		        //$this->q="236";
		        $this->aht=241;
		        $this->comida=3600;
		        $this->dep="TMT";
		        break;
				case 11:
						//$this->q="218";
						$this->aht=550;
						$this->comida=3600;
						$this->dep="Mesa de Expertos";
						break;
		   case 5:
		        $this->dep="Upsell";
		        $this->aht=261;
		        $this->comida=1800;
						$this->aht=10000;
						break;
			default:
		        $this->dep=$this->skill;
		        $this->aht=500; 
		        $this->comida=1800;
						$this->aht=10000;
						break;
		}
	}

	public function startScripts(){

		if($_GET['q']==1){
			$qType='cola';
		}else{
			$qType='newRTMon';
		}

		echo "<link href='".MODULE_PATH."livemonitors.css' rel='stylesheet'>";
		echo "<script> path='".MODULE_PATH."'; skill=".$this->skill."; aht=".$this->aht."; comida=".$this->comida."; qType='".$qType."'; uri='".$_SERVER['HTTP_REFERER'].$_SERVER['PHP_SELF']."?module=rtmonitor&tipo=".$_GET['tipo']."&skill='; </script>";
		echo "<script> asesores = ".$this->asesoresJSON.";</script>";
		echo "<script type='text/javascript' src='".MODULE_PATH."livemonitors.js'></script>";

		if(isset($_GET['zoom'])){
			$this->zoom=$_GET['zoom'];
		}else{
			$this->zoom=".7";
		}
	}

	public function printBlocks($tipo){

		switch($tipo){
			case 'h':
				$titulo="";
				$font_skill="style='font-size:35px;'";
				break;
			case 'v':
				$titulo="<div class='titles'>Status Asesores</div><br>";
				$font_skill="";
				break;
			case 'all':
				$titulo="<div class='titles' style='padding:10px; font-size: 50px;'><span id='monTitle'>".$this->dep."</span> // <lu id='LU'></lu></div><br>";
				$font_skill="";
				break;
		}


		$resumen_div="<div class='resumen'>
					<div class='container'><div class='res_icon'><img src=\"/images/online.png\" height=\"60\" width=\"60\"></div><div class='res_detail' id='res_online'></div></div>
					<div class='container'><div class='res_icon'><img src=\"/images/avail.png\" height=\"60\" width=\"60\"></div><div class='res_detail' id='res_avail'></div></div>
					<div class='container'><div class='res_icon'><img src=\"/images/paused.png\" height=\"60\" width=\"60\"></div><div class='res_detail' id='res_paused'></div></div>
					<div class='container'><div class='res_icon'><img src=\"/images/waiting.png\" height=\"60\" width=\"60\"></div><div class='res_detail' id='res_waiting'></div></div>
					<div class='container'><div class='res_icon'><img src=\"/images/inbound.png\" height=\"60\" width=\"60\"></div><div class='res_detail' id='res_inbound'></div></div>
					<div class='container'><div class='res_icon'><img src=\"/images/outbound.png\" height=\"60\" width=\"60\"></div><div class='res_detail' id='res_outbound'></div></div>
					<div class='container'><div class='res_icon'><img src=\"/images/aht.png\" height=\"60\" width=\"60\"></div><div class='res_detail' id='res_aht'></div></div>
				<div class='container'><div class='res_icon'><img src=\"/images/aht.png\" height=\"60\" width=\"60\"></div><div class='res_detail' id='res_lwait'></div></div>

			</div>";
		$resumen= "<div class='titles' $font_skill><span id='monTitle'>".$this->dep."</span><br><lu id='LU' style='font-size: 20px;'></lu></div>
				<br>$resumen_div";

		$esperas = "<div style='width:1044px; margin: auto; clear: both'>
					<div class='resumen' id='res_esperas' style='clear: both'>
						<div class='waits' skill='35'><div class='w_detail' id='w_ventasMP'></div><div class='w_dep'>Ventas MP</div></div>
						<div class='waits' skill='3'><div class='w_detail' id='w_ventas'></div><div class='w_dep'>Ventas</div></div>
						<div class='waits' skill='4'><div class='w_detail' id='w_sac'></div><div class='w_dep'>SAC</div></div>
						<div class='waits' skill='5'><div class='w_detail' id='w_upsell'></div><div class='w_dep'>Upsell</div></div>
						<div class='waits' skill='9'><div class='w_detail' id='w_tmp'></div><div class='w_dep'>TMP</div></div>
						<div class='waits' skill='8'><div class='w_detail' id='w_tmt'></div><div class='w_dep'>TMT</div></div>
						<div class='waits' skill='7'><div class='w_detail' id='w_agencias'></div><div class='w_dep'>Agencias</div></div>
					</div></div>";

		$bloques="$titulo<div class='resumen_bloques' id='res_asesores'></div>
					<div class='resumen_bloques' id='res_asesores_apoyo'></div><div class='resumen_bloques' id='res_asesores_pdv'></div>";

		switch($tipo){
			case 'h':
				echo "<div id='resumen_display' style='vertical-align: top; display: inline-block; width: 200; margin: 0; height:100%;'>$resumen<br>"
					."</div>"
					."<div id='blocks_display' style='display: inline-block; width: 100%; margin-left: 10px; height:100%;'>"
					.str_replace("<div style='width:1044px; margin: auto; clear: both'>", "", substr($esperas, 0, -6))."<br>$bloques</div>";
				break;
			case 'v':
				echo "$resumen<br>$esperas<br>$bloques";
				break;
			case 'all':
				echo "<style>body{ zoom: 0.45; overflow-x: hidden;}</style>";
				echo $bloques;
				break;
			case 'queues':
				echo "<style>body{ zoom: $this->zoom; overflow-x: hidden;}</style>";
				echo "$resumen<br>$bloques";
				break;

		}
	}
}

$display = new liveMonitor($skill);

$display->startScripts();
$display->printBlocks($_GET['tipo']);

?>
