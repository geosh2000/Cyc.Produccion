<?php

session_start();

if(strpos($_SESSION['profile_name'],'pdv')!==false){
    echo "Si";
}else{
    echo "no";
}

echo "<pre>"; 
print_r($_SESSION);
echo "</pre>";