<head><meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
    
    <title>Tendencias por dia</title>

    <!-- include the Tools -->
   
      

	<!-- picker styling -->
    <link rel="stylesheet" type="text/css"
          href="http://comeycome.com/pt/styles/pickerwdw.css"/>

    <!-- calendar styling -->
    <link rel="stylesheet" type="text/css"
          href="http://comeycome.com/pt/styles/calendar.css"/>

</head>
<body bgcolor=#000000>
<form method="POST" action='$_SERVER['PHP_SELF']' name="SelctDays" id="flight">
  <label>
    Start <br/>
    <input type="date" name="start" data-value="-7" value="<? echo $Pstart; ?>"/>
  </label>

  <label>
    End <br/>
    <input type="date" name="end" data-value="-1" value="<? echo $Pend; ?>"/>
  </label>
  
   <Dlabel>
    Mon<br/>
    <input type="checkbox" name="dia1" value="1"<? if($Pdw[1]==1){ echo " checked";} ?>/>
  </Dlabel>
   <Dlabel>
    Tue<br/>
    <input type="checkbox" name="dia2" value="1"<? if($Pdw[2]==1){ echo " checked";} ?>/>
  </Dlabel>
   <Dlabel>
    Wen<br/>
    <input type="checkbox" name="dia3" value="1"<? if($Pdw[3]==1){ echo " checked";} ?>/>
  </Dlabel>
<Dlabel>
    Thu<br/>
    <input type="checkbox" name="dia4" value="1"<? if($Pdw[4]==1){ echo " checked";} ?>/>
  </Dlabel>
   <Dlabel>
    Fri<br/>
    <input type="checkbox" name="dia5" value="1"<? if($Pdw[5]==1){ echo " checked";} ?>/>
  </Dlabel>
   <Dlabel>
    Sat<br/>
    <input type="checkbox" name="dia6" value="1"<? if($Pdw[6]==1){ echo " checked";} ?>/>
  </Dlabel>
<Dlabel>
    Sun<br/>
    <input type="checkbox" name="dia7" value="1" <? if($Pdw[7]==1){ echo " checked";} ?>/>
  </Dlabel>
  <Slabel>
  <input type="submit" value="Consultar">
  </Slabel>
  
  
</form>


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