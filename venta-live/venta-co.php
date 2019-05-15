<?php
include_once("../modules/modules.php");

initSettings::start(false);
initSettings::printTitle('KPIs del día');
?>

<style>
.bloque{
    height: 330;
    padding: 10;
    margin-top: 35px;
}

.bloque_short{
    height: 280;
    padding: 10;
    margin-top: 35px;
}

.title{
    /* float: left; */
    margin: auto;
    width: 100%;
    height: 40px;
    color: white;
    background: #215086;
    /* display: flex; */
    /* justify-content: right; */
    text-align: center;
    align-items: center;
    font-size:31;
    line-height: normal;
    font-family: Arial, Helvetica, sans-serif;
    font-weight: bold;
    font-style: normal;
    font-smoothing: antialiased;
    -webkit-font-smoothing: antialiased;
    -moz-font-smoothing: antialiased;
    -o-font-smoothing: antialiased;
    -ms-font-smoothing: antialiased;
    text-decoration: none;
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
    text-shadow: 1px 1px 2px black, 0 0 5px darkblue;
    /* padding: 0 15 0 0; */


}

.container{
    background: #779ECB;
    height: 100%;
    line-height: 100px;
    color: white;
    display: flex;
    justify-content: left;
    padding-left: 25;
    align-items: center;
    font-size: 75;
    font-family: Arial, Helvetica, sans-serif;
    font-weight: bold;
    font-style: normal;
    font-smoothing: antialiased;
    -webkit-font-smoothing: antialiased;
    -moz-font-smoothing: antialiased;
    -o-font-smoothing: antialiased;
    -ms-font-smoothing: antialiased;
    text-decoration: none;
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
    text-shadow: 1px 1px 2px black, 0 0 25px #AD2E4E, 0 0 5px darkblue;border-radius: 3px #83DDEC;
}

.pin{
    background: #AD2E4E;
}

.pus{
    background: #D35A78;
}

.ppdv{
    background: #E292A6;
}

.online{
    background: #efc2cd;
}

.cpin{
    background: #C68B9E;
    text-shadow: 1px 1px 2px black, 0 0 25px #AD2E4E, 0 0 5px darkblue;border-radius: 3px #83DDEC;
}

.cpus{
    background: #DD92A9;
    text-shadow: 1px 1px 2px black, 0 0 25px #AD2E4E, 0 0 5px darkblue;border-radius: 3px #83DDEC;
}

.cppdv{
    background: #E9BECA;
    text-shadow: 1px 1px 2px black, 0 0 25px #AD2E4E, 0 0 5px darkblue;border-radius: 3px #83DDEC;
}

.conline{
    background: #f2d9e0;
    text-shadow: 1px 1px 2px black, 0 0 25px #AD2E4E, 0 0 5px darkblue;border-radius: 3px #83DDEC;
}


.title p{
    background: #C7CC2C;
    vertical-align: middle;
    line-height: normal;
    margin: 10;

}

.upvar{
    color: #08EF08;
}

.downvar{
    color: #FF1100;
}

.container a{
    font-size:25;
    text-align: center;
    line-height: 1;
}

.container p  a aval{
    width: 100px;
}

.zoomout{
    width: 1020;
}

.header{
    background: #215086;
    height: 205;
    margin-bottom: -35px;
    line-height: 100px;
    color: white;
    text-align: center;
    font-size: 75;
    font-family: Arial, Helvetica, sans-serif;
    font-weight: bold;
    font-style: normal;
    font-smoothing: antialiased;
    -webkit-font-smoothing: antialiased;
    -moz-font-smoothing: antialiased;
    -o-font-smoothing: antialiased;
    -ms-font-smoothing: antialiased;
    text-decoration: none;
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
    text-shadow: 1px 1px 2px black, 0 0 25px #215086, 0 0 5px darkblue;
}

.dayView tr th, .chanView tr th{
  text-align: right;
}

</style>
<script>

