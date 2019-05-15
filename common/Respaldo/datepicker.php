<head><meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
    
    

    <!-- include the Tools -->
    
      

	<!-- picker styling -->
    <link rel="stylesheet" type="text/css"
          href="http://comeycome.com/pt/styles/picker.css"/>

    <!-- calendar styling -->
    <link rel="stylesheet" type="text/css"
          href="http://comeycome.com/pt/styles/calendar.css"/>

</head>
<script>
$(function(){
    $('#dateselect').datepicker();
});
</script>
<body bgcolor=#000000>
<form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>" name="SelctDays" id="flight">
  <label>

    <input type="text" name="start" id='dateselect' data-value="<?php echo $CalValue; ?>" value="<?php echo $Pstart; ?>" onchange=""/>
  </label>
 <?php if($showDeps==1){ echo "<Slabel>
  <select name='skill'>
  	
  	<option value='Servicio a Cliente' $opt_SC >Servicio a Cliente</option>
  	<option value='Soporte Agencias' $opt_SA >Soporte Agencias</option>
  	<option value='Trafico MP' $opt_TMP >Trafico MP</option>
  	<option value='Trafico MT' $opt_TMT >Trafico MT</option>
  	<option value='Ventas' $opt_V >Ventas</option>
  </select>
  ";
} ?>

<?php 
	
	if($showHour==1){ echo "<Slabel><table style='margin-top:-20px; border:solid transparent;'>
	<tr>
	<td>
  <select name='time' width='130px'>";
  	$tmptime="00:00:00";
  	$i=0;
  	while($i<48){
  		$tmpsegs=$i*30;
  		$tmpsel="";
  		if(date('H:i:s',strtotime($tmptime."+$tmpsegs minutes"))==$pickhour){$tmpsel="selected";}
  		echo "<option value='".date('H:i:s',strtotime($tmptime."+$tmpsegs minutes"))."' $tmpsel>".date('H:i:s',strtotime($tmptime."+$tmpsegs minutes"))."</option>";
  	$i++;
  	}	
  	
  echo "</select></td>
  <td style='color:white'>
  
  Auto: <input type='checkbox' name='autoselform' id='autosel' $autoselok></td></tr></table>
  </Slabel>";
} ?>
  
  <submitlabel>
  <input type="submit" value="Consultar">
  </submitlabel>
  
  
</form>


<script>
$(":date").dateinput({ trigger: true, format: 'dd mmmm yyyy', max:0 })

// use the same callback for two different events. possible with bind
$(":date").bind("onShow onHide", function()  {
	$(this).parent().toggleClass("active");
});



</script>