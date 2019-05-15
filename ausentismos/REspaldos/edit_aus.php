<?php
session_start();
include("../connectDB.php");
$id=$_GET['id'];
$moper=$_GET['moper'];
$delete=$_GET['delete'];

if($delete=='ok'){
$query="SELECT * FROM Ausentismos WHERE ausent_id='$id'";
$result=mysql_query($query);
$fields=mysql_num_fields($result);
if(mysql_result($result,0,'tipo_ausentismo')==5){
     $qdel="DELETE FROM `Dias Pendientes Redimidos` WHERE caso='".mysql_result($result,0,'caso')."' AND id='".mysql_result($result,0,'asesor')."' AND id_ausentismo='$id'";
    mysql_query($qdel);
}
$i=0;
$tmp_field="";
$tmp_value="";
while($i<$fields){
    if($i<$fields-1){$comma=",";}else{$comma="";}
    $tmp_field.="`".mysql_field_name($result,$i)."`$comma";
    $tmp_value.="'".mysql_result($result,0,$i)."'$comma";
$i++;
}
$query="INSERT INTO Ausentismos_Deleted ($tmp_field,User_delete) VALUES ($tmp_value,'".$_SESSION['id']."')";
mysql_query($query);
if(mysql_errno()){
				    echo "MySQL error ".mysql_errno().": "
				         .mysql_error()."\n<br>When executing <br>\n$query\n<br><br>";
                          exit;
				}

$query="DELETE FROM Ausentismos WHERE ausent_id='$id'";
mysql_query($query);
        if(mysql_errno()){
				    echo "MySQL error ".mysql_errno().": "
				         .mysql_error()."\n<br>When executing <br>\n$query\n<br><br>";
                          exit;
				}else{echo "Done"; exit;}
}

$query="UPDATE Ausentismos SET Moper=$moper WHERE ausent_id='$id'";
mysql_query($query);
				if(mysql_errno()){
				    echo "MySQL error ".mysql_errno().": "
				         .mysql_error()."\n<br>When executing <br>\n$query\n<br><br>";

				}else{echo $moper;}


?>