$(function(){

  sendRequest();

  setInterval(function(){
           sendRequest();
       },60000);

  $( 'test' ).tooltip({
		items: "[title]",
		content: function() {
	        var element = $( this );
	        if ( element.is( "[title]" ) ) {
	          var tipo = element.attr('tipo');
	          var camp = element.attr('camp');
	          return showMore(tipo,camp);
	    	}
		}

    });

  function getVal(data,canal,chanel,servicio,tipo,date,op,comp,av){
  	var operacion = (typeof op === 'undefined') ? 'total' : op;
  	var varAv = (typeof av === 'undefined') ? false : true;
  	var compara = (typeof comp === 'undefined' || comp == 0) ? 'Td' : comp;
  	var valor;
  	var datothis = (typeof data[canal][chanel][servicio][date][tipo] === 'undefined') ? 0 : data[canal][chanel][servicio][date][tipo];
  	var datocomp = (typeof data[canal][chanel][servicio][compara][tipo] === 'undefined') ? 0 : data[canal][chanel][servicio][compara][tipo];

  	switch(operacion){
  		case 'total':
  			valor=datothis;
  			break;
  		case 'var':
  			if(varAv){
  				var datolocs = (typeof data[canal][chanel][servicio][date]['loc'] === 'undefined') ? 0 : data[canal][chanel][servicio][date]['loc'];
  				var datolocsTd = (typeof data[canal][chanel][servicio]['Td']['loc'] === 'undefined') ? 0 : data[canal][chanel][servicio]['Td']['loc'];
  				var tdav = (data[canal][chanel][servicio]['Td'][tipo]/datolocsTd);
  				var thisav= datothis/datolocs;
  				valor = tdav/thisav*100-100;
  			}else{
  				valor=datothis/datocomp*100-100;
  			}
  			break
  		case 'av':
  			var datolocs = (typeof data[canal][chanel][servicio][date]['loc'] === 'undefined') ? 0 : data[canal][chanel][servicio][date]['loc'];
  			valor=datothis/datolocs;
  			break
  	}

    var resultCell;

  	switch(tipo){
  		case 'monto':
      case 'Monto':
  		case 'venta':
  		case 'xld':
  		case 'fc':
      case 'Hotel':
  		case 'Paquete':
  		case 'Vuelo':
  			if(operacion=='var'){
          resultCell = number_format(valor,2);
        }else{
          resultCell = number_format(valor*153.235/1000,0)+"K";
        }
  			break;
  		default:
  			if(operacion=='var'){
  				resultCell = number_format(valor.toPrecision(2),2);
  			}else{
  			     resultCell = valor;
        }
  			break;

  	}

    switch(servicio){
      case 'Todo':
        return resultCell;
        break;
      default:
        if(operacion=='var'){
          return resultCell;
        }else{
          if(servicio=='Base' || tipo=='FCS'){
            if(tipo=='FCS'){
              return number_format(resultCell.toPrecision(2),2);
            }else{
              return resultCell;
            }
          }else{
            if(canal=='us'){
              return resultCell+' ('+ data[canal][chanel][servicio][date]['Locs'] +' / '+data['us'][chanel]['BaseDesg'][date]['Base'][servicio]+')';
            }else{
              return resultCell+' ('+ data[canal][chanel][servicio][date]['Locs'] +')';
            }
          }
        }
        break;
    }

  }

  function nC(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

   function number_format (number, decimals, decPoint, thousandsSep) {
   	  var number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
	  var n = !isFinite(+number) ? 0 : +number
	  var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
	  var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
	  var dec = (typeof decPoint === 'undefined') ? '.' : decPoint
	  var s = ''

	  var toFixedFix = function (n, prec) {
	    var k = Math.pow(10, prec)
	    return '' + (Math.round(n * k) / k)
	      .toFixed(prec)
	  }

	  // @todo: for IE parseFloat(0.55).toFixed(0) = 0;
	  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.')
	  if (s[0].length > 3) {
	    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
	  }
	  if ((s[1] || '').length < prec) {
	    s[1] = s[1] || ''
	    s[1] += new Array(prec - s[1].length + 1).join('0')
	  }

	  return s.join(dec)
	}

   function sendRequest(){
        $.ajax({
            url: "__query_venta_v2.php",
            type: 'POST',
            data: {pais: 'CO'},
            dataType: 'json', // will automatically convert array to JavaScript
            success: function(array) {
                data=array;

                //LU
                $('#LU').text(data['lu']);


                //Dynamic Fields
                $('.dynamic').each(function(){
                	var element=$(this);
                	var canal = (typeof element.attr('canal') === 'undefined') ? element.closest('div').attr('canal') : element.attr('canal');
                  var chanel = (typeof element.attr('chan') === 'undefined') ? 'Total' : element.attr('chan');
                  var servicio = (typeof element.closest('tr').attr('servicio') === 'undefined') ? 'Todo' : element.closest('tr').attr('servicio');
                	var tipo=element.attr('tipo');
                	var date=element.attr('date');
                	var op=element.attr('op');
                	var compare=element.attr('compare');

                	try{
                		if($(this).closest('tr').attr('tipo')=='av'){
                			element.attr('valor',getVal(data,canal,chanel,servicio,tipo,date,op,compare,true));
	                		element.text(getVal(data,canal,chanel,servicio,tipo,date,op,compare,true));
                		}else{
                			element.attr('valor',getVal(data,canal,chanel,servicio,tipo,date,op,compare));
	                		element.text(getVal(data,canal,chanel,servicio,tipo,date,op,compare));
                		}

	                }
	                catch(err){
	                	element.attr('valor',0);
                    if(canal=='us' && (servicio=='Hotel' || servicio=='Paquete' || servicio=='Vuelo')){
                      try{
                        var base = (typeof data['us'][chanel]['BaseDesg'][date]['Base'][servicio] === 'undefined') ? 0 : data['us'][chanel]['BaseDesg'][date]['Base'][servicio];
                      }

                      catch(err){
                        var base=0;
                      }
                      element.text('0 (0 / '+base+')');
                    }else{
                	    element.text(0);
                    }
	                }

                	//Format Fields
                	if(element.hasClass('var')){
                		element.removeClass('upvar');
                		element.removeClass('downvar');
                		if(element.attr('valor')>=10){
                			element.addClass('upvar');
                		}else if(element.attr('valor')<=-10){
                			element.addClass('downvar');
                		}
                	}
                });





           }

        });
    }

    $('#zoom').click(function(){
        if($(this).attr('status')==0){
            $('.header, .bloque, .bloque_short').addClass('zoomout');
            $('body').css('zoom','0.3');
            $(this).attr('status','1');
        }else{
            $('.header, .bloque, .bloque_short').removeClass('zoomout');
            $('body').css('zoom','1');
            $(this).attr('status','0');
        }

    });

    flagView=true;
    $('.dayView').show();
    $('.chanView').hide();

    $('#chgView').click(function(){
        if(flagView){
          $('.dayView').hide();
          $('.chanView').show();
          flagView=false;
          $('#chgView').text('Ver info por Día');
        }else{
          $('.dayView').show();
          $('.chanView').hide();
          flagView=true;
          $('#chgView').text('Ver info por Canal');
        }
    });
});
</script>

<div class='header'>
KPIS Venta<br>
<aval chan='Total' id='LU'></aval>
</div>

<div class='header' style= 'height: 25px; margin-top: 40px; font-size: 30px; line-height: 0; padding-top: 22px; background: #54c2ec; cursor: hand' id='chgView'>Ver info por Canal</div>

<?php

function printBloque($channel, $title, $class1, $class2, $inbound, $base, $ch){

  $chans=explode('|',$ch);

  echo "<div class='bloque'>
      <div class='title' style='background: $class1'>$title</div>
      <div class='container' style='background: $class2' canal='$channel'>
      	<div style='clear: both; width: 100%' canal='$channel'>
  	    	<table style='width: 90%; margin: auto;'><tr><th style='font-size: 74px; color: white;'>$<aval chan='Total' class='dynamic' tipo='monto' date='Td' op='total' compare='0'></aval></th></tr></table>
  	        <table class='dayView' style='width: 90%; margin: auto; color: white; text-align: right'>
  	        	<tr style='text-align: center'>
  	        		<th>KPI</th>
  	        		<th>Td</th>
  	        		<th>Yd</th>
  	        		<th>Var% Yd</th>
  	        		<th>LW</th>
  	        		<th>Var% LW</th>
  	        	</tr>
  	        	<tr style='color: red'>
  	        		<td>Xld</td>
  	        		<td>$<aval chan='Total' class='dynamic' tipo='xld' date='Td' op='total' compare='0'></aval></td>
  	        		<td>$<aval chan='Total' class='dynamic' tipo='xld' date='Y' op='total' compare='0'></aval></td>
  	        		<td><aval chan='Total' class='dynamic' tipo='xld' date='Td' op='var' compare='Y'></aval>%</td>
  	        		<td>$<aval chan='Total' class='dynamic' tipo='xld' date='LW' op='total' compare='0'></aval></td>
  	        		<td><aval chan='Total' class='dynamic' tipo='xld' date='Td' op='var' compare='LW'></aval>%</td>
  	        	</tr>
  	        	<tr>
  	        		<td>Venta</td>
  	        		<td>$<aval chan='Total' class='dynamic' tipo='venta' date='Td' op='total' compare='0'></aval></td>
  	        		<td>$<aval chan='Total' class='dynamic' tipo='venta' date='Y' op='total' compare='0'></aval></td>
  	        		<td><aval chan='Total' class='dynamic var' tipo='venta' date='Td' op='var' compare='Y'></aval>%</td>
  	        		<td>$<aval chan='Total' class='dynamic' tipo='venta' date='LW' op='total' compare='0'></aval></td>
  	        		<td><aval chan='Total' class='dynamic var' tipo='venta' date='Td' op='var' compare='LW'></aval>%</td>
  	        	</tr>
  	        	<tr tipo='av'>
  	        		<td tipo='av'>Avg Tkt</td>
  	        		<td>$<aval chan='Total' class='dynamic' tipo='venta' date='Td' op='av' compare='0'></aval></td>
  	        		<td>$<aval chan='Total' class='dynamic' tipo='venta' date='Y' op='av' compare='0'></aval></td>
  	        		<td><aval chan='Total' class='dynamic var' tipo='venta' date='Y' op='var' compare='Y'></aval>%</td>
  	        		<td>$<aval chan='Total' class='dynamic' tipo='venta' date='LW' op='av' compare='0'></aval></td>
  	        		<td><aval chan='Total' class='dynamic var' tipo='venta' date='LW' op='var' compare='LW'></aval>%</td>
  	        	</tr>
  	        	<tr>
  	        		<td>Locs</td>
  	        		<td><aval chan='Total' class='dynamic' tipo='loc' date='Td' op='total' compare='0'></aval></td>
  	        		<td><aval chan='Total' class='dynamic' tipo='loc' date='Y' op='total' compare='0'></aval></td>
  	        		<td><aval chan='Total' class='dynamic var' tipo='loc' date='Td' op='var' compare='Y'></aval>%</td>
  	        		<td><aval chan='Total' class='dynamic' tipo='loc' date='LW' op='total' compare='0'></aval></td>
  	        		<td><aval chan='Total' class='dynamic var' tipo='loc' date='Td' op='var' compare='LW'></aval>%</td>
  	        	</tr>";

    if($inbound==1){
        echo "<tr>
  	        		<td>FC %</td>
  	        		<td><aval chan='Total' class='dynamic' tipo='fc' date='Td' op='total' compare='0'></aval>%</td>
  	        		<td><aval chan='Total' class='dynamic' tipo='fc' date='Y' op='total' compare='0'></aval>%</td>
  	        		<td><aval chan='Total' class='dynamic var' tipo='fc' date='Td' op='var' compare='Y'></aval>%</td>
  	        		<td><aval chan='Total' class='dynamic' tipo='fc' date='LW' op='total' compare='0'></aval>%</td>
  	        		<td><aval chan='Total' class='dynamic var' tipo='fc' date='Td' op='var' compare='LW'></aval>%</td>
  	        	</tr>
  	        	<tr>
  	        		<td>Calls</td>
  	        		<td><aval chan='Total' class='dynamic' tipo='callstotal' date='Td' op='total' compare='0'></aval></td>
  	        		<td><aval chan='Total' class='dynamic' tipo='callstotal' date='Y' op='total' compare='0'></aval></td>
  	        		<td><aval chan='Total' class='dynamic var' tipo='callstotal' date='Td' op='var' compare='Y'></aval>%</td>
  	        		<td><aval chan='Total' class='dynamic' tipo='callstotal' date='LW' op='total' compare='0'></aval></td>
  	        		<td><aval chan='Total' class='dynamic var' tipo='callstotal' date='Td' op='var' compare='LW'></aval>%</td>
  	        	</tr>";
    }
      echo "<tr servicio='Hotel'>
  	        		<td>Hotel</td>
  	        		<td>$<aval chan='Total' class='dynamic' tipo='Monto' date='Td' op='total' compare='0'></aval></td>
  	        		<td>$<aval chan='Total' class='dynamic' tipo='Monto' date='Y' op='total' compare='0'></aval></td>
  	        		<td><aval chan='Total' class='dynamic var' tipo='Monto' date='Td' op='var' compare='Y'></aval>%</td>
  	        		<td>$<aval chan='Total' class='dynamic' tipo='Monto' date='LW' op='total' compare='0'></aval></td>
  	        		<td><aval chan='Total' class='dynamic var' tipo='Monto' date='Td' op='var' compare='LW'></aval>%</td>
  	        	</tr>
  	        	<tr servicio='Vuelo'>
  	        		<td>Vuelo</td>
  	        		<td>$<aval chan='Total' class='dynamic' tipo='Monto' date='Td' op='total' compare='0'></aval></td>
  	        		<td>$<aval chan='Total' class='dynamic' tipo='Monto' date='Y' op='total' compare='0'></aval></td>
  	        		<td><aval chan='Total' class='dynamic var' tipo='Monto' date='Td' op='var' compare='Y'></aval>%</td>
  	        		<td>$<aval chan='Total' class='dynamic' tipo='Monto' date='LW' op='total' compare='0'></aval></td>
  	        		<td><aval chan='Total' class='dynamic var' tipo='Monto' date='Td' op='var' compare='LW'></aval>%</td>
  	        	</tr>
  	        	<tr servicio='Paquete'>
  	        		<td>Paquete</td>
  	        		<td>$<aval chan='Total' class='dynamic' tipo='Monto' date='Td' op='total' compare='0'></aval></td>
  	        		<td>$<aval chan='Total' class='dynamic' tipo='Monto' date='Y' op='total' compare='0'></aval></td>
  	        		<td><aval chan='Total' class='dynamic var' tipo='Monto' date='Td' op='var' compare='Y'></aval>%</td>
  	        		<td>$<aval chan='Total' class='dynamic' tipo='Monto' date='LW' op='total' compare='0'></aval></td>
  	        		<td><aval chan='Total' class='dynamic var' tipo='Monto' date='Td' op='var' compare='LW'></aval>%</td>
  	        	</tr>";
      if($base==1){
        echo "<tr servicio='Base'>
          <td>Base</td>
          <td><aval chan='Total' class='dynamic' tipo='Base' date='Td' op='total' compare='0'></aval></td>
          <td><aval chan='Total' class='dynamic' tipo='Base' date='Y' op='total' compare='0'></aval></td>
          <td><aval chan='Total' class='dynamic var' tipo='Base' date='Td' op='var' compare='Y'></aval>%</td>
          <td><aval chan='Total' class='dynamic' tipo='Base' date='LW' op='total' compare='0'></aval></td>
          <td><aval chan='Total' class='dynamic var' tipo='Base' date='Td' op='var' compare='LW'></aval>%</td>
        </tr>
        <tr servicio='Base'>
          <td>FC</td>
          <td><aval chan='Total' class='dynamic' tipo='FCS' date='Td' op='total' compare='0'></aval>%</td>
          <td><aval chan='Total' class='dynamic' tipo='FCS' date='Y' op='total' compare='0'></aval>%</td>
          <td><aval chan='Total' class='dynamic var' tipo='FCS' date='Td' op='var' compare='Y'></aval>%</td>
          <td><aval chan='Total' class='dynamic' tipo='FCS' date='LW' op='total' compare='0'></aval>%</td>
          <td><aval chan='Total' class='dynamic var' tipo='FCS' date='Td' op='var' compare='LW'></aval>%</td>
        </tr>";
      }
      echo "</table>";
      
      if(count($chans)>0){
            echo "<table class='chanView' style='width: 90%; margin: auto; color: white; text-align: right'>
  	        	<tr style='text-align: center'>
  	        		<th>KPI</th>";
  	        foreach($chans as $id => $info){
              echo "<th>$info</th>";
  	        }
  	        echo "</tr>
  	        	<tr style='color: red'>
  	        		<td>Xld</td>";
  	        foreach($chans as $id => $info){
              echo "<td>$<aval chan='$info' class='dynamic' tipo='xld' date='Td' op='total' compare='0'></aval></td>";
  	        }
  	        echo "</tr>
  	        	<tr>
  	        		<td>Venta</td>";
  	        foreach($chans as $id => $info){
              echo "<td>$<aval chan='$info' class='dynamic' tipo='venta' date='Td' op='total' compare='0'></aval></td>";
  	        }
  	        echo "</tr>
  	        	<tr tipo='av'>
  	        		<td tipo='av'>Avg Tkt</td>";
  	        foreach($chans as $id => $info){
              echo "<td>$<aval chan='$info' class='dynamic' tipo='monto' date='Td' op='av' compare='0'></aval></td>";
  	        }
  	        echo "</tr>
  	        	<tr>
  	        		<td>Locs</td>";
  	        foreach($chans as $id => $info){
              echo "<td>$<aval chan='$info' class='dynamic' tipo='loc' date='Td' op='total' compare='0'></aval></td>";
  	        }
  	        echo "</tr>
  	        	<tr servicio='Hotel'>
  	        		<td>Hotel</td>";
  	        foreach($chans as $id => $info){
              echo "<td>$<aval chan='$info' class='dynamic' tipo='Monto' date='Td' op='total' compare='0'></aval></td>";
  	        }
  	        echo "</tr>
  	        	<tr servicio='Vuelo'>
  	        		<td>Vuelo</td>";
  	        foreach($chans as $id => $info){
              echo "<td>$<aval chan='$info' class='dynamic' tipo='Monto' date='Td' op='total' compare='0'></aval></td>";
  	        }
  	        echo "</tr>
  	        	<tr servicio='Paquete'>
  	        		<td>Paquete</td>";
  	        foreach($chans as $id => $info){
              echo "<td>$<aval chan='$info' class='dynamic' tipo='Monto' date='Td' op='total' compare='0'></aval></td>";
  	        }
  	        echo "</tr></table>";
  	  }
  		echo "</div>
      </div>
  </div>";
}

$connectdb=Connection::mysqliDB('CC');
$query="SELECT * FROM monitor_kpiLive_modules WHERE activo=1 AND pais='CO' AND CURDATE() BETWEEN inicio AND fin ORDER BY `order`";
if($result=$connectdb->query($query)){
  while($fila=$result->fetch_assoc()){
    printBloque($fila['canal'],$fila['titulo'],$fila['class1'],$fila['class2'],$fila['inbound'],$fila['base'],$fila['ch']);
  }
}else{
  echo "ERROR! -> ".$connectdb->error." ON $query";
}
$connectdb->close();

?>

<button class='button button_blue_w' id='zoom' status='0'>Zoom</button>
