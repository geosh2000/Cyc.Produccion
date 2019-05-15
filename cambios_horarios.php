<?php
include("connectDB.php");
include("common/scripts.php");

$query="SELECT * FROM `Historial Programacion` WHERE `change`!=0 AND Fecha>='2016-09-15'";
$result=mysql_query($query);
$num=mysql_numrows($result);
$i=0;
while($i<$num){
    $id[$i]=mysql_result($result,$i,'id');
    $cambio[$i]=mysql_result($result,$i,'change');
$i++;
}

echo "Start ".($num-1)." Regs...<br><br>";
foreach($id as $key => $horario){
    $query="SELECT * FROM `Cambios de Turno` WHERE id='$cambio[$key]'";
    $result=mysql_query($query);
    if(mysql_numrows($result)!=0){
        $q_change="UPDATE `Historial Programacion` SET `jornada start`='".mysql_result($result,0,'jornada start new')."', `jornada end`='".mysql_result($result,0,'jornada end new')."', `comida start`='".mysql_result($result,0,'comida start new')."', `comida end`='".mysql_result($result,0,'comida end new')."' WHERE id='$horario'";
        mysql_query($q_change);
            echo "$key:<br>$q_change<br>";
			if(mysql_errno()){
			    echo "$key MySQL error ".mysql_errno().": "
			         .mysql_error()."\n<br>When executing <br>\n$q_change\n<br><br>";
                     $err_count++;
			}else{echo "OK<br><br>";}
    }
}
echo "Done!<br>$err_count Found!";



include("../common/menup.php");
?>