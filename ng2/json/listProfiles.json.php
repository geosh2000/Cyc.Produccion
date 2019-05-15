<?php
header("Access-Control-Allow-Origin: *");

include_once("../../modules/modules.php");
include_once("../../common/JWT.php");
include_once("../validateToken.php");

timeAndRegion::setRegion('Cun');

validateTk(function(){

    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);

    $connectdb=Connection::mysqliDB('CC');

    $query="SELECT * FROM profilesDB ORDER BY profile_name";

    $x=0;
    if($result=$connectdb->query($query)){
        while($fila=$result->fetch_assoc()){
            $data[$x]=Array( "id" => $fila['id'], "name" => $fila['profile_name'] );
            $x++;
        }
    }


    $connectdb->close();

    echo json_encode($data, JSON_PRETTY_PRINT);

},$tkFlag);
