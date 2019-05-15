<?php

class Session{

	//Start Session... Redirec to to Login if not logged
	public static function start(){
		session_start();
		$this_page=$_SERVER['PHP_SELF'];

		if($this_page!='/common/login.php'){

			if($_SESSION['login']!='1'){
				if(isSet($_COOKIE[$cookie_name])){
					parse_str($_COOKIE[$cookie_name]);
					include_once('login.php');
				}else{
				header("Location: /common/login.php?address=$this_page");
				exit;
				}
			}
		}
	}


	//Check credentials or Display error msg
	public static function check($credential){
		if(!isset($_SESSION[$credential]) || $_SESSION[$credential]!='1'){
			echo "Your profile is not allowed to access this feature. Please check credentials with the administrator";
			exit;
		}
	}
}

?>
