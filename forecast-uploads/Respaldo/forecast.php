<?php
include_once("../modules/modules.php");

timeAndRegion::setRegion('Cun');

include_once("modules.php");

$forecast = new forecastModule($_GET['set']);

$forecast->start();
