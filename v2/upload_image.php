<?php

include_once("../modules/modules.php");

initSettings::start(true,'config');
initSettings::printTitle('Pantallas Upload');

$cat=utf8_decode(strtoupper($_POST['categoria']));
$prov=utf8_decode(strtoupper($_POST['proveedor']));
$desc=utf8_decode(strtoupper($_POST['descripcion']));

$normalizeChars = array(
	    'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
	    'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
	    'Ï'=>'I', 'Ñ'=>'N', 'Ń'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
	    'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
	    'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
	    'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ń'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
	    'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f',
	    'ă'=>'a', 'î'=>'i', 'â'=>'a', 'ș'=>'s', 'ț'=>'t', 'Ă'=>'A', 'Î'=>'I', 'Â'=>'A', 'Ș'=>'S', 'Ț'=>'T',
	);

$connectdb=Connection::mysqliDB('CC');

$query="INSERT INTO pantallas_imageUrl (categoria, proveedor, descripcion) VALUES ('$cat','$prov','$desc')";
if($result=$connectdb->query($query)){
  $newID=$connectdb->insert_id;

  //Upload CSV File
  	$target_dir = "images/";
  	$target_file = $target_dir . $newID;
  	$uploadOK = 1;
  	$FileType = ".".substr($_FILES["fileToUpload"]['name'],strpos($_FILES["fileToUpload"]['name'],'.')+1,100);
  	$filename = $target_file . $FileType;

    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $filename)) {
        $status='success';
        $msg="Imagen guardada correctamente con id: $newID";
    } else {
        $status='error';
        $msg="Error al guardar imagen";

        $query="DELETE FROM pantallas_imageUrl WHERE id=$newID";
        $connectdb->query($query);
    }
}else{
  echo "ERROR! -> ".$connectdb->error." ON<br>$query";
}
?>
<script>
$(function(){
  showNoty(<?php echo "'$status','$msg',5000"; ?>);
})
</script>
