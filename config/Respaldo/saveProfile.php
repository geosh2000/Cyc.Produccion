<?php
include_once("../modules/modules.php");

$connectdb=Connection::mysqliDB('CC');

foreach($_POST as $var => $info){
  if($var!='id'){
    if($info=='true'){
      $val=1;
    }else{
      $val=0;
    }
  }else{
    $val=$info;
  }

  $variable[$var]=$val;
}

$query="UPDATE profilesDB SET ";

foreach($variable as $var => $info){
  if($var!='id'){

    if($var=='default'){
      $varOK='`default`';
    }else{
      $varOK=$var;
    }


    $query.=$varOK."=$info, ";
  }
}

$query=substr($query,0,-2);

$query.=" WHERE id=".$variable['id'];

if($result=$connectdb->query($query)){
  $data['status']=1;
}else{
  $data['status']=0;
  $data['msg']=utf8_encode("Error! -> ".$connectdb->error." ON $query");
}

$connectdb->close();

echo json_encode($data,JSON_PRETTY_PRINT);

 ?>
