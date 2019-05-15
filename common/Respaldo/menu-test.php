<?php
session_start();
$log=$_SESSION['login'];
$noProfile="Your profile is not allowed to access this feature. Please check credentials with the administrator";
//include("../connectDB.php");
//include("../common/scripts.php");
$querysesion="SELECT session_id FROM userDB WHERE userid='".$_SESSION['id']."'";
$sesid=mysql_result(mysql_query($querysesion),0,'session_id');
//echo session_id()." $log // $sesid<br>$query";
if(session_id()!=$sesid){session_destroy(); }
$thpage=$_SERVER['PHP_SELF'];
?>



<?php

$query="SELECT * FROM menu ORDER BY parent, titulo";
$result=mysql_query($query);
$num=mysql_numrows($result);
$i=0;
while($i<$num){
    //MenuInfo
    $menu_ok[mysql_result($result,$i,'id')]['Titulo']=mysql_result($result,$i,'titulo');
    $menu_ok[mysql_result($result,$i,'id')]['Parent']=mysql_result($result,$i,'parent');
    $menu_ok[mysql_result($result,$i,'id')]['liga']=mysql_result($result,$i,'liga');
    $menu_ok[mysql_result($result,$i,'id')]['Permiso']=mysql_result($result,$i,'permiso');

    //Childs
    $childs[mysql_result($result,$i,'level')][mysql_result($result,$i,'parent')][]=mysql_result($result,$i,'id');

    //Niveles
    $nivel[mysql_result($result,$i,'id')]=mysql_result($result,$i,'level');
    $niveles[mysql_result($result,$i,'level')][]=mysql_result($result,$i,'id');
    $padre[mysql_result($result,$i,'id')]=mysql_result($result,$i,'parent');
    $permiso[mysql_result($result,$i,'id')]=mysql_result($result,$i,'permiso');
  


$i++;
}

//All Childs Licenses
foreach($nivel as $index => $info){
    foreach($niveles as $index2 => $info2){
        switch($index2){
          case 1:
            foreach($info2 as $index3 => $info3){
              if($padre[$info3]==$index){
                if(!in_array($permiso[$info3],$licenses[$index]) && $permiso[$info3]!=NULL){
                  $licenses[$index][]=$permiso[$info3];
                 }
              }
              
            }
            unset($index3,$info3);
            break;
          case 2:
            foreach($info2 as $index3 => $info3){
              foreach($id[$index] as $index4 => $info4){
                if($padre[$info4]==$index){
                  if(!in_array($permiso[$info4],$licenses[$index]) && $permiso[$info4]!=NULL){
                    $licenses[$index][]=$permiso[$info4];
                   }
                }
              }

            }
            unset($index3,$info3);
            break;
        }
    }
    unset($index2,$info2);
    if(!in_array($permiso[$index],$licenses[$index]) && $permiso[$index]!=NULL){
      $licenses[$index][]=$permiso[$index];
     }
}

?>

<nav>
    <ul>
        <li>
            <a>Menu</a>
                <ul>
                 <?php

                 foreach($childs[0] as $index => $info){
                     foreach($info as $index2 => $info2){

                        // IMPRESION DE NIVEL 0
                         $flag=false;

                         //Validación de permisos
                         if(count($licenses[$info2])>0){
                             foreach($licenses[$info2] as $lic_index => $lic_permiso){
                                 if($_SESSION[$lic_permiso]==1){
                                     $flag=true;
                                 }
                             }
                             unset($lic_index,$lic_permiso);
                         }else{
                             $flag=true;
                         }

                         //Validado => Output
                         if($flag){
                            echo "<li>\n\t";
                            echo "\t<a href='".$menu_ok[$info2]['liga']."'>".$menu_ok[$info2]['Titulo']."</a>\n\t";

                            // IMPRESION DE NIVEL 1 (Si existiera)
                            if(count($childs[1][$info2])>0){
                                echo "\t\t<ul>\n";
                                foreach($childs[1][$info2] as $ch1_index => $ch1_info){
                                        $flag=false;

                                        //Validación de permisos nivel 1
                                        if(count($licenses[$ch1_info])>0){
                                             foreach($licenses[$ch1_info] as $lic_index => $lic_permiso){
                                                 if($_SESSION[$lic_permiso]==1){
                                                     $flag=true;
                                                 }
                                             }
                                             unset($lic_index,$lic_permiso);
                                         }else{
                                             $flag=true;
                                         }

                                         //Validado => Output Nivel 1
                                         if($flag){
                                            echo "<li>\n\t";
                                            echo "\t\t<a href='".$menu_ok[$ch1_info]['liga']."'>".$menu_ok[$ch1_info]['Titulo']."</a>\n\t";

                                            // IMPRESION DE NIVEL 2 (Si existiera)
                                            if(count($childs[2][$ch1_info])>0){
                                                echo "\t\t<ul>\n";
                                                foreach($childs[2][$ch1_info] as $ch2_index => $ch2_info){
                                                        $flag=false;

                                                        //Validación de permisos nivel 2
                                                        if(count($licenses[$ch2_info])>0){
                                                             foreach($licenses[$ch2_info] as $lic_index => $lic_permiso){
                                                                 if($_SESSION[$lic_permiso]==1){
                                                                     $flag=true;
                                                                 }
                                                             }
                                                             unset($lic_index,$lic_permiso);
                                                         }else{
                                                             $flag=true;
                                                         }

                                                         //Validado => Output Nivel 2
                                                         if($flag){
                                                            echo "<li>\n\t";
                                                            echo "\t\t<a href='".$menu_ok[$ch2_info]['liga']."'>".$menu_ok[$ch2_info]['Titulo']."</a>\n\t";

                                                            echo "\t\t</li>";
                                                         }
                                                }
                                                unset($ch2_index,$ch2_info);
                                                echo "\t\t</ul>\n";
                                            }

                                            echo "\t\t</li>";
                                         }
                                }
                                unset($ch1_index,$ch1_info);
                                echo "\t\t</ul>\n";
                            }
                            echo "</li>\n\t";
                         }
                     }
                     unset($index2,$info2);
                 }
                 unset($index,$info);

                 ?>
                </ul>
        </li>
    </ul>
</nav>

<div style="margin-left:50px; width:95%">

<?php if(isset($_SESSION[$credential]) && $_SESSION[$credential]!='1'){ echo $noProfile; exit;}?>