<head>
<link rel="stylesheet" type="text/css"
          href="http://comeycome.com/pt/styles/tables1.css"/>
</head>

<html>
<body>
<?php include("../common/menu.php") ?>
<table class='t2' style='width:80%; margin:auto'>
	<tr class='title'>
		<th colspan=100>Subir Archivo de Llamadas</th>
	</tr>
	<tr class='pair'>
		<td style='width:50%'><form action="answ2015.php" method="post" enctype="multipart/form-data">Select file to upload:</td>
		<td style='width:50%'><input type="file" name="fileToUpload" id="fileToUpload" required></td>
	</tr>
	<tr class='odd'>
		<td>Contestadas<input type='radio' name='tipo' value='ans' required></td>
		<td>Abandonadas<input type='radio' name='tipo' value='abn' required></td>
	</tr>
	<tr class='total'>
		<td colspan=100><input type='submit' value='Subir'></td>
	</tr>
	
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