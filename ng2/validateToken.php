<?php

//Get NG2 Posted Info
$postdata = file_get_contents("php://input");

$requestPhp = json_decode($postdata);

if(!isset($_GET['token'])){
    $user=$requestPhp->usn;
    $token=$requestPhp->token;
}else{
    $user=$_GET['usn'];
    $token=$_GET['token'];
}

//function validateTk($flag, $data){
//    if($flag){
//        return $data;
//    }else{
//        $error['status']=0;
//        $error['msg']=utf8_encode("Token invalido, favor de iniciar sesion nuevamente");
//        return $error;
//    }
//}

function validateTk($func, $flag){
    global $token;
    if($flag){
        $func();
    }else{
        $error['status']=0;
        $error['msg']=utf8_encode("Token invalido, favor de iniciar sesion nuevamente");
        
        $decoded=JWT::decode($token,'cAlbertyCome');
        $error['token']=$decoded;
        $error['evaluateDate']=utf8_encode(date('Y-m-d H:i:s'));
        echo json_encode($error);
    }
}


//Validate Token
$tkFlagUsn=JWT::validateToken( $token,$user,'cAlbertyCome' );
$tkFlagDate=JWT::checkToken( $token,date('Y-m-d H:i:s'),'cAlbertyCome' );

if($tkFlagUsn && $tkFlagDate){
    $tkFlag=true;
}else{
    $tkFlag=false;
}



 ?>
