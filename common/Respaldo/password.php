<?php
include_once("../modules/modules.php");

initSettings::start(true);
initSettings::printTitle('Cambio de Contraseña');

$connectdb=Connection::mysqliDB('CC');

$class="tred";

$old=password_hash($_POST['old'], PASSWORD_BCRYPT);
$new=password_hash($_POST['new'], PASSWORD_BCRYPT);
$new2=password_hash($_POST['new2'], PASSWORD_BCRYPT);

if(isset($_POST['new2'])){
	if($_POST['new']!=$_POST['new2']){
		$resultado="Password validation must be equal";
		$class="tred";
	}else{
		$query="SELECT * FROM `userDB` WHERE `userid`='".$_SESSION['id']."'";
		$result=$connectdb->query($query);
		$fila=$result->fetch_assoc();
		$pass=$fila['hashed_pswd'];
		if(strlen($_POST['new'])<8){
			$resultado="Password must be at least 8 characters";
			$class="tred";
		}else{
			//if($old==$pass){
			if (password_verify($_POST['old'], $pass)) {
				if($old==$new || $_POST['new']=='pricetravel2016'){
					$resultado="You can't select the same old password, and it can't be the default password assigned";
					$class="tred";
				}else{
					//$query="UPDATE `userDB` SET `password`='$new' WHERE `userid`='".$_SESSION['id']."'";
					$query="UPDATE `userDB` SET `hashed_pswd`='$new' WHERE `userid`='".$_SESSION['id']."'";
					if($result=$connectdb->query($query)){
						$resultado="Password Updated";
						$_SESSION['login']='1';
						$_SESSION['defaultpswd']=0;
						$class="tgreen";
						$flagredir=1;
					}else{
						$resultado="ERROR!";
						$class="tred";
					}
				}
			}else{
				$resultado="Invalid old Password";
				$class="tred";
			}
		}

	}
}

$connectdb->close();


?>
<?php if($resultado!=NULL){
echo "<table width='600px' style='margin: auto' class='$class'>
	<tr class='title'>
		<th>$resultado</th>
	</tr>
</table>";
}

if($flagredir==1){
	echo"<script>
			$(function(){
				setTimeout(function(){
					window.location.assign('/app');
				},3000);
			});
		</script><br><p style='text-align: center'>Espera un momento para ser redirigido a la página de inicio</p>";

		exit;
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
<br>
<table width='600px' style='margin: auto' class='t2'><form name='pwdchg' method='post' id='form' action='<?php $_SERVER['PHP_SELF']; ?>'><input name='id' type'text' value='<?php $_SESSION['id']; ?>' hidden>
	<tr class='title'>
		<th colspan=2>Change Password for: <?php echo $_SESSION['user']; ?></th>
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
<div style='width: 600px; margin: auto;'>*Si tu password es el que se asigno por default, debes cambiarlo para poder hacer uso del sistema</div>
