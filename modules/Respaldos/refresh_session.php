<?php

session_start();

if(isset($_SESSION['id'])){
  $td['status']=1;
}else{
  $td['status']=0;
}

echo json_encode($td,JSON_PRETTY_PRINT);

?>
