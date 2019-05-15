<?php
header('Location: http://wfm.pricetravel.com.mx/monitores/index.php');
include("../common/scripts.php");
?>

<script>
	
	$(function(){
		tag='montos';
		setInterval(function(){
			switch(tag){
				case 'montos':
					tag='ocupacion';
					last='montos';
					break;
				case 'ocupacion':
					tag='montos';
					last='ocupacion';
					break;
			}
			
			animateFrame(last,tag);
			
			
		},20000);
		
		function animateFrame(hide,show){
			$('#'+hide).hide('1000');
			$('#'+show).show('1000');
			$( '#'+hide+' iframe').attr( 'src', function ( i, val ) { return val; });
		}
		
		function replace(tagOK){
			tmp=$('#'+tagOK);
			$('#'+tagOK).remove();
			$('#display').append(tmp);	
		}
	});
	
</script>



<div id='display' style='width:1905; height:1050px; margin: auto; overflow: hidden'>
	<div class='frame' id='montos' style='float: left; margin: 0; padding: 0; '><iframe width="1900" height="1050" src="upsell_montos.php"></iframe></div>
	<div class='frame' id='ocupacion' style='float: left; margin: 0; padding: 0; '><iframe width="1900" height="1050" src="upsell_occupation.php"></iframe></div>	
</div>

