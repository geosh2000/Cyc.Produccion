<?php

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
$usr=$_POST['user'];
$pass=$_POST['pass'];
$page=$_POST['uri'];
$redirect=$_POST['address'];
$modal=$_GET['modal'];

// conexión al servidor LDAP
$ldapconn = ldap_connect("ccad02.pricetravel.com.mx")
    or die("No se pudo establecer conexion con el LDAP server.");

if ($ldapconn) {

    // realizando la autenticación
    $ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);

    // verificación del enlace
    if ($ldapbind) {
        echo "LDAP bind successful...";
    } else {
        ldap_close();
        echo "LDAP bind failed...";
        exit;
    }

}

ldap_close();
exit;

/* Search on DB */
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
	if(isset($_POST['login'])){

		//Check if user is Active

		if($flag){

			//Check Login
			if(password_verify($pass, $data['pswd'])) {


				//Declare Session Permissions
				foreach($permissions as $credential => $info){
					$_SESSION[$credential]=$info;
				}

				//set RememberMe Cookie
				if($_POST['remember']==1){
					setcookie ('siteAuth_cyc', 'usr='.$usr.'&hash='.$pass, time() + (3600 * 24 * 30));
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
					if($pass=='pricetravel2016'){
						$_SESSION['defaultpswd']=1;
						header('Location: /common/password.php');
					}else{
						$_SESSION['defaultpswd']=0;
						if($redirect!=""){
							header("Location: $redirect");
						}else{
							if($this_page==NULL){$this_page='/index.php';} //Default

							header("Location: /index.php");
						}
					}


					exit;

			}else{
				$errorMessage= "Incorrect user/password";

				$_SESSION['login']='0';
	            header("Location: /index.php");
				exit;

			}
		}else{
			$errorMessage="Invalid user/password... Please try again"; session_start(); session_destroy();
		}
	}
}



$connectdb->close();

if(isset($_SESSION['login']) && $_SESSION['login']==0){ $errorMessage="Invalid user/password... Please try again"; session_start(); session_destroy();}

?>
<script>
$(function(){
	$('.mainButts').remove();
})
</script>
<br>
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
	<tr class='pair'>
		<td>Remember Login:</td>
		<td><input type='checkbox' size='20' name='remember' value=1></td>
	</tr>
	<tr class='total'>
		<th colspan=2><input type='submit' name='login' value='Login'></th>
	</tr>
<input type='hidden' name='address' value='<?php echo $refered; ?>'>
</form></table><br>
