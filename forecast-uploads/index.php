<?php
include_once("../modules/modules.php");

initSettings::start(true,"schedules_upload");

?>
<br>
<link rel="stylesheet" href="/js/periodpicker/build/jquery.periodpicker.min.css">
<script src="/js/periodpicker/build/jquery.periodpicker.full.min.js"></script>
<script>

$(function() {
    $('#inicio').periodpicker({
		end: '#fin',
		lang: 'en',
		animation: true
	});
});
</script>

<table class='t2' style='width:600px; margin:auto'><form action="upload.php" method="post" enctype="multipart/form-data">
	<tr class='title'>
		<th colspan=10>Subir Participacion Forecast</th>
	</tr>
	<tr class='title'>
		<td style='width:33%'>Periodo</td>
		<td style='width:33%'>Programa</td>
		<td style='width:33%'>Archivo</td>
	</tr>
	<tr class='pair'>
		<td><input type='text' name='start' id='inicio' required><input type='text' name='end' id='fin' required></td>
		<td class='pair'><select name="skill" required><option value=''>Selecciona...</option>
		<?php  $query="SELECT * FROM PCRCs WHERE forecast=1 ORDER BY Departamento";
            if($result=Queries::query($query)){
              while($fila=$result->fetch_assoc()){
                echo "<option value='".$fila['id']."'>".$fila['Departamento']."</option>";
              }
            }
     ?>
		 </select></td>
		<td><input type="file" name="fileToUpload" id="fileToUpload"></td>
	</tr>
	<tr class='total'>
		<td colspan=10><input type="submit" value="Upload" name="submit"></td>
	</tr>
	<tr class='pair'><td colspan=100><a href="forecast_upload_format.csv" download>Descargar template para Upload</a><br>
										<a href="analisis_participacion.xlsx" download>Descargar XLS para analisis</a>
	</td></tr>
</form></table>

</body>
<script>
$(":date").dateinput({ trigger: true, format: 'dd mmmm yyyy', max: -1 })

// use the same callback for two different events. possible with bind
$(":date").bind("onShow onHide", function()  {
	$(this).parent().toggleClass("active");
});

// when first date input is changed
$(":date:first").data("dateinput").change(function() {
	// we use it's value for the seconds input min option
	$(":date:last").data("dateinput").setMin(this.getValue(), true);
});
</script>
</html>
