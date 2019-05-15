<?php

include("../connectDB.php");
header("Content-Type: text/html;charset=utf-8");

foreach($_POST as $key => $info){
	if(substr($key,0,2)=='f_'){
		$newKey=substr($key,2);
		$levels.=$newKey.",";
		if(intval($info)==$info){
			$values.=intval($info).",";
		}else{
			$values.="'$info',";
		}
		
	}else{
		$newKey=$key;
	}
	
	$data[$newKey]=$info;
}

//print_r($data);
//exit;

$query="INSERT INTO ME_tipificacion ($levels asesor) VALUES ($values ".$data['asesor'].")";
if($result=$connectdb->query($query)){
	echo "DONE";
	//echo "<br>$query";
}else{
	echo "ERROR: ".$connectdb->error." ON $query";
}



?>