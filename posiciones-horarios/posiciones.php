<?php
include_once("../modules/modules.php");
include_once('modules.php');

timeAndRegion::setRegion('Cun');
initSettings::start(true,'schedules_upload');
initSettings::printTitle('Asignación de Posiciones');

Scripts::periodScript('inicio', 'fin');

$posiciones = new posicionHModule();

$posiciones->printFilter();
$posiciones->printBlocks();


