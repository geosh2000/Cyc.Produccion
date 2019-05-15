<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");

include_once("../common/JWT.php");
include_once("../modules/modules.php");

timeAndRegion::setRegion('Cun');

if(isset($_GET['usn'])){
    $user=$_GET['usn'];
    $pass=$_GET['usp'];
}else{
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);

    $user=$request->usn;
    $pass=$request->usp;
    $rem=$request->remember;
}

$wrongDomain=false;

if(strpos($user,"@")==""){
	$usr=$user;
	$user=$user."@pricetravel.com.mx";
}else{
	if(substr($user,strpos($user,"@"),15)=="@pricetravel.co"){
		$usr=substr($user,0,strpos($user,"@"));
	}else{
		$wrongDomain=true;
	}
}

if(!$wrongDomain){

    $connectdb=Connection::mysqliDB('CC');
    //NO ActiveDirectory Users
	$query="SELECT username FROM userDB WHERE noAD=1";
	if($result=$connectdb->query($query)){
		while($fila=$result->fetch_assoc()){
			$noAD[]=$fila['username'];
		}
	}

	if(in_array($usr,$noAD)){
        $data['status']=0;
        $data['msg']=utf8_encode("El usuario no pertenece al AD... debe iniciar sesión desde el CyC v1.0");
        $connectdb->close();
        echo json_encode($data);
		exit;
	}

	//END OF NO ActiveDirectory Users

	$userSuccess=false;

	// conexión al servidor LDAP
    $ldapconn = ldap_connect("ccad02.pricetravel.com.mx")
        or die("No se pudo establecer conexion con el LDAP server.");

    if ($ldapconn) {

        // realizando la autenticación
        if($ldapbind = ldap_bind($ldapconn, $user, $pass)){
            $userSuccess=true;
//            $data['ldpn']=utf8_encode("Conectado... auth ok");
        }else{
            $userSuccess=false;
//            $data['ldpn']=utf8_encode("Conectado... auth failed");
        }

    }else{
        $data['ldpn']=utf8_encode("Sin Conecion LDAP");
    }

//    echo $data['ldpn']."<br>";

//    $data['usr']=$user;
//    $data['pas']=$pass;

    ldap_close($ldapconn);

	if($userSuccess){
		/* Search on DB */

		$usr=substr($user,0,strpos($user,"@"));

        $data['status']=1;

		$query="SELECT userid, profile, asesor_id, `N Corto`, `id Departamento`, Esquema, hashed_pswd, Egreso, active, getDepartamento(Asesores.id, CURDATE()) as DepOK FROM `userDB` LEFT JOIN Asesores ON userDB.asesor_id=Asesores.id WHERE `username`='$usr'";
		if($result=$connectdb->query($query)){
			while($fila=$result->fetch_assoc()){
				$userInfo['userid']=$fila['userid'];
				$userInfo['profile']=$fila['profile'];
				$userInfo['asesor_id']=$fila['asesor_id'];
				$userInfo['ncorto']=$fila['N Corto'];
				$userInfo['idDepartamento']=$fila['DepOK'];
				$userInfo['esquema']=$fila['Esquema'];
				$userInfo['pswd']=$fila['hashed_pswd'];
				$userInfo['egreso']=$fila['Egreso'];
				$userInfo['active']=$fila['active'];
			}
		}
		unset($result);

		if($userInfo['egreso']==NULL){
			if($userInfo['active']==1){
				$flag=true;
			}else{
				$flag=false;
			}
		}else{
			if(date('Y-m-d', strtotime($userInfo['egreso']))>=date('Y-m-d')){
				$flag=true;
			}else{
				$flag=false;
			}
		}

		$query="SELECT * FROM `profilesDB` WHERE id='".$userInfo['profile']."'";
		if($result=$connectdb->query($query)){
			$fieldsnum=$result->field_count;
			$fields=$result->fetch_fields();
			while($fila=$result->fetch_row()){
				for($i=0;$i<$fieldsnum;$i++){
					$permissions[$fields[$i]->name]=$fila[$i];
				}
			}
		}
		unset($result);

		if(1==1){
			//Check if user is Active

            if($flag){
                //Declare Session Permissions
                foreach($permissions as $credential => $info){
                    $data['credentials'][$credential]=$info;
                }

                $data['login']='1';
                $data['user']=$usr;
                $data['id']=$userInfo['userid'];
                $data['asesor_id']=$userInfo['asesor_id'];
                $data['name']=$userInfo['ncorto'];
                $data['dep']=$userInfo['idDepartamento'];
                $data['esquema']=$userInfo['esquema'];
                $data['profile']=$userInfo['profile'];

                //Register Session
                $query="INSERT INTO `Detalles de Logueo` (user, tipo,`IP Internal`, `IP Remote Addr`, `IP Fowarded`,Page,Path) VALUES ('".$data['userid']."','login','".$_SERVER['SERVER_ADDR']."','".$_SERVER['REMOTE_ADDR']."','$hostsipaddress','$this_page','".$_SERVER['REQUEST_URI']."')";
                $connectdb->query($query);
                $query="UPDATE userDB SET session_id='".session_id()."' WHERE userid='".$userInfo['userid']."'";
                $connectdb->query($query);

                $td['status'] = 1;

                if($rem){
                    $td['tokenExpire']=date('Y-m-d H:i:s',strtotime('+ 15 days'));
                }else{
                    $td['tokenExpire']=date('Y-m-d H:i:s',strtotime('+ 4 hours'));
                }

                $query="SELECT
                            a.id AS vacante_id,
                            d.clave AS hc_udn_clave,
                            d.id AS hc_udn_id,
                            c.id AS hc_area_id,
                            c.clave AS hc_area_clave,
                            b.clave AS hc_dep_clave,
                            b.id AS hc_dep_id,
                            e.id AS hc_puesto_id,
                            e.clave AS hc_puesto_clave
                        FROM
                            asesores_plazas a
                                LEFT JOIN
                            hc_codigos_Departamento b ON a.hc_dep = b.id
                                LEFT JOIN
                            hc_codigos_Areas c ON b.area = c.id
                                LEFT JOIN
                            hc_codigos_UnidadDeNegocio d ON c.unidadDeNegocio = d.id
                                LEFT JOIN
                            hc_codigos_Puesto e ON a.hc_puesto = e.id
                        WHERE
                            a.id = GETVACANTEASESOR(".$data['asesor_id'].", CURDATE())";
                if($result=$connectdb->query($query)){
                    $fila=$result->fetch_assoc();
                    $td['hcInfo']['vacante']=$fila['vacante_id'];
                    $td['hcInfo']['hc_dep']=$fila['hc_dep_id'];
                    $td['hcInfo']['hc_area']=$fila['hc_area_id'];
                    $td['hcInfo']['hc_udn']=$fila['hc_udn_id'];
                    $td['hcInfo']['hc_puesto']=$fila['hc_puesto_id'];
                    $td['hcInfo']['hc_dep_clave']=$fila['hc_dep_clave'];
                    $td['hcInfo']['hc_area_clave']=$fila['hc_area_clave'];
                    $td['hcInfo']['hc_udn_clave']=$fila['hc_udn_clave'];
                    $td['hcInfo']['hc_puesto_clave']=$fila['hc_puesto_clave'];
                    $td['hcInfo']['query']=utf8_encode($query);
                }else{
                    $td['hcError']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
                }

                $connectdb->close();

                $genToken['usn']=$usr;
                $genToken['expire']=$td['tokenExpire'];

                $td['credentials']=$permissions;
                $td['hcInfo']['id']=$data['asesor_id'];

                $td['token'] = JWT::encode($genToken, 'cAlbertyCome');
                $td['remember']=$rem;

                echo json_encode($td);
                exit;

            }else{
                $connectdb->close();
                $data['status']=0;
                $data['msg']=utf8_encode("Usuaro inactivo en sistema. Favor de contactar a WFM");
                $data['info']=$userInfo;
                echo json_encode($data);
                exit;
            }


		}

	}else{
        $connectdb->close();
		$data['status']=0;
        $data['msg']=utf8_encode("Nombre de Usuario o Contraseña incorrecta");
	}
}else{
	$data['status']=0;
    $data['msg']=utf8_encode("Dominio incorrecto");
}

echo json_encode($data);
exit;


 ?>
