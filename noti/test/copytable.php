<?php
include("../connectDB.php");

$start=$_GET['start'];
$end=$start+1;
$next=$end;

if($start>12){ echo "Finalizacion de Copia"; exit;}

$query="SELECT ac_id, AsteriskID, Count(*) as Duplicados FROM t_Answered_Calls_copy WHERE MONTH(Fecha)=$start GROUP BY AsteriskID HAVING Duplicados>1";

$result=mysql_query($query);
$num=mysql_numrows($result);
echo $query."<br>$num regs<br><br>";
$field_num=mysql_num_fields($result);
$i=0;
while($i<$field_num){
    $field[$i]=mysql_field_name($result,$i);
    $fieldnames.="`".$field[$i]."`,";
$i++;
}
$fieldnames=substr($fieldnames,0,-1);
ob_start();
$i=0;
while($i<$num){
    foreach($field as $key => $campo){
        $data[$i][$campo]=mysql_result($result,$i,$campo);
        $values[$i].="'".$data[$i][$campo]."',";
    }
    $values[$i]=substr($values[$i],0,-1);

    $query="DELETE FROM t_Answered_Calls_copy WHERE AsteriskID='".$data[$i]['AsteriskID']."' AND ac_id!='".$data[$i]['ac_id']."'";
    echo $data[$i]['ac_id'].":<br>";
    mysql_query($query);
            if(mysql_errno()){
			    echo "$key MySQL error ".mysql_errno().": "
			         .mysql_error()."\n<br>When executing <br>\n$query\n<br><br>";
                     $err_count++;
			}else{echo "$query<br>OK<br><br>";}

$i++;
ob_flush();
}
unset($key,$campo);

echo "Done!...<br>$err_count errors.";
echo "<script type=\"text/javascript\">location.replace(\"copytable.php?start=$next\");</script>";


ob_end_flush();


?>