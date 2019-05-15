<?php
include_once("../modules/modules.php");

date_default_timezone_set('America/Bogota');

$type=$_POST['tipo'];

//Error Handler

function divError(){
 echo "";
}
set_error_handler("divError");

switch($type){
	case 'Needed':
		//Include Functions SLA
			include('forecast_erlang.php');
			for($y=0;$y<48;$y++){
				if($data[$_POST['start']]['forecast'][$y]==NULL){
					$td[$y]=0;
				}else{
					if($data[$_POST['start']]['necesarios'][$y]==NULL || $data[$_POST['start']]['necesarios'][$y]==""){
						$td[$y]=0;
					}else{
						$td[$y]=$data[$_POST['start']]['necesarios'][$y];
					}
				}
			}
		break;
}



print json_encode($td,JSON_PRETTY_PRINT);



?>
