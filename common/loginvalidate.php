<?
include("../connectDB.php");
session_start();
$usr=$_POST['user'];
$pass=$_POST['pass'];
$page=$_POST['uri'];

$query="SELECT * FROM `userDB` LEFT JOIN `profilesDB` on `userDB`.profile=`profilesDB`.id WHERE `userDB`.`username`='$usr' AND `password`='$pass'";
$result=mysql_query($query);
$num=mysql_numrows($result);
$log_id=mysql_result($result,0,'userid');
$schedules_upload=mysql_result($result,0,'schedules_upload');
$schedules_query=mysql_result($result,0,'schedules_query');
$schedules_change=mysql_result($result,0,'schedules_change');
$schedules_diaspendientes=mysql_result($result,0,'schedules_diaspendientes');
$schedules_selectSpecial=mysql_result($result,0,'schedules_selectSpecial');
$monitor_y_lw=mysql_result($result,0,'monitor_y_lw');
$monitor_gtr=mysql_result($result,0,'monitor_gtr');
$monitor_pya=mysql_result($result,0,'monitor_pya');
$tables_all=mysql_result($result,0,'tables_all');
$config=mysql_result($result,0,'config');




	if(isset($_POST['login'])){
		if($num>0){
			$_SESSION['login']='1';
			$_SESSION['user']=$usr;
			$_SESSION['id']=$log_id;
			$_SESSION['logschedules_uploadin']=$logschedules_uploadin;
			$_SESSION['schedules_query']=$schedules_query;
			$_SESSION['schedules_change']=$schedules_change;
			$_SESSION['schedules_diaspendientes']=$schedules_diaspendientes;
			$_SESSION['schedules_selectSpecial']=$schedules_selectSpecial;
			$_SESSION['monitor_y_lw']=$monitor_y_lw;
			$_SESSION['monitor_gtr']=$monitor_gtr;
			$_SESSION['monitor_pya']=$monitor_pya;
			$_SESSION['tables_all']=$tables_all;
			$_SESSION['config']=$config;
			header("Location: $page");
			
		}else{
			$errorMessage= "Incorrect user/password";
			
			$_SESSION['login']='0';
			header("Location: $page");
			
		}
	}
	
?>