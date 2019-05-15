<?php
header("Access-Control-Allow-Origin: *");

include_once("../../modules/modules.php");
include_once("../../common/JWT.php");
include_once("../validateToken.php");

timeAndRegion::setRegion('Cun');

validateTk(function(){

    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);

    $user=$request->user;
    $user=str_replace(" ",".",strtolower($user));
    $asesor=$request->asesor;

    $connectdb=Connection::mysqliDB('CC');

    $query="SELECT * FROM userDB WHERE username='$user' AND asesor_id!=$asesor";

    if($result=$connectdb->query($query)){
        $data['res']=$result->num_rows;
    }


    $connectdb->close();

    echo json_encode($data, JSON_PRETTY_PRINT);

},$tkFlag);
