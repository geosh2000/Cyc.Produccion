<?php
include_once('../modules/modules.php');

initSettings::start(true,'upload_info');
initSettings::printTitle('Llamadas Oreka');
timeAndRegion::setRegion('Cun');
?>
<br>
<table class='t2' style='width:80%; margin:auto'>
	<tr class='title'>
		<th colspan=100>Subir Archivo de Llamadas</th>
	</tr>
	<tr class='pair'>
		<td><form action="upload_orekafile.php" method="post" enctype="multipart/form-data">Select file to upload: <input type="file" name="fileToUpload" id="fileToUpload" required></td>
	</tr>
	<tr class='total'>
		<td colspan=100><input type='submit' value='Subir'></td>
	</tr>

</form></table>

</body>
</html>
