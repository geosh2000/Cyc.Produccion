<?php
  include("../connectDB.php");
  date_default_timezone_set("America/Bogota");

  $start='2016-01-01';

  $i=0;
  while($i<1000){
       $query="INSERT INTO Fechas (Fecha) VALUES ('".date('Y-m-d',strtotime($start.' +'.$i.' days'))."')";
       mysql_query($query);
				if(mysql_errno()){
				    echo date('Y-m-d',strtotime($start.' +'.$i.' days'))." error ".mysql_errno().": "
				         .mysql_error()."\n<br>When executing <br>\n$query\n<br><br>";
				}
  $i++;
  }
?>