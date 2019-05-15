<?php
error_reporting(E_ALL ^ E_NOTICE);

//Start settings
class initSettings{
	public static function start($showMenu, $credential=NULL){
		Session::start();
		header("Content-Type: text/html;charset=utf-8");
		Scripts::loadScripts("generic",IS_MOBILE);
		if($showMenu){

			if(IS_MOBILE || $_GET['forceMobile']==1){
				date_default_timezone_set('America/Bogota');
				echo "<script> asesor='".$_SESSION['asesor_id']."'; today='".date('Y-m-d')."'; fechahorarios='";
				if(intval(date('H'))<4){echo date('Y-m-d');}else{echo date('Y-m-d',strtotime('+1 day'));}
				echo "'; serverpath='".$_SERVER['HTTP_REFERER']."'; </script>";
				echo "<script type='text/javascript' src='/js/logout.js'></script>";
				Menu::showMenu();
			}else{
				date_default_timezone_set('America/Bogota');
				echo "<script> asesor='".$_SESSION['asesor_id']."'; today='".date('Y-m-d')."'; fechahorarios='";
				if(intval(date('H'))<4){echo date('Y-m-d');}else{echo date('Y-m-d',strtotime('+1 day'));}
				echo "'; serverpath='".$_SERVER['HTTP_REFERER']."'; </script>";
				echo "<script type='text/javascript' src='/js/logout.js'></script>";
				MenuNew::showMenu();
			}
		}else{
			date_default_timezone_set('America/Bogota');
			echo "<script> asesor='".$_SESSION['asesor_id']."'; today='".date('Y-m-d')."'; fechahorarios='";
			if(intval(date('H'))<4){echo date('Y-m-d');}else{echo date('Y-m-d',strtotime('+1 day'));}
			echo "'; serverpath='".$_SERVER['HTTP_REFERER']."'; </script>";
			echo "<script type='text/javascript' src='/js/logout.js'></script>";
			MenuNew::showNOMenu();
		}
		if($credential!=NULL){
			Session::check($credential);
		}
	}



	public static function startScreen($showMenu){
		header("Content-Type: text/html;charset=utf-8");
		Scripts::loadScripts("generic");
		if($showMenu){
			Menu::showMenu();
		}
	}

	public static function printTitle($title){
		echo "<title>CyC -> $title</title>";
		//echo "<div style='width: 100%; color: white; background: #008cba; text-align: ; font-weight: bold; '>$title</div>";
	}
}

class timeAndRegion{
	public static function setRegion($zone){
		switch($zone){
			case 'Mex':
				date_default_timezone_set('America/Mexico_City');
				break;
			case 'Cun':
				date_default_timezone_set('America/Bogota');
				break;
		}
	}
}





//Constants
$GLOBALS['cun_time'] = new DateTimeZone('America/Bogota');
$cun_time = new DateTimeZone('America/Bogota');
$mx_time = new DateTimeZone('America/Mexico_City');
define('MODULES_PATH','/var/www/html/pthpv14.pricetravel.com.mx/site/modules/');

//Detect Mobile
$useragent=$_SERVER['HTTP_USER_AGENT'];
if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
	define(IS_MOBILE,true);
}else{
	define(IS_MOBILE,false);
}

//DB Connection
include_once(MODULES_PATH."connection.php");

//Menu Insertion
include_once(MODULES_PATH."menu.php");
include_once(MODULES_PATH."menuNew.php");

//Session & Permissions
include_once(MODULES_PATH."permissions.php");

//Scripts and Styles
include_once(MODULES_PATH."scripts.php");

//Scripts and Styles
include_once(MODULES_PATH."queries.php");

//Filters
include_once(MODULES_PATH."filters.php");

?>
