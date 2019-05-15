<?php
$depto=$_GET['id'];
$color1="green";
$color2="blue";
$color3="red";
$metaok=17;
$metalow=13;
$width="122%";
$height="100%";

?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<?php

switch ($depto){
	case "ventas":
		include("charts_Ventas.php");
		$reloadTime=50000;
		$titulo="Ventas";
		break;
	case "sc":
		include("charts_SC.php");
		$reloadTime=60000;
		$titulo="Servicio a Cliente";
		break;
	case "upsell":
		$width="119%";
		$height="90%";
		include("charts_Upsell.php");
		$reloadTime=60000;
		$titulo="Upsell";
		break;
}



?>
<style>
	* {
	  @include box-sizing(border-box);
	}
	
	$pad: 20px;
	
	.grid {
	  background: white;
	  margin: 0 0 $pad 0;
	  
	  &:after {
	    /* Or @extend clearfix */
	    content: "";
	    display: table;
	    clear: both;
	  }
	}
	
	[class*='col-'] {
	  float: left;
	  padding-right: $pad;
	  .grid &:last-of-type {
	    padding-right: 0;
	  }
	}
	.col-2-3 {
	  width: 66.66%;
	}
	.col-1-3 {
	  width: 33.33%;
	}
	.col-1-2 {
	  width: <?php echo $width; ?>;
	}
	.col-1-4 {
	  width: 25%;
	}
	.col-1-8 {
	  width: 12.5%;
	}
	
	/* Opt-in outside padding */
	.grid-pad {
	  padding: $pad 0 $pad $pad;
	  [class*='col-']:last-of-type {
	    padding-right: $pad;
	  }
	}
	.chart {
	  width: 100%; 
	  height: <?php echo $height; ?>;
	}
</style>
<?php include("common/scripts.php"); ?>
<script>

var total =<?phpecho $reloadTime;?>;
var myVar = setInterval(function(){ myTimer() }, 1000);

function myTimer() {
   total= total-1000;
    document.getElementById("demo").innerHTML = "Reload in " + total/1000 + " sec.";
}
</script>
<script>
	setTimeout(function() {
	    window.location.reload();
	}, <?phpecho $reloadTime;?>);
	</script>
  <body bgcolor="white">

 
<style type="text/css">
.tg  {border-collapse:collapse;border-spacing:0;}
.tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
.tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
.tg .tg-yw4l{vertical-align:to;text-align:left;}
.tg .tg-yw4l1{vertical-align:to; color:#ffffff;text-align:center;font-size:46px;}
.tg .tg-lblp{font-weight:bold;font-size:22px;background-color:#2245a9;color:#ffffff;text-align:center;vertical-align:top}
.tg .tg-lblp1{font-weight:bold;font-size:22px;background-color:#FF681C;color:#ffffff;text-align:center;vertical-align:top}
.tg .tg-lblp2{font-weight:bold;font-size:22px;background-color:#FF0000;color:#ffffff;text-align:center;vertical-align:top}
.tg .tg-ouin{font-weight:bold;font-size:50px;background-color:#000000;color:#ffffff;text-align:center;vertical-align:top}
.tg .tg-ouin2{font-weight:bold;font-size:20px;background-color:#000000;color:#ffffff;text-align:center;vertical-align:top}
}
</style>



<table class="t2" style="width:1080px; margin:auto;font-size:50px">
<tr>
    <th class="title" colspan="15" style="font-size:50px">Dashboard <?php echo $titulo; ?></th>
  </tr>
  <tr>
    <th class="title" colspan="15" style="font-size:30px">Ultima Actualizacion: <?php echo $fecha[1]; ?></th>
  </tr>
  <tr>
    <th class="title" colspan="15" id="demo"></th>
  </tr>
  <?php if($depto=="sc"){echo "
  <tr>
  	<th class=\"tg-ouin2\" style=\"width: 13%\"></th>
  	<th class=\"tg-lblp\" style=\"width: 25%\">AHT correcto</th>
   	<th class=\"tg-lblp1\" style=\"width: 25%\">AHT Elevado</th>
   	<th class=\"tg-lblp2\" style=\"width: 25%\">AHT Cr&iacutetico</th>
    	<th class=\"tg-ouin2\" style=\"width: 12%\"></th>
  </tr>";}?>
</table>
 <br>
 <br>

    <div id="dual_x_div" style="width:1080px; height:1620px; margin:0 0 0 0;"></div>

	

</body>