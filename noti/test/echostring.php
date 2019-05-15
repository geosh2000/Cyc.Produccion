<?php ?>
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
</head>
<?php
include("../connectDB.php");
header("Content-Type: text/html;charset=utf-8");

echo $_GET['name'];
mysql_query("SET NAMES 'utf8'");

$query="INSERT INTO TEST (nombre) VALUES ('".$_GET['name']."')";
mysql_query($query);
$id=mysql_insert_id();
$query="SELECT * FROM TEST WHERE id='$id'";

echo "<br>Resultado: ".mysql_result(mysql_query($query),0,'nombre');
?>
