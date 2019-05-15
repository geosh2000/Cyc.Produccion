<?php

session_start();
$this_page=$_SERVER['PHP_SELF'];
if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
date_default_timezone_set('America/Bogota');
$credential="upload_info";
$menu_uploads="class='active'";

?>

<?php
include("../connectDB.php");
header("Content-Type: text/html;charset=utf-8");

include("../common/scripts.php");

?>

<?php

include("../common/menu.php");

?>

En Construccion