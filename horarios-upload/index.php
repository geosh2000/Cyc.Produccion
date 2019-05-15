<?php

include_once('../modules/modules.php');

initSettings::start(true, 'schedules_upload');
initSettings::printTitle('Subir Horarios CC');

$tbody="<td><input type='text' name='start' id='inicio' required><input type='text' name='end' id='fin' required></td><td><input type='file' name='fileToUpload' id='fileToUpload'></td>";

Filters::showFilter('uploadV2.php',"POST' enctype='multipart/form-data",'upload', 'Upload',$tbody);

?>
<script>

$(function() {
    $('#inicio').periodpicker({
		end: '#fin',
		lang: 'en',
		animation: true
	});
});
</script>
<br>
<a href="formato.csv" download>Descargar template para Upload</a><br>
<a href="convertidor.xlsx" download>Descargar XLS para convertir</a>
