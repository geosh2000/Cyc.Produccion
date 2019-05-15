
<link href='/js/jquery/jquery-ui.min.css' rel='stylesheet'><link rel='stylesheet' type='text/css' href='/styles/tables1.css'/><link rel='stylesheet' type='text/css' href='/styles/forms.css'/><link rel='stylesheet' type='text/css' href='/styles/greentables.css'/><link rel='stylesheet' type='text/css' href='/styles/picker.css'/><link rel='stylesheet' type='text/css' href='/styles/express-table-style.css'/><link rel='stylesheet' type='text/css' href='/js/tablesorter/css/theme.blue.css'/><link rel='stylesheet' type='text/css' href='/js/tablesorter/css/theme.jui.css'/><link rel='stylesheet' type='text/css' href='/styles/animate.css'/><link rel='stylesheet' type='text/css' href='/styles/calendar.css' /><link rel='stylesheet' href='/js/jquerycustom/jquery-ui.css'><script src='/js/jquery.tools.min.js'></script><script src='/js/jquery/jquery-1.10.2.js'></script><script src='/js/jquery/jquery-ui.js'></script><script type='text/javascript' src='/js/pnotify.custom.min.js'></script><script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script><script type='text/javascript' src='/js/jquery.PopUpWindow.js'></script><script type='text/javascript' src='/js/noty-2.3.8/js/noty/packaged/jquery.noty.packaged.min.js'></script><script type='text/javascript' src='/js/tablesorter/js/jquery.tablesorter.js'></script><script type='text/javascript' src='/js/tablesorter/js/jquery.tablesorter.widgets.js'></script><script type='text/javascript' src='/js/jquery.filtertable.js'></script><script>

