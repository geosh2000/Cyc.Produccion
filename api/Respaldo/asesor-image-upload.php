<?php

if(isset($_POST['submit'])){

	$target_dir = "../img/asesores/";
	$target_file = $target_dir . $_GET['id'];
	$uploadOK = 1;
	$FileType = ".jpg";
	$filename = $target_file . $FileType;

	if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $filename)) {
	    $status='success';
	    $msg="Imagen guardada correctamente con id: ".$_GET['id'];

	} else {
	    $status='error';
			$upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/img/asesores";

			if (is_dir($upload_dir)){
				$msg2 = "Dir Exists";
				if(is_writable($upload_dir)) {
			    $msg2 .= " || Dir is writable";
				} else {
				  $msg2 .= " || Dir is NOT writable";
				}
			}else{
				$msg2 = "Dir Doesnt Exist";
			}

	    $msg="Error al guardar imagen => ".$_FILES["fileToUpload"]["error"]."<br>$upload_dir<br>$msg2";
		}

		echo $msg;
		exit;
}

if($_GET['id']==''){
	echo "No es posible asignar fotos a asesores sin numero de colaborador";
	exit;
}

?>
<table class='t2' id='addform' style='width:80%; margin:auto'>
	<tr class='pair'>
    <form action="" method="post" enctype="multipart/form-data">
		<td>Sube una foto para el asesor <?php echo $_GET['id']; ?>: <input type="file" name="fileToUpload" id="fileToUpload" required></td>
	</tr>
	<tr class='total'>
		<td colspan=100><input type='submit' name='submit' value='Subir'></td>
	</tr>
</form></table>
