<head><meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
    
    <title><?php echo $showSkill; ?> - Volumen por d&iacutea</title>

    <!-- include the Tools -->
    <script src="http://cdn.jquerytools.org/1.2.6/full/jquery.tools.min.js"></script>
      

	<!-- picker styling -->
    <link rel="stylesheet" type="text/css"
          href="http://wfm.pricetravel.com/styles/pickerhist.css"/>

    <!-- calendar styling -->
    <link rel="stylesheet" type="text/css"
          href="http://wfm.pricetravel.com/styles/calendar.css"/>

</head>
<body bgcolor=#000000>
<form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>" name="SelctDays" id="flight">
  <label>
    Date <br/>
    <input type="date" name="start" data-value="<?php echo $CalValue; ?>" value="<?php echo $Pstart; ?>" onchange="this.form.submit();"/>
  </label>
  <Slabel>
  <select name='skill'>
  	
  	<option value="Servicio a Cliente" <?phpecho $opt_SC; ?>>Servicio a Cliente</option>
  	<option value="Soporte Agencias" <?phpecho $opt_SA; ?>>Soporte Agencias</option>
  	<option value="Trafico MP" <?phpecho $opt_TMP; ?>>Trafico MP</option>
  	<option value="Trafico MT" <?phpecho $opt_TMT; ?>>Trafico MT</option>
  	<option value="Ventas" <?phpecho $opt_V; ?>>Ventas</option>
  </select>
  </Slabel>
  
  <label>
  <input type="submit" value="Consultar">
  </label>
  
  
</form>


<script>
$(":date").dateinput({ trigger: true, format: 'dd mmmm yyyy', max:0 })

// use the same callback for two different events. possible with bind
$(":date").bind("onShow onHide", function()  {
	$(this).parent().toggleClass("active");
});



</script>