$(function(){
	
	function printAsesor(data){
   			
		var tipo,segundos, motivo, tiempo, detalle, pdv;
		
		switch(data['status']){
			case 0:
				tipo='avail';
				segundos=data['availseg'];
				motivo='';
				tiempo=data['avail'];
				detalle='Tiempo Disponible';
				break;
			case 1:
				tipo='incall';
				segundos=data['callseg'];
				motivo='';
				tiempo=data['calldur'];
				detalle=data['queue'];
				break;
			case 2:
				tipo='onpause';
				segundos=data['pauseseg'];
				motivo=data['pausem'];
				tiempo=data['pausedur'];
				detalle=data['pausem'];
				break;
			case 3:
				tipo='onpausecall';
				segundos=data['callseg'];
				motivo='';
				tiempo=data['calldur'];
				detalle=data['queue'];
				break;
			case 4:
				tipo='outcall';
				segundos=data['callseg'];
				motivo='';
				tiempo=data['calldur'];
				detalle=data['queue'];
				break;		
			case 5:
				tipo='notLogged';
				segundos=data['availseg'];
				motivo='';
				tiempo=data['avail'];
				detalle='Tiempo Disponible';
				break;
		}
		
		if(data['PDV']==1){
			pdv = "<div class='pdv'>PDV</div>";
		}else{
			if(data['Departamento'].length>=12){
				departamento=data['Departamento'].substr(0,10)+"...";
			}else{
				departamento=data['Departamento'];
			}
			pdv="<div class='cc'>"+departamento+"</div>";
		}
		
		if(detalle.length>=20){
			detalle=detalle.substr(0,18)+"...";
		}
		
		agent_block="<div class='asesor "+tipo+"' status='"+data['status']+"'>"+pdv+"<div class='comida'>"+data['comida']+"</div><div class='extension'>"+data['ext']+"</div><div class='name'>"+data['asesor']+"</div><div class='calldetails' seg='"+segundos+"' mot='"+motivo+"'>"+tiempo+"</div><div class='callqueue'>"+detalle+"</div</div>";
		
		if(data['PDV']==1){
			$('#res_asesores_pdv').append(agent_block);
		}else{
			$('#res_asesores').append(agent_block);
		}
	}
	
	function printResumen(data){
		$('#res_online').text(data['online']);
		$('#res_avail').text(data['avail']);
		$('#res_paused').text(data['pause']);
		$('#res_waiting').text(data['waiting']);
		$('#res_inbound').text(data['inbound']);
		$('#res_outbound').text(data['outbound']);
		$('#res_aht').text(data['aht']).attr('seg',data['ahtseg']);
		$('#res_lwait').text(data['longestw']);

	}
	
	function printWaits(){
		$('#w_ventasMP').text(data['waits'][35]);
		$('#w_ventas').text(data['waits'][3]);
		$('#w_sac').text(data['waits'][4]);
		$('#w_upsell').text(data['waits'][5]);
		$('#w_tmp').text(data['waits'][9]);
		$('#w_tmt').text(data['waits'][8]);
		$('#w_agencias').text(data['waits'][7]);
    }

   setInterval(function(){
           sendRequest();
           formatBlocks();
       },3000);

   function sendRequest(){
   		$.ajax({
   			url: "qm_vars.php",
   			type: "GET",
   			data: {tipo: 'newRTMon', skill: '35'},
   			dataType: 'json',
   			success: function(array){
   						data=array;
   						
   						//Clear Contents
   						$('#res_asesores, #res_asesores_pdv').empty();
   						
						$.each(data['asesor'],function(index,value){
							printAsesor(value);
						});
						
						printResumen(data);
						printWaits();
						
						$('#LU').text(data['lu']);
						
						formatBlocks();
							
					}   						
   		});
	}
    
   function formatBlocks(){
   		if($('#res_waiting').text()>0){
		       if($('#res_waiting').text()>2){
		        $('#res_waiting').parent().css('background','#E8C44F');
		       }
		       if($('#res_waiting').text()>5){
		        $('#res_waiting').parent().css('background','#A80000');
		       }
		       if($('#res_waiting').text()<=2){
		        $('#res_waiting').parent().css('background','');
		       }
		   }
		   
		   if(parseInt($('#res_aht').attr('seg'))>600){
		       $('#res_aht').parent().addClass('flash');
		   }else{
		   		$('#res_aht').parent().removeClass('flash');
		   }
		
		   if($('#res_paused').text()>0){
		       if($('#res_paused').text()>3){
		        $('#res_paused').parent().css('background','#E8C44F');
		       }
		       if($('#res_paused').text()>5){
		        $('#res_paused').parent().css('background','#A80000');
		       }
		       if($('#res_paused').text()<=3){
		        $('#res_paused').parent().css('background','');
		       }
		   }
		
		   $('.incall').each(function(index){
		   		var seg=$(this).find('.calldetails').attr('seg');
		   		if(parseInt(seg)>780){
		   			$(this).find('.calldetails').addClass('flash');
		   		}
		   });

			$('.onpause').each(function(index){
		   		var seg=$(this).find('.calldetails').attr('seg');
		   		var mot=$(this).find('.calldetails').attr('mot');
		   		switch(mot){
		   			case "Comida":
		   				if(parseInt(seg)>1800){
		   					$(this).find('.calldetails').addClass('flash');
		   				}		
		   				break;
		   			case "Pausa No Productiva":
		   				if(parseInt(seg)>300){
		   					$(this).find('.calldetails').addClass('flash');
		   				}
		   				break;
		   			case "ACW":
		   				if(parseInt(seg)>120){
		   					$(this).find('.calldetails').addClass('flash');
		   				}
		   				break;
		   			case "Charla con Supervisor":
		   				if(parseInt(seg)>600){
		   					$(this).find('.calldetails').addClass('flash');
		   				}
		   				break;
		   				
		   		}
		   		
		   });

	
   }
   
   sendRequest();
   
});


</script>
<style>
body{
	zoom: .75;
}

.res_icon{
	position: relative;
	top: 4px;
	left: 26px;
}

