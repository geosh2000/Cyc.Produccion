<?php

include_once("../modules/modules.php");

initSettings::startScreen(false);
timeAndRegion::setRegion("Cun");

$connectdb=Connection::mysqliDB('CC');
$connectdbcc=Connection::mysqliDB('WFM');

$skill=$_GET['dep'];

class liveMonitor{
	public $skill;
	public $dep;
	public $comida;
	public $aht;
	public $q;
	
	public function __construct($skill){
		$this->skill=$skill;
		
		switch($this->skill){
		    case 35:
		        $this->q="207|206|208";
		        $this->aht=550;
		        $this->comida=1800;
		        $this->dep="Ventas MP";
		        break;
		    case 3:
		        $this->q="224|227|232|234|259|";
		        $this->aht=550;
		        $this->comida=1800;
				$this->dep="Ventas MT";
		        break;
		    case 4:
		        $this->q="226|229|233|235|230|666";
		        $this->aht=700;
		        $this->comida=1800;
		        $this->dep="SAC IN";
		        break;
		    case 7:
		        $this->q="222|223";
		        $this->aht=550;
		        $this->comida=3600;
		        $this->dep="Agencias";
		        break;
		    case 9:
		        $this->q="231";
		        $this->aht=500;
		        $this->comida=1800;
		        $this->dep="TMP";
		        break;
		    case 8:
		        $this->q="236";
		        $this->aht=241;
		        $this->comida=3600;
		        $this->dep="TMT";
		        break;
		   case 5:
		        $this->dep="Upsell";
		        $this->aht=261;
		        $this->comida=1800;
				$this->aht=10000;
				break;
		}
	}
	
	public function startScripts(){
			
		echo "<link href='livemonitors.css' rel='stylesheet'>";
		echo "<script> path=''; skill=".$this->skill."; aht=".$this->aht."; comida=".$this->comida."; uri='".$_SERVER['HTTP_REFERER'].$_SERVER['PHP_SELF']."?tipo=".$_GET['tipo']."&dep='; </script>";
		echo "<script type='text/javascript' src='livemonitors.js'></script>";
	}

	public function printBlocks($tipo){
		
		switch($tipo){
			case 'h':
				$titulo="";
				break;
			case 'v':
				$titulo="<div class='titles'>Status Asesores</div><br>";
				break;
			case 'all':
				$titulo="<div class='titles' style='padding:10px; font-size: 50px;'>".$this->dep." // <lu id='LU'></lu></div><br>";
				break;
		}
		
		
		
		$resumen= "<div class='titles'>".$this->dep."<br><lu id='LU' style='font-size: 20px;'></lu></div>
				<br>
				<div class='resumen'>
				    <div class='container'><div class='res_icon'><img src=\"/images/online.png\" height=\"60\" width=\"60\"></div><div class='res_detail' id='res_online'></div></div>
				    <div class='container'><div class='res_icon'><img src=\"/images/avail.png\" height=\"60\" width=\"60\"></div><div class='res_detail' id='res_avail'></div></div>
				    <div class='container'><div class='res_icon'><img src=\"/images/paused.png\" height=\"60\" width=\"60\"></div><div class='res_detail' id='res_paused'></div></div>
				    <div class='container'><div class='res_icon'><img src=\"/images/waiting.png\" height=\"60\" width=\"60\"></div><div class='res_detail' id='res_waiting'></div></div>
				    <div class='container'><div class='res_icon'><img src=\"/images/inbound.png\" height=\"60\" width=\"60\"></div><div class='res_detail' id='res_inbound'></div></div>
				    <div class='container'><div class='res_icon'><img src=\"/images/outbound.png\" height=\"60\" width=\"60\"></div><div class='res_detail' id='res_outbound'></div></div>
				    <div class='container'><div class='res_icon'><img src=\"/images/aht.png\" height=\"60\" width=\"60\"></div><div class='res_detail' id='res_aht'></div></div>
					<div class='container'><div class='res_icon'><img src=\"/images/aht.png\" height=\"60\" width=\"60\"></div><div class='res_detail' id='res_lwait'></div></div>
					
				</div>";
				
		$esperas = "<div class='titles'>Colas</div>
					<br>
					<div style='width:1360px; margin: auto;'>
					<div class='resumen' id='res_esperas'>
						<div class='waits' skill='35'><div class='w_detail' id='w_ventasMP'></div><div class='w_dep'>Ventas MP</div></div>
						<div class='waits' skill='3'><div class='w_detail' id='w_ventas'></div><div class='w_dep'>Ventas</div></div>
						<div class='waits' skill='4'><div class='w_detail' id='w_sac'></div><div class='w_dep'>SAC</div></div>
						<div class='waits' skill='5'><div class='w_detail' id='w_upsell'></div><div class='w_dep'>Upsell</div></div>
						<div class='waits' skill='9'><div class='w_detail' id='w_tmp'></div><div class='w_dep'>TMP</div></div>
						<div class='waits' skill='8'><div class='w_detail' id='w_tmt'></div><div class='w_dep'>TMT</div></div>
						<div class='waits' skill='7'><div class='w_detail' id='w_agencias'></div><div class='w_dep'>Agencias</div></div>
					</div></div>";	
					
		$bloques="$titulo<div class='resumen_bloques' id='res_asesores'></div>
					<div class='resumen_bloques' id='res_asesores_pdv'></div>";	
				
		switch($tipo){
			case 'h':
				echo "<div id='resumen_display' style='vertical-align: top; display: inline-block; width: 388px; margin: 0; height:100%;'>$resumen<br>"
					.str_replace("<div style='width:1360px; margin: auto;'>", "", substr($esperas, 0, -6))."</div>"
					."<div id='blocks_display' style='display: inline-block; width: 100%; margin-left: 10px; height:100%;'>$bloques</div>";
				break;
			case 'v':
				echo "$resumen<br>$esperas<br>$bloques";
				break;
			case 'all':
				echo "<style>body{ zoom: 0.45; overflow: hidden;}</style>";
				echo $bloques;
				break;
		}
	}
}

$display = new liveMonitor($skill);

$display->startScripts();
$display->printBlocks($_GET['tipo']);

?>


