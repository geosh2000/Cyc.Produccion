<?php
header("Access-Control-Allow-Origin: *");

include_once("../modules/modules.php");

$connectdb=Connection::mysqliDB('CC');

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

$data['post']=$_POST;
$data['get']=$_GET;
$data['request']=$request;

$id=$request->id;

$query="SELECT * FROM Asesores WHERE id=$id";
if($result=$connectdb->query($query)){

  if($result->num_rows==0){
    $data['queryStatus']=0;
    $data['errorMsg']=utf8_encode("Sin registros");
  }else{
    $data['queryStatus']=1;

    $fields=$result->fetch_fields();
    while($fila=$result->fetch_array()){
      for($i=0; $i<$result->field_count; $i++){
        $data['table'][$fields[$i]->name]=utf8_encode($fila[$i]);
      }
    }
  }

}else{
  $data['queryStatus']=0;
  $data['errorMsg']=utf8_encode("Error! -> ".$connectdb->error." ON $query");
}

$connectdb->close();


echo json_encode($data);
