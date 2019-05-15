<?php
include_once("../modules/modules.php");

 $connectdb=Connection::mysqliDB('CC');

 initSettings::startScreen(false);

$query="SELECT * FROM Asesores";
if($result=$connectdb->query($query)){
	$i=0;
	while($fila=$result->fetch_assoc()){
		$id[$i]=$fila['id'];
		$NCorto[$i]=$fila['N Corto'];
		$Nombre[$i]=($fila['Nombre']);
		$i++;
	}


}

$connectdb->close()


?>

<table>
	<tr>
		<td>Nombre Corto</td>
		<td>ID</td>
		<td>Nombre</td>
	<tr>
	<?php foreach($id as $key => $id_ok){
		echo "\t<tr>\n\t\t<td>".$NCorto[$key]."</td>\n\t\t<td>$id_ok</td>\n\t\t<td>$Nombre[$key]</td>\n\t</tr>\n";
	}

	echo "\t<tr>\n\t\t<td>Reserbot</td>\n\t\t<td>-100</td>\n\t\t<td>Reserbot</td>\n\t</tr>\n";
	?>
</table>
