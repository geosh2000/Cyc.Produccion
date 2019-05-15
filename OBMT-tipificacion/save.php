<?php
include_once("../modules/modules.php");

$connectdb=Connection::mysqliDB('CC');

foreach($_POST as $key => $info){
	if(substr($key,0,2)=='f_'){
		$newKey=substr($key,2);
		$levels.=$newKey.",";
		if(intval($info)===$info){
			$values.=intval($info).",";
		}else{
			$values.="'".utf8_encode($info)."',";
		}

	}else{
		$newKey=$key;
	}

	$data[$newKey]=strtoupper(utf8_decode($info));
}

//print_r($data);
//exit;

$query="INSERT INTO OBMT_tipificacion ($levels asesor) VALUES ($values ".$data['asesor'].")";
if($result=$connectdb->query($query)){
	echo "DONE";
	//echo "<br>$query";
}else{
	echo "ERROR: ".$connectdb->error." ON $query";
}


$connectdb->close();

?>
