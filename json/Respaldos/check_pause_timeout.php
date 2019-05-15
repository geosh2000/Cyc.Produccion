<?php
include("../connectDB.php");
header("Content-Type: text/html;charset=utf-8");  
date_default_timezone_set('America/Bogota');

//Validacion de tiempo total de PNP
include("noti_json/pauses_exceeded.php");

//Validacion de tiempo total Alimentos
include("noti_json/comidas_exceeded.php");

//Validacion de Faltas
include("noti_json/faltas_check.php");

//Validacion de Pausas Bloqueadas
include("noti_json/blocked_pauses.php");

//Validacion de tiempo de ACW
include("noti_json/acw_check.php");

//Validacion de tiempo de PNP
include("noti_json/pnp_check.php");

?>