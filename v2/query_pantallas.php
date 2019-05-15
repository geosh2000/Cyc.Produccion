<?php

include_once("../modules/modules.php");

timeAndRegion::setRegion('Cun');

if(isset($_POST['pantalla'])){
	$pantalla=$_POST['pantalla'];
}else{
	$pantalla='Ventas1';
}

$query="SELECT id, path FROM pantallas_display WHERE $pantalla=1 AND activo=1 AND '".date('Y-m-d H:i:s')."' BETWEEN inicio AND fin ORDER BY id" ;
if($result=Queries::query($query)){
	$x=0;
	while($fila=$result->fetch_assoc()){
		$data[$x]['id']=$fila['id'];
		$data[$x]['src']=utf8_encode($fila['path']);
		$x++;
	}
}

$data['result']=$x;
$data['query']=utf8_decode($query);

print json_encode($data, JSON_PRETTY_PRINT);

?>
