<?php
include("../modules/modules.php");

session_start();

$id=$_POST['id'];

$query="SELECT * FROM profilesDB WHERE id=$id ORDER BY profile_name";
if($result=Queries::query($query)){
  $fields=$result->fetch_field();
	$num=$result->field_count;
  while($fila=$result->fetch_array()){
		while($field=$result->fetch_field()){
			$data[$field->name]=$fila[$field->name];
		}
  }
}

//Print JSON
print json_encode($data,JSON_UNESCAPED_UNICODE);
//print json_encode($table,JSON_PRETTY_PRINT);

?>
