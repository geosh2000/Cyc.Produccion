<?php
include("../connectDB.php");

$idname="confirming_id";
$dbname="bo_confirming";

$query="SELECT *, COUNT(*) as cuenta FROM $dbname GROUP BY em, tipo_seguimiento, `user`, actividad, fecha_recepcion HAVING cuenta>1";
$result=mysql_query($query);
$num=mysql_numrows($result);
$i=0;
while($i<$num){
    $id[$i]=mysql_result($result,$i,$idname);
    $actividad[$i]=mysql_result($result,$i,'actividad');
    $tipo_seguimiento[$i]=mysql_result($result,$i,'tipo_seguimiento');
    $em[$i]=mysql_result($result,$i,'em');
    $user[$i]=mysql_result($result,$i,'user');
    $fecha[$i]=mysql_result($result,$i,'fecha_recepcion');
$i++;
}

foreach($id as $key => $iden){
    $query="DELETE FROM $dbname
        WHERE em=$em[$key] AND actividad=$actividad[$key] AND `user`=$user[$key] AND tipo_seguimiento=$tipo_seguimiento[$key] AND fecha_recepcion='$fecha[$key]' AND $idname!=$iden";
    mysql_query($query);
    echo "$iden:<br>";
    if(mysql_errno()){
			    echo "$key MySQL error ".mysql_errno().": "
			         .mysql_error()."\n<br>When executing <br>\n$query\n<br><br>";
                     $err_count++;
			}else{echo "$query\n<br>OK<br><br>";}
}

?>