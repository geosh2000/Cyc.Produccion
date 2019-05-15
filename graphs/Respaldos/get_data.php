<?php
include_once('../modules/modules.php');

$connectdb=Connection::mysqliDB('CC');


//Build Info

$query=$_POST['query'];

if(substr($query,0, 6)=="SELECT"){

	if ($result=$connectdb->query($query)) {
		$status=1;
		$msg=utf8_encode("Query correcto");
		
		$info_field_act=$result->fetch_fields();
		
		$x=0;
		while ($fila = $result->fetch_array(MYSQLI_BOTH)) {
			for($i=0;$i<$result->field_count;$i++){
				$data[$x][]=utf8_encode($fila[$i]);
			}
		$x++;
		}
		
	}else{
		$status=0;
		$msg=utf8_encode('Error -> '.$connectdb->error." ON $query");
	}		
	
	//Add titles to tableheaders
	for($i=0;$i<$result->field_count;$i++){
		$dataheaders[]=utf8_encode(str_replace("_", " ", $info_field_act[$i]->name));
	}
	
	
	//Create Headers
	foreach($dataheaders as $index => $info){
		$headers[]=array("text"=>$info);
	}
	
	//Create Rows
	foreach($data as $id =>$info){
		$row[]=$info;
	}
	
}else{
	$status=0;
	$msg=utf8_encode("Error -> Sólo puedes crear consultas 'SELECT'");
}

//Build JSON
$table['result']= array("rows" => $row,"headers"=>array($headers));
$table['status']= $status;
$table['msg']=$msg;

//Print JSON
print json_encode($table,JSON_UNESCAPED_UNICODE);

?>


