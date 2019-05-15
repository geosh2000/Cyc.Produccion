<?php
include("../connectDB.php");

$id=$_GET['id'];

$query="UPDATE noti_mensajes SET `read`=1 WHERE id='$id'";
mysql_query($query);
echo $query;


?>