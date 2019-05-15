<?php
include("connectDB.php");

$query="SELECT * FROM `Asesores`";
$result=mysql_query($query);

$ASnum=mysql_numrows($result);

$i=0;
while ($i < $ASnum) {

$ASid[$i]=mysql_result($result,$i,"id");
$ASNCorto[$i]=mysql_result($result,$i,"N Corto");
$ASdepto[$i]=mysql_result($result,$i,"id Departamento");
$ASactive[$i]=mysql_result($result,$i,"Activo");
$ASname[$i]=mysql_result($result,$i,"Nombre");
$ASnewid[$i]=mysql_result($result,$i,"newid");
$ASingreso[$i]=mysql_result($result,$i,"Ingreso");
$ASegreso[$i]=mysql_result($result,$i,"Egreso");
$ASusuario[$i]=mysql_result($result,$i,"Usuario");
$ASesquema[$i]=mysql_result($result,$i,"Esquema");

$i++;
}

$ASNCorto_Sorted=$ASNCorto;
sort($ASNCorto_Sorted);
$i=0;
while ($i < $ASnum) {
	$x=0;
	while($x<$ASnum){
		if($ASNCorto_Sorted[$i]==$ASNCorto[$x]){
			$ASid_Sorted[$i]=$ASid[$x];
			$ASdepto_Sorted[$i]=$ASdepto[$x];
			$ASactive_Sorted[$i]=$ASactive[$x];
			$ASname_Sorted[$i]=$ASname[$x];
			$ASnewid_Sorted[$i]=$ASnewid[$x];
			$ASingreso_Sorted[$i]=$ASingreso[$x];
			$ASegreso_Sorted[$i]=$ASegreso[$x];
			$ASusuario_Sorted[$i]=$ASusuario[$x];
			$ASesquema_Sorted[$i]=$ASesquema[$x];
		}
	$x++;
	}

$i++;
}

?>