<?
session_start();
$this_page=$_SERVER['PHP_SELF'];
if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
include("../connectDB.php");

$class="tred";

if(isset($_POST['new2'])){
	if($_POST['new']!=$_POST['new2']){
		$result="Password validation must be equal";
		$class="tred";
	}else{
		$query="SELECT * FROM `userDB` WHERE `userid`='".$_SESSION['id']."'";
		$result=mysql_query($query);
		$pass=mysql_result($result,0,'password');
		$new=$_POST['new'];
		if(strlen($new)<8){
			$result="Password must be at least 8 characters";
			$class="tred";
		}else{
			if($_POST['old']==$pass){
				if($_POST['old']==$_POST['new'] || $_POST['new']=='pricetravel2016'){
					$result="You can't select the same old password, and it can't be the default password assigned";	
					$class="tred";
				}else{
					$query="UPDATE `userDB` SET `password`='$new' WHERE `userid`='".$_SESSION['id']."'";
					mysql_query($query);
					$result="Password Updated";
					$_SESSION['login']='1';
					$_SESSION['defaultpswd']=0;
					$class="tgreen";
					$flagredir=1;
				}
			}else{
				$result="Invalid old Password";
				$class="tred";
			}
		}
		
	}
}

include("../common/scripts.php");
$menu_sesion="class=active";
include("../common/menu.php");

?>
<? if($result!=NULL){
echo "<table width='100%' class='$class'>
	<tr class='title'>
		<th>$result</th>
	</tr>
</table>";
}

if($flagredir==1){
	echo"<script>
			$(function(){
				setTimeout(function(){
					window.location.assign('/home');
				},3000);
			});
		</script>";
}

?>
<script>
	$(function(){
		$('#change').click(function(){
			var flag=false;
			var regexp = /^\S*$/;
			var newpswd=$('#newpswd').val();
			flag=regexp.test(newpswd);
			if(flag){
				$('#form').submit();
				//alert(flag);
			}else{
				new noty({
                    text: 'Tu password no puede contener espacios',
                    type: 'error',
                    timeout: 5000,
                    animation: {
                        open: {height: 'toggle'}, // jQuery animate function property object
                        close: {height: 'toggle'}, // jQuery animate function property object
                        easing: 'swing', // easing
                        speed: 500 // opening & closing animation speed
                    }
                });
			}
		});
	});
</script>
<table width='100%' class='t2'><form name='pwdchg' method='post' id='form' action='<? $_SERVER['PHP_SELF']; ?>'><input name='id' type'text' value='<? $_SESSION['id']; ?>' hidden>
	<tr class='title'>
		<th colspan=2>Change Password for: <? echo $_SESSION['user']; ?></th>
	</tr>
	<tr class='pair'>
		<th>Old Password:</th>
		<th><input type='password' name='old' required></th>
	</tr>
	<tr class='odd'>
		<th>New Password:</th>
		<th><input type='password' id='newpswd' name='new' required></th>
	</tr>
	<tr class='pair'>
		<th>Verify New Password:</th>
		<th><input type='password' name='new2' required></th>
	</tr>
	</form>
	<tr class='total'>
		<th colspan=2><button class='button button_blue_w' id='change'>Cambiar</button></th>
	</tr>
</table>

*Si tu password es el que se asigno por default, debes cambiarlo para poder hacer uso del sistema
	