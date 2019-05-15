<?php
$username="comeycom";
$password="dyj21278370";
$database="comeycom_SLA";
$localhost="67.225.221.130:3306";

//Conectar a DB

mysql_connect($localhost,$username,$password);
$query="CREATE DATABASE `fsdfa` ";
mysql_query($query);
if(mysql_errno()){
		    echo "$key1 // $key_xfered MySQL error ".mysql_errno().": "
		         .mysql_error()."\n<br>When executing <br>\n$query\n<br><br>";
		}
?>