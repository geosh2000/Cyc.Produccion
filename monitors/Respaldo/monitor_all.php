<?php
include_once("../modules/modules.php");

initSettings::start(false);
initSettings::printTitle('Monitores');

?>

<style>
    .frame{
			display: inline-block;
			vertical-align: top;
			width: 100%;
			height: 100%;
			border: 0px;
			margin: 0px;
			overflow: hidden;
			resize: horizontal
		}
</style>
<script>
  $(function(){
    var iframe = $('#ventaLive frame').contents();
    iframe.scrollTop(880);
  });
</script>
<div id='ventaLive' style='display:inline-block; width:33%; height:100%; vertical-align: top;'>
  <iframe src='/monitors/participacion_cc.php' class='frame'></iframe>
</div>
<div style='display:inline-block; width:33%; height:100%; vertical-align: top;'>
  <iframe src='/pt-did-monitor' class='frame'></iframe>
</div>
<div style='display:inline-block; width:33%; height:100%; vertical-align: top;'>
  <iframe src='/monitors/queues.php?colas=206|207|208' class='frame'></iframe>
</div>