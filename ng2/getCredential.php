<?php
header("Access-Control-Allow-Origin: *");

include_once("../modules/modules.php");
include_once("../common/JWT.php");
include_once("validateToken.php");

timeAndRegion::setRegion('Cun');

validateTk(function(){

    $user=$_GET['usn'];

    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    $credential=$request->cred;

    $connectdb=Connection::mysqliDB('CC');

    $x=0;
    $query="SELECT $credential FROM userDB a LEFT JOIN profilesDB b ON a.profile=b.id WHERE username='$user'";
    if($result=$connectdb->query($query)){
        $fila=$result->fetch_array();
        $x+=$fila[0];
    }

    $connectdb->close();

    $data['credential']=$x;

    echo json_encode($data);
},$tkFlag);







 ?>
