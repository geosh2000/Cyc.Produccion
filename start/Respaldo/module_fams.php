<?php
include_once('../modules/modules.php');

class Event{
  public $Event_Asesor;
  public $departamento;
  public $deps_array;
  public $event_name;
  public $event_corto;
  public $tipo_evento;
  public $event_date;
  public $date_limit_inscript;
  public $date_limit_display;
  public $default_display;

  public function __construct($asesor, $dep, $deps_array, $event_name,$event_corto,$tipo_evento,$event_date,$date_limit_inscript,$date_limit_display,$default_display){
    $this->Event_Asesor=$asesor;
    $this->departamento=$dep;
    $this->deps_array=$deps_array;
    $this->event_name=$event_name;
    $this->event_corto=$event_corto;
    $this->tipo_evento=$tipo_evento;
    $this->event_date=$event_date;
    $this->date_limit_inscript=$date_limit_inscript;
    $this->date_limit_display=$date_limit_display;
    $this->default_display=$default_display;
  }

  public function printEvent(){

    if(in_array($this->departamento,$this->deps_array)){

      if(date('Y-m-d H:i:s')<=date('Y-m-d H:i:s',strtotime($this->date_limit_display))){
    		$query="SELECT asistira FROM Fams WHERE asesor=$this->Event_Asesor AND Evento='$this->event_corto'";
    		if($result=Queries::query($query)){
    			$fila=$result->fetch_assoc();
    			$fam_asist=$fila['asistira'];
    		}

    		if($this->default_display==1){

    			if(date('Y-m-d H:i:s')<=date('Y-m-d H:i:s',strtotime($this->date_limit_inscript)) || $fam_asist==1){
    				echo "<br>
    					<div style='width: auto; margin: auto; text-align: center'>"
    					."<div style='width:800px; background: yellow; color: black; margin: auto; text-align: center; display: inline-block; height: 37px; font-size:20; padding-top:15'>"
    					."¿Asistirás al $this->tipo_evento de $this->event_name ($this->event_date)? </div>";
    						if($fam_asist==1){
    								echo "<div style='width:400px; height: 45; background: Gainsboro; color: black; margin: auto; text-align: center; display: inline-block;'> Solicitud de Asistencia Enviada <img src='/images/ok.png' width=20 height=20> <button class='buttonlarge button_red_w send' id='xld'  resp='no' event='$this->event_corto'>Cancelar Solicitud</button></div>";
    							}else{
    								if(date('Y-m-d H:i:s')<=date('Y-m-d H:i:s',strtotime($this->date_limit_inscript))){
    									echo "<div style='width:400px; height: 48; padding-top:5px; background: yellow; color: black; margin: auto; text-align: center; display: inline-block;'><button class='buttonlarge button_green_w send' id='si' resp='si' event='$this->event_corto'>Enviar Solicitud</button></div>";
    								}else{
    									echo "<div style='width:400px; height: 35; padding-top:17px; background: Gainsboro; color: black; margin: auto; text-align: center; display: inline-block;'> Tiempo de Solicitudes Agotado</div>";
    								}
    							}
    				echo "</div>";
    			}
    		}else{
    			echo $this->default_display;
    		}

    	}
    }
  }

  public static function printScripts(){
    echo "<script>
          	function sendResp(response, evento){
                  $.ajax({
                      url: '".MODULE_PATH."/query_fams.php',
                      type: 'POST',
                      data:  {evento: evento, respuesta: response, asesor: '".$_SESSION['asesor_id']."'},
                      dataType: 'html', // will automatically convert array to JavaScript
                      })
                      .done(function(data){
                      	location.reload();
                      	//alert(data);
                      });
             }

          	function ExpXca(response, producto){
                  $.ajax({
                      url: '".MODULE_PATH."/xcaret_query.php',
                      type: 'POST',
                      data:  {producto: producto, respuesta: response, asesor: '".$_SESSION['asesor_id']."'},
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

          </style>";
  }
}


?>

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
