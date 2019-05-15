<?php
include("../connectDB.php");
date_default_timezone_set('America/Bogota');

$posted = json_decode($_POST['data']);
$fecha=date('Y-m-d', strtotime($_POST['fecha']));
$skill=$_POST['skill'];

function hourConst($hora){
	$temp=$hora/2;
	if($hora % 2 != 0){
		$min=30;
	}else{
		$min=0;
	}

	return date('H:i:s', strtotime(intval($temp).":".$min.":00"));
}


foreach($_POST as $key => $info){
	if($key=='fecha' || $key=='skill'){
		continue;
	}
		$data[$key]['id']=$info['id'];
		$data[$key]['j_s']=hourConst($info['js']);
		$data[$key]['j_e']=hourConst($info['je']);
		$data[$key]['c_s']=hourConst($info['cs']);
		$data[$key]['c_e']=hourConst($info['ce']);
		$data[$key]['x1_s']=hourConst($info['x1s']);
		$data[$key]['x1_e']=hourConst($info['x1e']);
		$data[$key]['x2_s']=hourConst($info['x2s']);
		$data[$key]['x2_e']=hourConst($info['x2e']);
}
unset($info);

foreach($data as $id => $info){
	$query="INSERT INTO prog_draft VALUES (NULL,'$id','".$info['id']."',NULL,'$fecha','".$info['j_s']."','".$info['j_e']."','".$info['c_s']."','".$info['c_e']."','".$info['x1_s']."','".$info['x1_e']."','".$info['x2_s']."','".$info['x2_e']."','$skill')";
	if($result=$connectdb->query($query)){
		$td[$id]['status']='inserted';
		$td[$id]['msg']='ok';
	}else{
		$td[$id]['status']=$connectdb->error." ON $query";
		$query="UPDATE prog_draft SET asesor='".$info['id']."', `jornada start`='".$info['j_s']."', `jornada end`='".$info['j_e']."', "
				."`comida start`='".$info['c_s']."', `comida end`='".$info['c_e']."', `extra1 start`='".$info['x1_s']."', `extra1 end`='".$info['x1_e']."', `extra2 start`='".$info['x2_s']."', `extra2 end`='".$info['x2_e']."' "
				." WHERE Fecha='$fecha' AND skill='$skill' AND slot='$id'";
		if($result2=$connectdb->query($query)){
			$td[$id]['status'].='updated';
			$td[$id]['msg']='ok';
		}else{
			$td[$id]['status']=$connectdb->error." ON $query";
			$td[$id]['msg']='error';
		}
	}
}

$connectdb->close();

print json_encode($td, JSON_PRETTY_PRINT);

?>
