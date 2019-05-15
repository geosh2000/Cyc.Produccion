<?php
include_once("../modules/modules.php");

initSettings::start(true,"schedules_upload");

?>
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
<br>
<table class='t2' style='width:600px; margin:auto'><form action="vol_edit.php" method="post" enctype="multipart/form-data">
	<tr class='title'>
		<th colspan=10>Editor de Volumenes (Forecast)</th>
	</tr>
	<tr class='title'>
		<td style='width:33%'>Periodo</td>
		<td style='width:33%'>Programa</td>
		<td class='total' rowspan=2><input type="submit" value="Consultar" name="submit"></td>
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
     ?></select></td>
		
	</tr>
	
</form></table>

</body>

</html>
