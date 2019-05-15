<?php
//session_start();
$log=$_SESSION['login'];
$noProfile="Your profile is not allowed to access this feature. Please check credentials with the administrator";
include("../connectDB.php");
//include("../common/scripts.php");
$menu_querysesion="SELECT session_id FROM userDB WHERE userid='".$_SESSION['id']."'";
$sesid=mysql_result(mysql_query($menu_querysesion),0,'session_id');
//echo session_id()." $log // $sesid<br>$menu_query";
if(session_id()!=$sesid){session_destroy(); }
$thpage=$_SERVER['PHP_SELF'];
?>



<?php

$menu_query="SELECT * FROM menu WHERE activo=1 ORDER BY parent, titulo";
$menu_result=mysql_query($menu_query);
$menu_num=mysql_numrows($menu_result);
$i=0;
while($i<$menu_num){
    //MenuInfo
    $menu_ok[mysql_result($menu_result,$i,'id')]['Titulo']=mysql_result($menu_result,$i,'titulo');
    $menu_ok[mysql_result($menu_result,$i,'id')]['Parent']=mysql_result($menu_result,$i,'parent');
    $menu_ok[mysql_result($menu_result,$i,'id')]['liga']=mysql_result($menu_result,$i,'liga');
    $menu_ok[mysql_result($menu_result,$i,'id')]['Permiso']=mysql_result($menu_result,$i,'permiso');

    //Childs
    $childs[mysql_result($menu_result,$i,'level')][mysql_result($menu_result,$i,'parent')][]=mysql_result($menu_result,$i,'id');

    //Niveles
    $nivel[mysql_result($menu_result,$i,'id')]=mysql_result($menu_result,$i,'level');
    $niveles[mysql_result($menu_result,$i,'level')][]=mysql_result($menu_result,$i,'id');
    $padre[mysql_result($menu_result,$i,'id')]=mysql_result($menu_result,$i,'parent');
    $hijos[mysql_result($menu_result,$i,'parent')][]=mysql_result($menu_result,$i,'id');
    $permiso[mysql_result($menu_result,$i,'id')]=mysql_result($menu_result,$i,'permiso');



$i++;
}

//All Childs Licenses
foreach($nivel as $menu_index => $menu_info){
    foreach($niveles as $menu_index2 => $menu_info2){
        if($padre[$menu_info2]==$menu_index){
            if(!in_array($permiso[$menu_info2],$licenses[$menu_index]) && $permiso[$menu_info2]!=NULL){
                $licenses[$menu_index][]=$permiso[$menu_info2];
            }
        }
        if(count($hijos[$menu_index])>0){
            foreach($hijos[$menu_index] as $h_index => $h_info){
                if(!in_array($permiso[$h_info],$licenses[$menu_index]) && $permiso[$h_info]!=NULL){
                    $licenses[$menu_index][]=$permiso[$h_info];
                }

                if(count($hijos[$h_info])>0){
                    foreach($hijos[$h_info] as $h_index2 => $h_info2){
                        if(!in_array($permiso[$h_info2],$licenses[$menu_index]) && $permiso[$h_info2]!=NULL){
                            $licenses[$menu_index][]=$permiso[$h_info2];
                        }
                    }
                }
            }
        }
        /*switch($menu_index2){
          case 1:
            foreach($menu_info2 as $menu_index3 => $menu_info3){
              if($padre[$menu_info3]==$menu_index){
                if(!in_array($permiso[$menu_info3],$licenses[$menu_index]) && $permiso[$menu_info3]!=NULL){
                  $licenses[$menu_index][]=$permiso[$menu_info3];
                 }
              }

            }
            if($padre[$menu_info2]==$menu_index){
                if(!in_array($permiso[$menu_info2],$licenses[$menu_index]) && $permiso[$menu_info2]!=NULL){
                  $licenses[$menu_index][]=$permiso[$menu_info2];
                 }
              }
            unset($menu_index3,$menu_info3);
            break;
          case 2:
            foreach($menu_info2 as $menu_index3 => $menu_info3){
              foreach($id[$menu_index] as $menu_index4 => $menu_info4){
                if($padre[$menu_info4]==$menu_index){
                  if(!in_array($permiso[$menu_info4],$licenses[$menu_index]) && $permiso[$menu_info4]!=NULL){
                    $licenses[$menu_index][]=$permiso[$menu_info4];
                   }
                }
              }
              if($padre[$menu_info3]==$menu_index){
                if(!in_array($permiso[$menu_info3],$licenses[$menu_index]) && $permiso[$menu_info3]!=NULL){
                  $licenses[$menu_index][]=$permiso[$menu_info3];
                 }
              }

            }
            if($padre[$menu_info2]==$menu_index){
                if(!in_array($permiso[$menu_info2],$licenses[$menu_index]) && $permiso[$menu_info2]!=NULL){
                  $licenses[$menu_index][]=$permiso[$menu_info2];
                 }
              }
            unset($menu_index3,$menu_info3);
            break;
        }
        */
    }
    unset($menu_index2,$menu_info2);
    if(!in_array($permiso[$menu_index],$licenses[$menu_index]) && $permiso[$menu_index]!=NULL){
          $licenses[$menu_index][]=$permiso[$menu_index];
         }
}

?>

<nav>
    <ul>
        <li>
            <a>Menu</a>
                <ul>
                 <?php

                 foreach($childs[0] as $menu_index => $menu_info){
                     foreach($menu_info as $menu_index2 => $menu_info2){

                        // IMPRESION DE NIVEL 0
                         $flag=false;

                         //Validación de permisos
                         if(count($licenses[$menu_info2])>0){
                             foreach($licenses[$menu_info2] as $lic_index => $lic_permiso){
                                //$lpermisos.=" $lic_permiso,";
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
                            echo "\t<a href='".$menu_ok[$menu_info2]['liga']."'>".$menu_ok[$menu_info2]['Titulo']."</a>\n\t";

                            // IMPRESION DE NIVEL 1 (Si existiera)
                            if(count($childs[1][$menu_info2])>0){
                                echo "\t\t<ul>\n";
                                foreach($childs[1][$menu_info2] as $ch1_index => $ch1_info){
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
                     unset($menu_index2,$menu_info2);
                 }
                 unset($menu_index,$menu_info);

                 ?>
                </ul>
        </li>
    </ul>
</nav>

<div style="margin-left:50px; width:95%">
<pre>


<?php if(isset($_SESSION[$credential]) && $_SESSION[$credential]!='1'){ echo $noProfile; exit;}?>
