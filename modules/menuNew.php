<?php
include_once("connection.php");

//Show menu by calling static functions "menu::showMenu"

class MenuNew{

	public static function showMenu(){
		$menuConnect = Connection::mysqliDB('CC');

			$query="SELECT * FROM menu WHERE activo=1 ORDER BY parent, titulo";
			if($result=$menuConnect->query($query)){
				while($fila=$result->fetch_assoc()){

					//MenuInfo
				    $menu_ok[$fila['id']]['Titulo']=utf8_encode($fila['titulo']);
				    $menu_ok[$fila['id']]['Parent']=$fila['parent'];
				    $menu_ok[$fila['id']]['liga']=$fila['liga'];
				    $menu_ok[$fila['id']]['Permiso']=$fila['permiso'];

				    //Childs
				    $childs[$fila['level']][$fila['parent']][]=$fila['id'];

				    //Niveles
				    $nivel[$fila['id']]=$fila['level'];
				    $niveles[$fila['level']][]=$fila['id'];
				    $padre[$fila['id']]=$fila['parent'];
				    $hijos[$fila['parent']][]=$fila['id'];
				    $permiso[$fila['id']]=$fila['permiso'];
				}
			}

			$menuConnect->close();

			//All Childs Licenses
			foreach($nivel as $menu_index => $menu_info){
				$licenses[$menu_index][0]="flag";
			    foreach($niveles as $menu_index2 => $menu_info2){
			    	foreach($menu_info2 as $index3 => $menu_info3){
			    		if($padre[$menu_info3]==$menu_index){
				            if(!in_array($permiso[$menu_info3],$licenses[$menu_index]) && $permiso[$menu_info3]!=NULL){
				                $licenses[$menu_index][]=$permiso[$menu_info3];
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

			    	}
					unset($menu_info3,$index3);

			    }
			    unset($menu_index2,$menu_info2);
				if(count($licenses[$menu_index])>0){
				    if(!in_array($permiso[$menu_index],$licenses[$menu_index]) && $permiso[$menu_index]!=NULL){
				          $licenses[$menu_index][]=$permiso[$menu_index];
				    }
				}else{
					$licenses[$menu_index][]=$permiso[$menu_index];
				}
			}



			echo "<div class='fullloader' style='text-align: center'>
            <br><br><img src='/images/logoCyC.png'><br><img src='/images/loading2.gif'>
        </div><div class='mainHeader'>
				<div class='logo'><img src='/images/logoCyC.png'></div>
				<div class='mainSes'>
					<div class='mainWelcome'>".$_SESSION['name']."</div>
					<div class='mainButts'>
						<a href='/app?module=home'><img src='/images/home.png' title='Home' width='30px'></a>
					</div>
					<div class='mainButts'>
						<img src='/images/logout.png' width='25px' id='logout' title='Logout'>
					</div>
				</div><div  id='bodycontent' >
			<nav class='nav'>
			    <ul>";

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

			                 echo "
			                </ul>
			</nav>
			</div></div>
			<div style='clear: both'></div>
			<div id='dialog-message' title='Cerrar Sesión' style='text-align: center; font-size: 27px'>
			  <p  class='check_horario' style='text-align: center'>¡Verifica tu horario de mañana!</p>
			  <table id='nd_horario' class='t2' style='text-align:center; margin: auto'>
			  	<tr class='title hth'>

			  	</tr>
			  	<tr class='pair hth'>

			  	</tr>
			  </table>
			  <p class='check_horario'><input id='check_horario' type='checkbox' req='1'> He revisado mi horario para mi siguiente día laboral</p>
			  <p><button class='button button_red_w' id='ok_logout'>Logout</button></a></p>
			</div>

			<div id='dialog-message-out' title='Sesión Finalizada' style='text-align: center'>
			  <p style='text-align: center'>¡Hasta pronto!</p>
			</div>

			<div id='dialog-load' title='Sesión Finalizada' style='text-align: center'>
				<div id='progressbarload'></div>
			</div>
			<div style='clear: both'>

			";
	}

	public static function showNOMenu(){

			echo "<div class='fullloader' style='text-align: center'>
            <br><br><img src='/images/logoCyC.png'><br><img src='/images/loading2.gif'>
        </div>
			<div style='clear: both'></div>
			<div id='dialog-message' title='Cerrar Sesión' style='text-align: center; font-size: 27px'>
			  <p  class='check_horario' style='text-align: center'>¡Verifica tu horario de mañana!</p>
			  <table id='nd_horario' class='t2' style='text-align:center; margin: auto'>
			  	<tr class='title hth'>

			  	</tr>
			  	<tr class='pair hth'>

			  	</tr>
			  </table>
			  <p class='check_horario'><input id='check_horario' type='checkbox' req='1'> He revisado mi horario para mi siguiente día laboral</p>
			  <p><button class='button button_red_w' id='ok_logout'>Logout</button></a></p>
			</div>

			<div id='dialog-message-out' title='Sesión Finalizada' style='text-align: center'>
			  <p style='text-align: center'>¡Hasta pronto!</p>
			</div>

			<div id='dialog-load' title='Sesión Finalizada' style='text-align: center'>
				<div id='progressbarload'></div>
			</div>
			<div style='clear: both'>

			";
	}
}
