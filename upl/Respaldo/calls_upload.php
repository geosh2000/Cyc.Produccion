<?php
include_once('../modules/modules.php');

initSettings::start(true,'upload_info');
initSettings::printTitle('Subir Llamadas');
timeAndRegion::setRegion('Cun');
?>

<table class='t2' style='width:80%; margin:auto'>
	<tr class='title'>
		<th colspan=100>Subir Archivo de Llamadas</th>
	</tr>
	<tr class='pair'>
		<td><form action="upload_callsV2.php" method="post" enctype="multipart/form-data">Select file to upload: <input type="file" name="fileToUpload" id="fileToUpload" required></td>
		<td>Año: <select name='year' required><option value=''>Selecciona...</option><option value='2016'>2016</option><?php if(date('Y')==2017){ echo "<option value='2017'>2017</option>";} ?></select></td>
	</tr>
	<tr class='total'>
		<td colspan=100><input type='submit' value='Subir'></td>
	</tr>

</form></table>

</body>
</html>
