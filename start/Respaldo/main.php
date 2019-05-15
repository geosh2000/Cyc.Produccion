<?php

include_once("../modules/modules.php");
include_once("module_mainscreen.php");
include_once("module_fams.php");

timeAndRegion::setRegion('Cun');
initSettings::start(true);

switch($_SESSION['profile_name']){
	case 'afiliados':
		echo "<script>
						$(function(){
							window.location.replace('/afiliados');
						});
					</script>";

		exit;
		break;

}

initSettings::printTitle('Inicio - '.$_SESSION['name']);

Event::printScripts();

//fams_display __construct($asesor, $dep, $deps_array, $event_name,$event_corto,$tipo_evento,$event_date,$date_limit_inscript,$date_limit_display,$default_display)
$query="SELECT * FROM fams_display WHERE activo=1 AND '".date('Y-m-d')."' BETWEEN date_start_display AND date_limit_display ORDER BY event_date";
if($result=Queries::query($query)){
	$fields=$result->fetch_fields();
	$num=$result->field_count;
	while($fila=$result->fetch_array()){
		for($i=1;$i<$num;$i++){
			$fam[$fila['evento']][$fields[$i]->name]=$fila[$i];
		}
	}
}


if(isset($fam)){
		foreach($fam as $event => $info){
			$array_dep=explode("|",$info['departamentos']);
			$evento[$event] = new Event($_SESSION['asesor_id'], $_SESSION['dep'], $array_dep, $event, $info['evento_corto'], $info['tipo_evento'], $info['event_date'], $info['date_limit_inscript'], $info['date_limit_display'],$info['default_display']);
			unset($array_dep);
		}
		unset($info,$event);
}


if(!isset($_POST['asesor'])){
	$mainScreen= new mainScreen($_SESSION['asesor_id']);
}else{
	$mainScreen= new mainScreen($_POST['asesor']);
}

$mainScreen->startScripts();

if($_SESSION['view_all_agents']==1){
	$mainScreen->showFilter();
}

if(isset($evento)){
		foreach($evento as $display => $info){
			echo "<br>";
			$info->printEvent();
		}
}


echo "<br>";
$mainScreen->print_AsesorDetails();

echo "<br>";
$mainScreen->printHorarios();

switch($mainScreen->departamento){ case 3: case 35: case 29: case 30: case 5: echo "<br>"; $mainScreen->printGraphs(); break;}

echo "<br>";
//$mainScreen->printSesiones();
