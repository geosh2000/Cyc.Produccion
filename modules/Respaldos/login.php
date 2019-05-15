<?php
include_once("../modules/modules.php");

//GET Params
if($autologin!=1){
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
}else{
	$usr=$user_auto;
	$pass=$pass_auto;
}

/* Search on DB */
$query="SELECT userid, profile, asesor_id, `N Corto`, `id Departamento`, Esquema, hashed_pswd FROM `userDB` LEFT JOIN Asesores ON userDB.username=Asesores.Usuario WHERE `username`='$usr'";
if($result=$connectdb->query($query)){
	while($fila=$result->fetch_assoc()){
		$data['userid']=$fila['userid'];
		$data['profile']=$fila['profile'];
		$data['asesor_id']=$fila['asesor_id'];
		$data['ncorto']=$fila['N Corto'];
		$data['idDepartamento']=$fila['id Departamento'];
		$data['esquema']=$fila['Esquema'];
		$data['pswd']=$fila['hashed_pswd'];
	}
}
unset($result);

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




	if(isset($_POST['login']) || isset($pass_auto)){
		
		//Check Login
		if(password_verify($pass, $data['pswd'])) {
			
			
			//Declare Session Permissions
			foreach($permissions as $credential => $info){
				$_SESSION[$credential]=$info;
			}
			
			//set RememberMe Cookie
			if($_POST['remember']==1){
				setcookie ('siteAuth_cyc', 'user_auto='.$usr.'&pass_auto='.$pass, time() + (3600 * 24 * 30));
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
            
            //Redirect
            
            	if($profile==17){$this_page="../tablaCopa";} //Perfil COPA
	            
	            //Change Default PSWD
				if($pass=='pricetravel2016'){
					$_SESSION['defaultpswd']=1;
					header('Location: http://operaciones.pricetravel.com.mx/common/password.php');
				}else{
					$_SESSION['defaultpswd']=0;
					if($redirect!=""){
						header("Location: $redirect");
					}else{
						if($this_page==NULL){$this_page='/index.php';} //Default
	            
						header("Location: $this_page");
					}	
				}
				
				
				exit;
			
		}else{
			$errorMessage= "Incorrect user/password";
			
			$_SESSION['login']='0';
            header("Location: '/index.php'");
			exit;

		}
	}



$connectdb->close();

if(isset($_SESSION['login']) && $_SESSION['login']==0){ $errorMessage="Invalid user/password... Please try again"; session_start(); session_destroy();}

?>
