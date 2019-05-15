<script type="text/javascript" src="//comeycome.com/pt/js/jquery-1.9.1.js"></script>

<script>
$('#isichk').prop('checked', true);

$('#isichk').on('click', function(){
    if($(this).is(':checked')){
        $('#moper').prop('readonly', true);
        $('#moper').prop('disabled', false);    
        //alert('checked');
    }else{
        $('#moper').prop('readonly', true);
        $('#moper').prop('disabled', true);    
        //alert('not checked');
    }    
});

//$('#checker').button(); //Requires jQuery UI
</script>

<script type="text/javascript">//<![CDATA[
$(window).load(function(){
$('#isichk').prop('checked', true);

$('#isichk').on('click', function() {
  if ($(this).is(':checked')) {
    $('#moper').prop('readonly', true);
    $('#moper').prop('disabled', false);
    //alert('checked');
  } else {
    $('#moper').prop('readonly', true);
    $('#moper').prop('disabled', true);
    //alert('not checked');
  }
});

//$('#checker').button(); //Requires jQuery UI

});//]]> 

</script>

<table width='100%' class='t2'><form name='senddias' method='post' action=''>
	<tr class='title'>
		<th colspan=100>Seleccion de Vacaciones para David Ramirez (7 dias)</th>
	</tr>
	<input type='text' name='asesor' value='13' hidden>
	<input type='text' name='tipo' value='1' hidden>
	<input type='text' name='dias' value='7' hidden>
	<input type='text' name='motivo' value='' hidden>
	<tr class='title'>
		<td width='25%'>Descansos Intermedios:</td>
		<td  class='pair' width='25%'><select name='descansos' id="descansos" required><option value='0'>0</option><option value='1'>1</option><option value='2'>2</option><option value='3'>3</option><option value='4'>4</option><option value='5'>5</option><option value='6'>6</option><option value='7'>7</option><option value='8'>8</option></select></td>
		<td width='25%'>Beneficios:</td>
		<td class='pair' width='25%'><select name='beneficios' id="beneficios" required ><option value='0'>0</option><option value='1'>1</option><option value='2'>2</option><option value='3'>3</option><option value='4'>4</option><option value='5'>5</option><option value='6'>6</option><option value='7'>7</option><option value='8'>8</option></select></td>
	</tr>
	<tr class='title'>
		<td width='25%'>Fecha Inicial:</td>
		<td  class='odd' width='25%'><input type='date' name='inicio' id="datestart" required></td>
		<td width='25%'>Fecha Final:</td>
		<td class='odd' width='25%'><input type='date' name='fin' id="dateend" readonly required></select></td>
	</tr>
	<tr class='title'>
		<td width='25%'>Caso:</td>
		<td  class='pair' width='25%'><input type='text' name='caso' id="test" size=8 required></td>
		<td width='25%'>Observaciones:</td>
		<td class='pair' width='25%'><input type='text' name='comment' id="test"></td>
	</tr>
<?php if($ausentismo==1 || $ausentismo==2 || $ausentismo==3 || $ausentismo==9){ 

echo "
	<tr>
		<td width='25%'>ISI RRHH:</td>
		<td  class='pair' width='25%'><input type='checkbox' name='isichk' id='isichk'></td>
		<td width='25%'>Moper:</td>
		<td class='pair' width='25%'><input type='text' name='moper' id='moper'</td>
	</tr>";} ?>

	<tr class='total'>
		<td colspan=100><input type='submit' name='upload' value='Guardar'></td>
	</tr>
</form></table>