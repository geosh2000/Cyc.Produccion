<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

include_once("../modules/modules.php");

$connectdb=Connection::mysqliDB('CC');

initSettings::start(false);
initSettings::printTitle('Login');

timeAndRegion::setRegion('Cun');

$refered=$_GET['address'];

// server should keep session data for AT LEAST 8 hour
ini_set('session.gc_maxlifetime', 28800);

// each client should remember their session id for EXACTLY 8 hour
session_set_cookie_params(28800);


/* POST INFO */
session_start();
$user=$_POST['user'];
$pass=$_POST['pass'];
$page=$_POST['uri'];
$redirect=$_POST['address'];
$modal=$_GET['modal'];

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

//echo substr($user,strpos($user,"@")+1,15);

if(!$wrongDomain){
  //echo "<br>No AD -> $user";

	//NO ActiveDirectory Users
	$query="SELECT username FROM userDB WHERE noAD=1";
	if($result=$connectdb->query($query)){
		while($fila=$result->fetch_assoc()){
			$noAD[]=$fila['username'];
		}
	}
	
	/*echo "<pre>";
	print_r($noAD);
	echo "</pre>";*/

	if(in_array($usr,$noAD)){
    //echo "<br>IN Array";
		//$usr=$user;
		include("login_SQL.php");
		exit;
	}
	
	//echo "<br>NOT IN Array";

	//END OF NO ActiveDirectory Users

	$userSuccess=false;

	if(isset($_POST['login'])){

		// conexión al servidor LDAP
		$ldapconn = ldap_connect("ccad02.pricetravel.com.mx")
		    or die("No se pudo establecer conexion con el LDAP server.");

		if ($ldapconn) {

		    // realizando la autenticación
		    $ldapbind = ldap_bind($ldapconn, $user, $pass);

		    // verificación del enlace
		    if ($ldapbind) {
		        $userSuccess=true;
		    } else {
		        $userSuccess=false;
		    }

		}

		ldap_close($ldapconn);
	}

	if($userSuccess){
		/* Search on DB */

		$usr=substr($user,0,strpos($user,"@"));

		$query="SELECT userid, profile, asesor_id, `N Corto`, `id Departamento`, Esquema, hashed_pswd, Egreso, active, getDepartamento(Asesores.id, CURDATE()) as DepOK FROM `userDB` LEFT JOIN Asesores ON userDB.asesor_id=Asesores.id WHERE `username`='$usr'";
		if($result=$connectdb->query($query)){
			while($fila=$result->fetch_assoc()){
				$data['userid']=$fila['userid'];
				$data['profile']=$fila['profile'];
				$data['asesor_id']=$fila['asesor_id'];
				$data['ncorto']=$fila['N Corto'];
				$data['idDepartamento']=$fila['DepOK'];
				$data['esquema']=$fila['Esquema'];
				$data['pswd']=$fila['hashed_pswd'];
				$data['egreso']=$fila['Egreso'];
				$data['active']=$fila['active'];
			}
		}
		unset($result);

		if($data['egreso']==NULL){
			if($data['active']==1){
				$flag=true;
			}else{
				$flag=false;
			}
		}else{
			if(date('Y-m-d', strtotime($data['egreso']))>=date('Y-m-d')){
				$flag=true;
			}else{
				$flag=false;
			}
		}

		$query="SELECT * FROM `profilesDB` WHERE id='".$data['profile']."'";
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
						$_SESSION[$credential]=$info;
					}

					$_SESSION['login']='1';
					$_SESSION['user']=$usr;
					$_SESSION['id']=$data['userid'];
	        $_SESSION['asesor_id']=$data['asesor_id'];
	        $_SESSION['name']=$data['ncorto'];
	        $_SESSION['dep']=$data['idDepartamento'];
	        $_SESSION['esquema']=$data['esquema'];
	        $_SESSION['profile']=$data['profile'];

					$hostsipaddress = str_replace("\n","",shell_exec("ifconfig eth0 | grep 'inet addr' | awk -F':' {'print $2'} | awk -F' ' {'print $1'}"));

					//Register Session
					$query="INSERT INTO `Detalles de Logueo` (user, tipo,`IP Internal`, `IP Remote Addr`, `IP Fowarded`,Page,Path) VALUES ('".$data['userid']."','login','".$_SERVER['SERVER_ADDR']."','".$_SERVER['REMOTE_ADDR']."','$hostsipaddress','$this_page','".$_SERVER['REQUEST_URI']."')";
					$connectdb->query($query);
					$query="UPDATE userDB SET session_id='".session_id()."' WHERE userid='".$data['userid']."'";
					$connectdb->query($query);


		      //Change Default PSWD
					$_SESSION['defaultpswd']=0;
					if($redirect!=""){
						header("Location: $redirect");
					}else{
						if($this_page==NULL){$this_page='/index.php';} //Default
						header("Location: /index.php");
					}

					exit;
				}else{
					$errorMessage="Usuaro inactivo en sistema. Favor de contactar a WFM"; session_start(); session_destroy();
				}
		}

	}else{
		if(isset($_POST['login'])){
			$errorMessage="Nombre de Usuario o Contraseña incorrecta"; session_start(); session_destroy();
		}
	}
}else{
	$errorMessage="Dominio incorrecto";
}


$connectdb->close();

?>
<script>
$(function(){
	$('.mainButts').remove();
})
</script>
<br>
<div style="height: 50px; font-size:25; color: white; background: red; text-align: center; line-height:2">Por favor usa el mismo usuario y contraseña que el resto de sistemas de PriceTravel</div><br>
<table style='width:50%; margin:auto' class='t2' ><form name='log' method='post' action='<?php echo $this_page; ?>'>
	<tr class='title'>
		<th colspan=2>Inicia Sesi&oacuten</th>

	</tr>
	<?php if($errorMessage!=NULL){ echo "<tr><td colspan=2 style='color:red'>$errorMessage</td></tr>"; } ?>
	<tr class='pair'>
		<td>Username:</td>
		<td><input type='text' size='20' name='user' required></td>
	</tr>
	<tr class='pair'>
		<td>Password:</td>
		<td><input type='password' size='20' name='pass' required></td>
	</tr>

	<tr class='total'>
		<th colspan=2><input type='submit' name='login' value='Login'></th>
	</tr>
<input type='hidden' name='address' value='<?php echo $refered; ?>'>
</form></table><br>
