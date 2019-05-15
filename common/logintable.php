<?
include("../connectDB.php");
session_start();


$usr=$_POST['user'];
$pass=$_POST['pass'];

$query="SELECT * FROM `userDB` WHERE `username`='$usr' AND `password`='$pass'";
$result=mysql_query($query);
$num=mysql_numrows($result);

if($_SESSION['login']!='1'){
	if(isset($_POST['login'])){
		if($num>0){
			session_start();
			$_SESSION['login']='1';
			
			
		}else{
			$errorMessage= "Incorrect user/password";
			session_start();
			$_SESSION['login']='';
			
		}
	}



?>

<table style='width:50%; margin:auto' class='t2' ><form name='log' method='post' action='<? $_SERVER['PHP_SELF']; ?>'>
	<tr class='title'>
		<th colspan=2>Inicia Sesi&oacuten</th>
		
	</tr>
	<? if($errorMessage!=NULL){ echo "<tr><td colspan=2 style='color:red'>$errorMessage</td></tr>"; } ?>
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

</form></table>
<?}?>