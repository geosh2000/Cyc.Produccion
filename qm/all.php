<?php
header("Location: http://wfm.pricetravel.com.mx/monitors/all.php");
include("../common/scripts.php");
?>
<html>
	<style>
		body{
			zoom: 0.75;
		}
		.frame{
			width: 33%;
			height: 468px;
			border: 0px;
			margin: 0px;
			overflow: hidden;
		}
	</style>
	<script>
		$(function(){
			setInterval(function(){
				$( '#pya' ).attr( 'src', function ( i, val ) { return val; });
			},60000);
		});
	</script>
	<iframe src='http://wfm.pricetravel.com.mx/qm/qm2all.php' class='frame'></iframe>
	<iframe src='http://wfm.pricetravel.com.mx/qm/qm3all.php' class='frame'></iframe>
	<?php if($_GET['piso']==1){
		echo "<iframe id='pya' src='http://wfm.pricetravel.com.mx/qm/pya.php' class='frame'></iframe>";
	}
	?>
	<iframe <?php if($_GET['piso']==1){ echo "style='height: 545'";} ?> src='http://wfm.pricetravel.com.mx/qm/qm_all.php?dep=SAC' class='frame'></iframe>
	<iframe <?php if($_GET['piso']==1){ echo "style='height: 545'";} ?> src='http://wfm.pricetravel.com.mx/qm/qm_all.php?dep=VentasMP' class='frame'></iframe>
	<iframe <?php if($_GET['piso']==1){ echo "style='height: 545'";} ?> src='http://wfm.pricetravel.com.mx/qm/qm_all.php?dep=Ventas' class='frame'></iframe>
	<iframe src='http://wfm.pricetravel.com.mx/qm/qm_all.php?dep=Upsell' class='frame'></iframe>
	<iframe src='http://wfm.pricetravel.com.mx/qm/qm_all.php?dep=TMP' class='frame'></iframe>
	<iframe src='http://wfm.pricetravel.com.mx/qm/qm_all.php?dep=Agencias' class='frame'></iframe>
	<?php if($_GET['piso']!=1){
		echo "<iframe src='http://wfm.pricetravel.com.mx/qm/qm_all.php?dep=TMT' class='frame'></iframe>";
	}
	?>
	
</html>