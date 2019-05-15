<?
session_start();
include("../connectDB.php");
$hostsipaddress = str_replace("\n","",shell_exec("ifconfig eth0 | grep 'inet addr' | awk -F':' {'print $2'} | awk -F' ' {'print $1'}"));
$query="INSERT INTO `Detalles de Logueo` (user, tipo,`IP Internal`, `IP Remote Addr`, `IP Fowarded`,Page,Path) VALUES ('".$_SESSION['id']."','logout','".$_SERVER['SERVER_ADDR']."','".$_SERVER['REMOTE_ADDR']."','$hostsipaddress','".$_SERVER['PHP_SELF']."','".$_SERVER['REQUEST_URI']."')";
mysql_query($query);

session_destroy();
header("Location: ../home");
?>