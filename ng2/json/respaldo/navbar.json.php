<?php
header("Access-Control-Allow-Origin: *");


include_once("../../modules/modules.php");

$connectdb=Connection::mysqliDB('CC');

$token=$_GET['token'];

$query="SELECT * FROM menu WHERE activo=1 ORDER BY parent, titulo";
if($result=$connectdb->query($query)){

  while($fila=$result->fetch_assoc()){
    $data[$fila['level']][$fila['parent']][]=Array(
      "title" => utf8_encode(str_replace("<br>", " ", $fila['titulo'])),
      "href" => utf8_encode($fila['liga']),
      "credential" => utf8_encode($fila['permiso']),
      "id" => $fila['id'],
      "v2link" => $fila['v2link'],
      "v2Active" => $fila['v2Active']
    );
  }

}else{
  echo $connectdb->error." ON $query<br>";
}

echo json_encode($data);



$connectdb->close();

 ?>
