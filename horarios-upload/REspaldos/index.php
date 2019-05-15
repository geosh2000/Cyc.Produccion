<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];
if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="schedules_upload";
$menu_programaciones="class='active'";
include("../common/scripts.php");
//echo session_id()."<br>";
include("../common/menu.php")
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

<table class='t2' style='width:600px; margin:auto'><form action="uploadV2.php" method="post" enctype="multipart/form-data">
	<tr class='title'>
		<th colspan=2>Subir Archivo de Horarios</th>
	</tr>
	<tr class='title'>
		<td style='width:50%'>Periodo</td>
		<td style='width:50%'>Archivo</td>
	</tr>
	<tr class='pair'>
		<td><input type='text' name='start' id='inicio' required><input type='text' name='end' id='fin' required></td>
		<td><input type="file" name="fileToUpload" id="fileToUpload"></td>
	</tr>
	<tr class='total'>
		<td colspan=2><input type="submit" value="Upload" name="submit"></td>
	</tr>
	<tr class='pair'><td colspan=100><a href="formato.csv" download>Descargar template para Upload</a><br>
										<a href="convertidor.xlsx" download>Descargar XLS para convertir</a>
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
