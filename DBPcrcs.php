<?php
include("connectDB.php");

$query="SELECT * FROM `PCRCs` ORDER BY `departamento`";
$result=mysql_query($query);
$pcrcs_num=mysql_numrows($result);

$i=0;

while($i<$pcrcs_num){
	$pcrcs_id[$i]=mysql_result($result,$i,'id');
	$pcrcs_departamento[$i]=mysql_result($result,$i,'departamento');
$i++;
}

$pcrcs_departamento_Sorted=$pcrcs_departamento;
sort($pcrcs_departamento_Sorted);
$i=0;
while($i<$pcrcs_num){
	$x=0;
	while($x<$pcrcs_num){
		if($pcrcs_departamento_Sorted[$i]==$pcrcs_departamento[$x]){$pcrcs_id_Sorted[$i]=$pcrcs_id[$x];}
	$x++;
	}
$i++;
}



?>