.res_title{
	position: relative;
	top: 25px;
	margin: 0;
	text-align: center;
	font-size: 46px;
	color: white;
}

.w_detail{
	position: relative;
	top: 14px;
	margin: 0;
	text-align: center;
	font-size: 46px;
	color: white;
	text-shadow: 0 -1px 1px rgba(0,0,0,0.50);
}

.w_dep{
	position: relative;
	top: 25px;
	margin: 0;
	text-align: center;
	font-size: 20px;
	color: white;
	text-shadow: 0 -1px 1px rgba(0,0,0,0.50);
}

.res_detail{
	position: relative;
	top: -35px;
	left: 65px;
	width: 280;
	margin: 0;
	text-align: center;
	font-size: 60px;
	color: white;
	text-shadow: 0 -1px 1px rgba(0,0,0,0.50);
}

.fila{
    width: 80%;
    height: 300px;
    margin:20px;
}

.resumen{
    margin: auto;
    width: 1582px;
    
}

.container {
  float: left;
  width: 375px;
  margin: 10 10 20 0;
  height: 65px;
  background: #0094ff;
  border-radius: 35px;
  box-shadow:
    inset 0 7em 10em -5em rgba(255,255,255,0.6),
    0 0.3em 0.5em -0.2em rgba(100,100,100,1),
    0 1em 2em -0.75em rgba(100,100,100,0.75),
    0 1em 3em -0.5em rgba(100,100,100,0.5),
    0 3em 3em -0.25em rgba(100,100,100,0.2);
  
}

.waits {
  float: left;
  width: 216px;
  margin: 10 10 20 0;
  height: 65px;
  background: #CB9F10;
  border-radius: 35px;
  box-shadow:
    inset 0 7em 10em -5em rgba(255,255,255,0.6),
    0 0.3em 0.5em -0.2em rgba(100,100,100,1),
    0 1em 2em -0.75em rgba(100,100,100,0.75),
    0 1em 3em -0.5em rgba(100,100,100,0.5),
    0 3em 3em -0.25em rgba(100,100,100,0.2);
  
}

.asesor {
  display: inline-block;
  position: relative;
  width: 300px;
  height: 10em;
  background: blue;
  border-radius: 35px;
  box-shadow:
    inset 0 7em 10em -5em rgba(255,255,255,0.6),
    0 0.3em 0.5em -0.2em rgba(100,100,100,1),
    0 1em 2em -0.75em rgba(100,100,100,0.75),
    0 1em 3em -0.5em rgba(100,100,100,0.5),
    0 3em 3em -0.25em rgba(100,100,100,0.2);
  transform: translate(5%,-5%);
  margin: 19px 15px 0 0;
}

.pdv {
	position : absolute;
  top: -16;
  left: 8px;
  width: 133px;
  height: 29px;
  background: #ff91e3;
  border-radius: 35px 35px 0px 0px;
  box-shadow:
    inset 0 7em 10em -5em rgba(255,255,255,0.6),
    0 0.3em 0.5em -0.2em rgba(100,100,100,1),
    0 1em 2em -0.75em rgba(100,100,100,0.75),
    0 1em 3em -0.5em rgba(100,100,100,0.5),
    0 3em 3em -0.25em rgba(100,100,100,0.2);
  transform: translate(5%,-5%);
  margin: 19px 15px 0 0;
  padding-top: 3px;
  font-size: 16px;
  color: black;
  text-align: center;
}

.cc {
	position : absolute;
  top: -16;
  left: 8px;
  width: 133px;
  height: 29px;
  background: #0400b9;
  border-radius: 35px 35px 0px 0px;
  box-shadow:
    inset 0 7em 10em -5em rgba(255,255,255,0.6),
    0 0.3em 0.5em -0.2em rgba(100,100,100,1),
    0 1em 2em -0.75em rgba(100,100,100,0.75),
    0 1em 3em -0.5em rgba(100,100,100,0.5),
    0 3em 3em -0.25em rgba(100,100,100,0.2);
  transform: translate(5%,-5%);
  margin: 19px 15px 0 0;
  padding-top: 3px;
  font-size: 16px;
  color: white;
  text-align: center;
}

