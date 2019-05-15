<?php
include("../connectDB.php");

$query="SELECT * FROM `Historial Llamadas`";
$result=mysql_query($query);
$num=mysql_numrows($result);
$err_count=0;

$i=0;
while($i<$num){
     $id[$i]=mysql_result($result,$i,'id');
     $dia[$i]=mysql_result($result,$i,'Dia');
     $mes[$i]=mysql_result($result,$i,'Mes');
     $anio[$i]=mysql_result($result,$i,'Anio');
     $fecha[$i]=$anio[$i]."/".$mes[$i]."/".$dia[$i];
$i++;
}


echo "$num Registers to edit...<br><br>";

foreach($id as $key => $iden){
    $date=date('Y/m/d',strtotime($fecha[$key]));
    $query="UPDATE `Historial Llamadas` SET Fecha='$date' WHERE id='$iden'";
    mysql_query($query);
				if(mysql_errno()){
				    echo "$key // $iden MySQL error ".mysql_errno().": "
				         .mysql_error()."\n<br>When executing <br>\n$query\n<br><br>";
                         $err_count++;
				}else{ echo "$key // $iden OK // $query // $fecha[$key]<br><br>";}
}
echo "DONE!<br>$err_count Errors found!";
?>