<?php
include("../connectDB.php");
include("../common/scripts.php");
date_default_timezone_set('America/Bogota');



?>

<script>
	function sendRequest(){
        $.ajax({
            url: "query_sesiones.php",
            type: 'GET',
            dataType: 'json', // will automatically convert array to JavaScript
            success: function(array) {
                var data=array;
                
                $('.block_jornada').each(function(){
                	var id=$(this).attr('id');
                	
                	var inicio_j = (typeof data[id]['inicio_j_pix'] === 'undefined') ? 0 : data[id]['inicio_j_pix'];
                	var duracion_j = (typeof data[id]['duracion_j_pix'] === 'undefined') ? 0 : data[id]['duracion_j_pix'];
                	var x1_j = (typeof data[id]['inicio_x1_pix'] === 'undefined') ? 0 : data[id]['inicio_x1_pix'];
                	var duracion_x1 = (typeof data[id]['duracion_x1_pix'] === 'undefined') ? 0 : data[id]['duracion_x1_pix'];
                	var inicio_x2 = (typeof data[id]['inicio_x2_pix'] === 'undefined') ? 0 : data[id]['inicio_x2_pix'];
                	var duracion_x2 = (typeof data[id]['duracion_x2_pix'] === 'undefined') ? 0 : data[id]['duracion_x2_pix'];
                	var inicio_c = (typeof data[id]['inicio_c_pix'] === 'undefined') ? 0 : data[id]['inicio_c_pix'];
                	var duracion_c = (typeof data[id]['duracion_c_pix'] === 'undefined') ? 0 : data[id]['duracion_c_pix'];
                	var inicio_s = (typeof data[id]['entrada_pix'] === 'undefined') ? 0 : data[id]['entrada_pix'];
                	var duracion_s = (typeof data[id]['duracion_pix'] === 'undefined') ? 0 : data[id]['duracion_pix'];
                	var ausentismo = (typeof data[id]['Ausentismo'] === 'undefined') ? 0 : data[id]['Ausentismo'];
                	var retardo = (typeof data[id]['Retardo'] === 'undefined') ? 0 : data[id]['Retardo'];
                	
                	//Set Jornada
                	$('#'+id+' .jornada').css('left',data[id]['inicio_j_pix']).css('width',data[id]['duracion_j_pix']);
                	//Set X1
                	$('#'+id+' .x1_j').css('left',data[id]['inicio_x1_pix']).css('width',data[id]['duracion_x1_pix']);
                	//Set X2
                	$('#'+id+' .x2_j').css('left',data[id]['inicio_x2_pix']).css('width',data[id]['duracion_x2_pix']);
                	//Set Comida
                	$('#'+id+' .comida_j').css('left',data[id]['inicio_c_pix']).css('width',data[id]['duracion_c_pix']);
                	
                	//Set Ausentismo // Retardo
                	if(ausentismo!=0){
	                	$('#'+id+' .jornada').css('background','#f4bf42').text(ausentismo).css('text-align','right');
	                }else{
	                	switch(retardo){
	                		case "A":
	                			$('#'+id+' .jornada').css('background','#ffc9fd').text(retardo).css('text-align','right');
	                			break;
	                		case "B":
	                			$('#'+id+' .jornada').css('background','#e20053').text(retardo).css('text-align','right');
	                			break;
	                		case "Erroneo":
	                			$('#'+id+' .jornada').css('background','#7900e2').text(retardo).css('text-align','right');
	                			break;
	                	}
	                }
	                
	                //Set Sesion
                	$('#'+id+' .sesion').css('left',data[id]['entrada_pix']).css('width',data[id]['duracion_pix']);
                	
                	
                });
           }
            
        });
        
		
    }
    
    
    
    $(function(){
    	sendRequest();
    	
    	//Stick Header
    	var s = $("#dash");
		var pos = s.position();	
		var stickermax = $(document).outerHeight() - s.outerHeight() - 40; //40 value is the total of the top and bottom margin
		$(window).scroll(function() {
			var windowpos = $(window).scrollTop();
			if (windowpos >= pos.top && windowpos < stickermax) {
				s.attr("style", ""); //kill absolute positioning
				s.addClass("stick"); //stick it
			} else if (windowpos >= stickermax) {
				s.removeClass(); //un-stick
				s.css({position: "absolute", top: stickermax + "px"}); //set sticker right above the footer
				
			} else {
				s.removeClass(); //top of page
			}
		});

    });
</script>

<style>
	.div_height{
		height: 15px;
	}
	
	.block_jornada{
		background: grey; 
		width: 1500; 
		margin: auto; 
		position: relative;	
		border: solid 1px black;
		padding: 0;
	}
	
	.inner_jornada{
		width: 0; 
		position: absolute; 
		left: 0; 
		height:15;
	}
	
	.jornada{
		background: cyan;
		top: 0px;
	}
	
	.comida_j{
		background: #a3b200;
		top: 0px;
	}
	
	.x1_j, .x2_j{
		background: #e5daac;
		top: 0px;
	}
	
	
	.sesion{
		background: green;
		top: 3px;
	}
	
	.pausa{
		background: yellow;
		top: 6px;
	}
	
	#dash{
		padding: 20px;
		margin: auto;
		width: 100%;
		background: navy;
		display: block;
		z-index: 100;
		color: white;
	}
	
	.stick {
		position:fixed;
		top:0px;
	}
</style>


<br>
<div id='contain' style='width: 1600px; margin: auto'>
<div id='dash'>
	<div style='height: 50px; background: white; width: 500px; margin-left: 1;'></div>
</div>

<div>
<?

function print_Div($id,$nombre){
	echo "<div class='block_jornada div_height' id='$id'>$nombre\n\t
				<div class='inner_jornada jornada'></div>\n\t
				<div class='inner_jornada comida_j'></div>\n\t
				<div class='inner_jornada x1_j'></div>\n\t
				<div class='inner_jornada x2_j'></div>\n\t
				<div class='inner_jornada sesion'></div>\n\t
				<div class='inner_jornada pausa'></div>\n\t
			</div>\n\n\t";
}

$query="SELECT a.id, `N Corto` FROM Asesores a LEFT JOIN (SELECT * FROM `Historial Programacion` WHERE Fecha=CURDATE()) b ON a.id=b.asesor WHERE Activo=1 AND `id Departamento` NOT IN (29) ORDER BY `jornada start`";

if($result=$connectdb->query($query)){
	while ($fila = $result->fetch_assoc()) {
		print_Div($fila['id'],$fila['N Corto']);
	}
}else{
	echo $connectdb->error."<br> ON <br>$query<br>";
}
?>

</div></div>