.comida {
	position : absolute;
  top: -16;
  left: 128px;
  width: 150px;
  height: 40px;
  border-radius: 35px 35px 0px 0px;
  transform: translate(5%,-5%);
  margin: 19px 15px 0 0;
  padding-top: 3px;
  font-size: 16px;
  color: black;
   text-align: center;
}

.name{
    position: relative;
    top: 14px;
    border-radius: 50px 50px 50px 50px;
    width: 100%;
    height:35;
    margin: 0;
    margin-top: 10;
    line-height: normal;
    text-align:center;
    padding-top: 1px;
    font-size: 27px;
    color: black;
    /* Mozilla Firefox */ 
	background-image: -moz-linear-gradient(left, #f6f8f9 0%, #f5f7f9 20%, #f5f7f9 51%, #f5f7f9 100%);
	/* Webkit (Chrome 11+) */ 
	background-image: -webkit-linear-gradient(left, #f6f8f9 0%, #f5f7f9 20%, #f5f7f9 51%, #f5f7f9 100%);
}

.extension{
    position: absolute;
    top: 51px;
    left: 185px;
    padding-top: 1px;
    border-radius: 0px 0px 50px 50px;
    width: 100px;
    height:28px;
    margin: 0;
    margin-top: 10;
    line-height: normal;
    text-align:center;
    font-size: 23px;
    color: white;
    background: #706ed7;
}

.calldetails{
    width: 100px;
    position: absolute;
    top: 66px;
    left: 25px;
    height: 20px;
    text-align:center;
    font-size: 21px;
    border-radius: 10px;
    
}

.callqueue{
	position: absolute;
	top: 90px;
	left: 34px;
    width: 200px;
    height: 20px;
    text-align:left;
    font-size: 21px;
    float:left;
    
}

.bottomT{
    width: 100%;
    height 30%;
    margin: 0;
    line-height: normal;
    text-align:center;
    padding-left: 14px;
    font-size: 30px;
    float:left;
}

.span{
    display:inline-block;
    vertical-align:middle
}

.titles{
    clear:left;
    text-align: center;
    font-size:65px;
    color: white;
    width: 100%;
    height: 30px;
    padding-top: 24px;
    padding-bottom: 22px;
    font-smoothing: antialiased;
    -webkit-font-smoothing: antialiased;
    -moz-font-smoothing: antialiased;
    -o-font-smoothing: antialiased;
    -ms-font-smoothing: antialiased;
    text-decoration: none;
    text-shadow: 0 -1px 1px rgba(0,0,0,0.50);
    border-radius: 3px #83DDEC;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    -o-border-radius: 3px;
    -ms-border-radius: 3px;
    border: 1px solid rgba(0,0,0,0.50);
    border-top: 1px solid rgba(0,0,0,0.001);
    box-shadow: 0 0 10px rgba(0,0,0,0.35), 0px 1px 3px rgba(0,0,0,0.18), inset 0px -3px 0px rgba(0,0,0,0.35), inset 0px 14px 14px rgba(255,255,255,0.10);
    -webkit-box-shadow: 0 0 10px rgba(0,0,0,0.35), 0px 1px 3px rgba(0,0,0,0.18), inset 0px -3px 0px rgba(0,0,0,0.35), inset 0px 14px 14px rgba(255,255,255,0.10);
    -moz-box-shadow: 0 0 10px rgba(0,0,0,0.35), 0px 1px 3px rgba(0,0,0,0.18), inset 0px -3px 0px rgba(0,0,0,0.35), inset 0px 14px 14px rgba(255,255,255,0.10);
    -o-box-shadow: 0 0 10px rgba(0,0,0,0.35), 0px 1px 3px rgba(0,0,0,0.18), inset 0px -3px 0px rgba(0,0,0,0.35), inset 0px 14px 14px rgba(255,255,255,0.10);
    -ms-box-shadow: 0 0 10px rgba(0,0,0,0.35), 0px 1px 3px rgba(0,0,0,0.18), inset 0px -3px 0px rgba(0,0,0,0.35), inset 0px 14px 14px rgba(255,255,255,0.10);text-decoration: none;
    text-shadow: 0 -1px 1px rgba(0,0,0,0.50);
    background: #009DCC
}

.incall{
	background: #4682B4;
	color: white;
}

.notLogged{
	background: #cc0000;
}

.outcall{
	background: #9BDDFF;
}

.avail{
	background: #9ECE08;
}

.onpause{
	background: #FF8C00;
	color: black;
}

.onpausecall{
	background: #B8860B;
	color: white;
}

.flash{
    -webkit-animation-name: flashing; /* Chrome, Safari, Opera */
    -webkit-animation-duration: 2s; /* Chrome, Safari, Opera */
    -webkit-animation-iteration-count: infinite; /* Chrome, Safari, Opera */
    -webkit-animation-direction: reverse; /* Chrome, Safari, Opera */

}

@-webkit-keyframes flashing {
    0%   {background-color:#750000; color: white;}
    50% {background-color:#D1BC00; color: black;}
    100% {background-color:#750000; color: white;}
}


</style>

<body style='background: white'>
<br>
<div class='titles'>VentasMP // <lu id='LU'>28-11-2016 16:20:48</lu></div>
<br>
<div class='resumen'>
    <div class='container'><div class='res_icon'><img src="/images/online.png" height="60" width="60"></div><div class='res_detail' id='res_online'></div></div>
    <div class='container'><div class='res_icon'><img src="/images/avail.png" height="60" width="60"></div><div class='res_detail' id='res_avail'></div></div>
    <div class='container'><div class='res_icon'><img src="/images/paused.png" height="60" width="60"></div><div class='res_detail' id='res_paused'></div></div>
    <div class='container'><div class='res_icon'><img src="/images/waiting.png" height="60" width="60"></div><div class='res_detail' id='res_waiting'></div></div>
    <div class='container'><div class='res_icon'><img src="/images/inbound.png" height="60" width="60"></div><div class='res_detail' id='res_inbound'></div></div>
    <div class='container'><div class='res_icon'><img src="/images/outbound.png" height="60" width="60"></div><div class='res_detail' id='res_outbound'></div></div>
    <div class='container'><div class='res_icon'><img src="/images/aht.png" height="60" width="60"></div><div class='res_detail' id='res_aht'></div></div>
	<div class='container'><div class='res_icon'><img src="/images/aht.png" height="60" width="60"></div><div class='res_detail' id='res_lwait'></div></div>
	
</div>
<br>
<div class='titles'>Espera en Colas</div>
<br>
<div class='resumen' id='res_esperas'>
	<div class='waits'><div class='w_detail' id='w_ventasMP'></div><div class='w_dep'>Ventas MP</div></div>
	<div class='waits'><div class='w_detail' id='w_ventas'></div><div class='w_dep'>Ventas</div></div>
	<div class='waits'><div class='w_detail' id='w_sac'></div><div class='w_dep'>SAC</div></div>
	<div class='waits'><div class='w_detail' id='w_upsell'></div><div class='w_dep'>Upsell</div></div>
	<div class='waits'><div class='w_detail' id='w_tmp'></div><div class='w_dep'>TMP</div></div>
	<div class='waits'><div class='w_detail' id='w_tmt'></div><div class='w_dep'>TMT</div></div>
	<div class='waits'><div class='w_detail' id='w_agencias'></div><div class='w_dep'>Agencias</div></div>
</div>
<br>
<div class='titles'>Status Asesores</div>
<br>
<div class='resumen_bloques' id='res_asesores'></div>
<div class='resumen_bloques' id='res_asesores_pdv'></div>




</body>
