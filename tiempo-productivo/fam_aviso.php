<?php
$Event_Asesor=$_SESSION['asesor_id'];

function printEvent($event_name,$event_corto,$tipo_evento,$event_date,$date_limit_inscript,$date_limit_display,$default_display){
	global $Event_Asesor, $connectdb;	
	if(date('Y-m-d H:i:s')<=date('Y-m-d H:i:s',strtotime($date_limit_display))){
		$query="SELECT asistira FROM Fams WHERE asesor=$Event_Asesor AND Evento='$event_corto'";
		if($result=$connectdb->query($query)){
			$fila=$result->fetch_assoc();
			$fam_asist=$fila['asistira'];
		}
		
		if($default_display==1){
			
			if(date('Y-m-d H:i:s')<=date('Y-m-d H:i:s',strtotime($date_limit_inscript)) || $fam_asist==1){
				echo "<br>
					<div style='width: auto; margin: auto; text-align: center'>"
					."<div style='width:800px; background: yellow; color: black; margin: auto; text-align: center; display: inline-block; height: 37px; font-size:20; padding-top:15'>"
					."¿Asistirás al $tipo_evento de $event_name ($event_date)? </div>"; 
						if($fam_asist==1){
								echo "<div style='width:400px; height: 45; background: Gainsboro; color: black; margin: auto; text-align: center; display: inline-block;'> Solicitud de Asistencia Enviada <img src='/images/ok.png' width=20 height=20> <button class='buttonlarge button_red_w send' id='xld'  resp='no' event='$event_corto'>Cancelar Solicitud</button></div>";
							}else{
								if(date('Y-m-d H:i:s')<=date('Y-m-d H:i:s',strtotime($date_limit_inscript))){
									echo "<div style='width:400px; height: 48; padding-top:5px; background: yellow; color: black; margin: auto; text-align: center; display: inline-block;'><button class='buttonlarge button_green_w send' id='si' resp='si' event='$event_corto'>Enviar Solicitud</button></div>";
								}else{
									echo "<div style='width:400px; height: 35; padding-top:17px; background: Gainsboro; color: black; margin: auto; text-align: center; display: inline-block;'> Tiempo de Solicitudes Agotado</div>";
								}
							}
				echo "</div>";
			}
		}else{
			echo $default_display;
		}
		
	}
}
?>
<script>
	function sendResp(response, evento){
        $.ajax({
            url: "fams.php",
            type: 'POST',
            data:  {evento: evento, respuesta: response, asesor: '<?php echo $_SESSION['asesor_id']; ?>'},
            dataType: 'html', // will automatically convert array to JavaScript
            })
            .done(function(data){
            	location.reload();
            	//alert(data);
            });
   }

	function ExpXca(response, producto){
        $.ajax({
            url: "xx.php",
            type: 'POST',
            data:  {producto: producto, respuesta: response, asesor: '<?php echo $_SESSION['asesor_id']; ?>'},
            dataType: 'html', // will automatically convert array to JavaScript
            })
            .done(function(data){
            	if(data=='Done!'){
            		var src=$('#'+producto).attr('src');
            		var on=$('#'+producto).attr('on');
            		$('#'+producto).attr('src',on);
            		$('#'+producto).attr('on',src);
            		$('#'+producto).attr('off',on);
            		$('#'+producto).attr('val',response);
            	}else{
            		alert(data);
            	}
            });
   }

	$(function(){
		$('.send').click(function(){
			sendResp($(this).attr('resp'),$(this).attr('event'));
		});
		
		$('.xcaret').click(function(){
			var newval=$(this).attr('val');
			if($(this).attr('val')==0){
				newval=1;
			}else{
				newval=0;
			}
			ExpXca(newval,$(this).attr('id'));
		});
		
		$('.xcaret').hover(
			function(){
				$(this).attr('src',$(this).attr('on'));
			},
			function(){
				$(this).attr('src',$(this).attr('off'));
		});
	});
</script>

<style>
	.center-block{
		display: block;
		margin-left: auto;
		margin-right: auto;
	}
	
</style>
<?php

/*

"id"	"Departamento"
"1"	"Gerencia"
"2"	"WFM"
"3"	"Ventas"
"4"	"SAC IN"
"5"	"Upsell"
"6"	"SAC BO"
"7"	"Agencias"
"8"	"Trafico MT"
"9"	"Trafico MP"
"10"	"Calidad"
"11"	"Mesa de Expertos"
"12"	"Supervisores CC"
"18"	"Grupos"
"13"	"Corporativo"
"14"	"LTMB"
"15"	"Trafico BO"
"16"	"Concierge"
"17"	"Viajes Internos"
"19"	"Trafico MP - Queues"
"20"	"Trafico MP - Mail"
"21"	"Trafico MP - Revisados"
"22"	"Trafico MP - Emisiones"
"23"	"Trafico MT - Queues"
"24"	"Trafico MT - Mail"
"25"	"Trafico MT - Revisados"
"26"	"Trafico MT - Emisiones"
"27"	"Trafico MT - Inplant"
"28"	"Ventas Salinillas"
"29"	"PDV"
"30"	"PDV - Erick"
"31"	"PDV-Outlet"
"32"	"Capacitador"
"33"	"Practicante"
"34"	"Trafico MT - Mesa de Servicios"
"35"	"Ventas MP"
"36"	"Servicio a Cliente - Cambios Urgentes"
"37"	"BackOffice - Mailing"
"38"	"BackOffice - Confirming"
"39"	"BackOffice - Reembolsos"
"40"	"BackOffice - Mejora Continua"
"41"	"Trafico MT - Contactos"
"42"	"Trafico MP - Segundo Nivel"
"43"	"Ventas Celaya"
"44"	"Gerencia CO"
"45"	"Agencias BO"
*/


//Evento Platinum Yucatan Princess
switch($_SESSION['dep']){
	case 1: case 2: case 3: case 4: case 5: case 6: case 7: case 8: case 9: case 10: case 11: case 12: case 16: case 17: case 19: case 32: case 35: case 45:
		printEvent('Platinum Yucatan Princess','Princess_2016','FAM / Estancia','17 a 18 Dic','2016-11-10 23:59:59','2016-12-15 23:59:59',1);
		break;
}

//CenaFam Solaris
switch($_SESSION['dep']){
case 1: case 12: case 2: case 4: case 5: case 6: case 7: case 3: case 35:
		printEvent('Solaris','Solaris_2016','Cena/FAM ','01 Dic','2016-11-17 23:59:59','2016-11-30 23:59:59',1);
		break;
}


