<?php

include_once('../modules/modules.php');

$connectdb=Connection::mysqliDB('CC');

$id=$_POST['id'];
$field=$_POST['field'];
$newVal="'".utf8_decode($_POST['newVal'])."'";
if($_POST['newVal']==""){$newVal="NULL";}


switch($field){
  case 'delete':
    $query="SELECT path FROM pantallas_display WHERE id='$id'";
    $data['query']=utf8_encode($query);
    if($result=$connectdb->query($query)){
      $fila=$result->fetch_assoc();
      $path=$fila['path'];
      $query="DELETE FROM pantallas_display WHERE id='$id'";
      $data['query']=utf8_encode($query);
    }else{
    	$data['status']=0;
    	$data['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");

      $connectdb->close();

      echo json_encode($data, JSON_PRETTY_PRINT);

      exit();
    }
    break;
	default:
		$query="UPDATE pantallas_display SET `$field`=$newVal WHERE id='$id'";
		break;
}


if($result=$connectdb->query($query)){
  $data['query']=utf8_encode($query);
	$data['status']=1;
  unlink($path);

}else{
	$data['status']=0;
	$data['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
}

$connectdb->close();

echo json_encode($data, JSON_PRETTY_PRINT);





?>
