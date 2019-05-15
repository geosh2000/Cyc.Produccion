<?php
include_once("../modules/modules.php");

initSettings::start(false);

?>

<style>
.bloque{
    height: 360;
    padding: 10;

}

.bloque_short{
    height: 280;
    padding: 10;

}

.title{
    float: left;
    margin: 0;
    width: 35%;
    height: 100%;
    color: white;
    background: #215086;
    display: flex;
    justify-content: right;
    text-align: right;
    align-items: center;
    font-size:75;
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
    padding: 0 15 0 0;


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
    text-shadow: 1px 1px 2px black, 0 0 25px #215086, 0 0 5px darkblue;

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
    margin-bottom: 0px;
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
       
  function getVal(data,canal,tipo,date,op,comp,av){
  	var operacion = (typeof op === 'undefined') ? 'total' : op;
  	var varAv = (typeof av === 'undefined') ? false : true;
  	var compara = (typeof comp === 'undefined' || comp == 0) ? 'Td' : comp;
  	var valor;
  	var datothis = (typeof data[canal][tipo][date] === 'undefined') ? 0 : data[canal][tipo][date];
  	var datocomp = (typeof data[canal][tipo][compara] === 'undefined') ? 0 : data[canal][tipo][compara];
  	
  	switch(operacion){
  		case 'total':
  			valor=datothis;
  			break;
  		case 'var':
  			if(varAv){
  				var datolocs = (typeof data[canal]['loc'][date] === 'undefined') ? 0 : data[canal]['loc'][date];
  				var datolocsTd = (typeof data[canal]['loc']['Td'] === 'undefined') ? 0 : data[canal]['loc']['Td'];
  				var tdav = (data[canal][tipo]['Td']/datolocsTd);
  				var thisav= datothis/datolocs;
  				valor = tdav/thisav*100-100;
  			}else{
  				valor=datothis/datocomp*100-100;
  			}
  			break
  		case 'av':
  			var datolocs = (typeof data[canal]['loc'][date] === 'undefined') ? 0 : data[canal]['loc'][date];
  			valor=datothis/datolocs;
  			break
  	}
  	
  	switch(tipo){
  		case 'monto':
  		case 'venta':
  		case 'xld':
  		case 'fc':
  		case 'Hotel':
  		case 'Paquete':
  		case 'Vuelo':
  			return number_format(valor,2);
  			break;
  		default:
  			if(operacion=='var'){
  				return number_format(valor,2);
  			}
  			return valor;
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
            type: 'GET',
            dataType: 'json', // will automatically convert array to JavaScript
            success: function(array) {
                data=array;
                
                //LU
                $('#LU').text(data['lu']);
                
                
                //Dynamic Fields
                $('.dynamic').each(function(){
                	var element=$(this);
                	var canal = (typeof element.attr('canal') === 'undefined') ? element.closest('div').attr('canal') : element.attr('canal');
                	var tipo=element.attr('tipo');
                	var date=element.attr('date');
                	var op=element.attr('op');
                	var compare=element.attr('compare');
                	
                	try{
                		if($(this).closest('tr').attr('tipo')=='av'){
                			element.attr('valor',getVal(data,canal,tipo,date,op,compare,true));
	                		element.text(getVal(data,canal,tipo,date,op,compare,true));
                		}else{
                			element.attr('valor',getVal(data,canal,tipo,date,op,compare));
	                		element.text(getVal(data,canal,tipo,date,op,compare));
                		}
	                	
	                }
	                catch(err){
	                	element.attr('valor',0);
	                	element.text(0);	
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

    })
});
</script>

<div class='header'>
KPIS Venta<br>
<aval id='LU'></aval>
</div>

<div class='bloque'>
    <div class='title pin'>Inbound</div>
    <div class='container cpin' canal='ibMP'>
    	<div style='clear: both; width: 100%' canal='ibMP'>
	    	<table style='width: 95%; margin: auto;'><tr><th style='font-size: 74px; color: white;'>$<aval class='dynamic' tipo='monto' date='Td' op='total' compare='0'></aval></th></tr></table>
	        <table style='width: 95%; margin: auto; color: white; text-align: right'>
	        	<tr style='text-align: center'>
	        		<th>KPI</th>
	        		<th>Td</th>
	        		<th>Yd</th>
	        		<th>Var% Yd</th>
	        		<th>LW</th>
	        		<th>Var% LW</th>
	        	</tr>
	        	<tr style='color: red'>
	        		<td style='text-align: left'>Xld</td>
	        		<td>$<aval class='dynamic' tipo='xld' date='Td' op='total' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='xld' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='xld' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='xld' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='xld' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>Venta</td>
	        		<td>$<aval class='dynamic' tipo='monto' date='Td' op='total' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='monto' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='monto' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='monto' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='monto' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>CC</td>
	        		<td>$<aval class='dynamic' canal='ibMPCC' tipo='monto' date='Td' op='total' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' canal='ibMPCC' tipo='monto' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' canal='ibMPCC' tipo='monto' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' canal='ibMPCC' tipo='monto' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' canal='ibMPCC' tipo='monto' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>Otros</td>
	        		<td>$<aval class='dynamic' canal='ibMPPDV' tipo='monto' date='Td' op='total' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' canal='ibMPPDV' tipo='monto' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' canal='ibMPPDV' tipo='monto' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' canal='ibMPPDV' tipo='monto' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' canal='ibMPPDV' tipo='monto' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr tipo='av'>
	        		<td style='text-align: left' tipo='av'>Avg Tkt</td>
	        		<td>$<aval class='dynamic' tipo='monto' date='Td' op='av' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='monto' date='Y' op='av' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='monto' date='Y' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='monto' date='LW' op='av' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='monto' date='LW' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>Locs</td>
	        		<td><aval class='dynamic' tipo='loc' date='Td' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic' tipo='loc' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='loc' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td><aval class='dynamic' tipo='loc' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='loc' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>FC %</td>
	        		<td><aval class='dynamic' tipo='fc' date='Td' op='total' compare='0'></aval>%</td>
	        		<td><aval class='dynamic' tipo='fc' date='Y' op='total' compare='0'></aval>%</td>
	        		<td><aval class='dynamic var' tipo='fc' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td><aval class='dynamic' tipo='fc' date='LW' op='total' compare='0'></aval>%</td>
	        		<td><aval class='dynamic var' tipo='fc' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>Calls</td>
	        		<td><aval class='dynamic' tipo='callstotal' date='Td' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic' tipo='callstotal' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='callstotal' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td><aval class='dynamic' tipo='callstotal' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='callstotal' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>Hotel</td>
	        		<td>$<aval class='dynamic' tipo='Hotel' date='Td' op='total' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='Hotel' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Hotel' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='Hotel' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Hotel' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>Vuelo</td>
	        		<td>$<aval class='dynamic' tipo='Vuelo' date='Td' op='total' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='Vuelo' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Vuelo' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='Vuelo' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Vuelo' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>Paquete</td>
	        		<td>$<aval class='dynamic' tipo='Paquete' date='Td' op='total' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='Paquete' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Paquete' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='Paquete' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Paquete' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        </table> 
		</div>
    </div>
</div>
<div class='bloque_short' style='height: 335px'>
    <div class='title pus'>Upsell</div>
    <div class='container cpus' canal='us'>
    	<div style='clear: both; width: 100%' canal='us'>
	    	<table style='width: 95%; margin: auto;'><tr><th style='font-size: 74px; color: white;'>$<aval class='dynamic' tipo='monto' date='Td' op='total' compare='0'></aval></th></tr></table>
	        <table style='width: 95%; margin: auto; color: white; text-align: right'>
	        	<tr style='text-align: center'>
	        		<th>KPI</th>
	        		<th>Td</th>
	        		<th>Yd</th>
	        		<th>Var% Yd</th>
	        		<th>LW</th>
	        		<th>Var% LW</th>
	        	</tr>
	        	<tr style='color: red'>
	        		<td style='text-align: left'>Xld</td>
	        		<td>$<aval class='dynamic' tipo='xld' date='Td' op='total' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='xld' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='xld' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='xld' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='xld' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>Venta</td>
	        		<td>$<aval class='dynamic' tipo='monto' date='Td' op='total' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='monto' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='monto' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='monto' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='monto' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>CC</td>
	        		<td>$<aval class='dynamic' canal='usCC' tipo='monto' date='Td' op='total' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' canal='usCC' tipo='monto' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' canal='usCC' tipo='monto' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' canal='usCC' tipo='monto' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' canal='usCC' tipo='monto' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>Otros</td>
	        		<td>$<aval class='dynamic' canal='usPDV' tipo='monto' date='Td' op='total' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' canal='usPDV' tipo='monto' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' canal='usPDV' tipo='monto' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' canal='usPDV' tipo='monto' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' canal='usPDV' tipo='monto' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr tipo='av'>
	        		<td style='text-align: left' tipo='av'>Avg Tkt</td>
	        		<td>$<aval class='dynamic' tipo='monto' date='Td' op='av' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='monto' date='Y' op='av' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='monto' date='Y' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='monto' date='LW' op='av' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='monto' date='LW' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>Locs</td>
	        		<td><aval class='dynamic' tipo='loc' date='Td' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic' tipo='loc' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='loc' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td><aval class='dynamic' tipo='loc' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='loc' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>Hotel</td>
	        		<td>$<aval class='dynamic' tipo='Hotel' date='Td' op='total' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='Hotel' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Hotel' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='Hotel' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Hotel' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>Vuelo</td>
	        		<td>$<aval class='dynamic' tipo='Vuelo' date='Td' op='total' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='Vuelo' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Vuelo' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='Vuelo' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Vuelo' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>Paquete</td>
	        		<td>$<aval class='dynamic' tipo='Paquete' date='Td' op='total' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='Paquete' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Paquete' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='Paquete' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Paquete' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        </table> 
		</div>
	</div>
</div>
<div class='bloque_short'>
    <div class='title ppdv'>PDV</div>
    <div class='container cppdv' canal='PDV'>
		<div style='clear: both; width: 100%' canal='PDV'>
	    	<table style='width: 95%; margin: auto;'><tr><th style='font-size: 74px; color: white;'>$<aval class='dynamic' tipo='monto' date='Td' op='total' compare='0'></aval></th></tr></table>
	        <table style='width: 95%; margin: auto; color: white; text-align: right'>
	        	<tr style='text-align: center'>
	        		<th>KPI</th>
	        		<th>Td</th>
	        		<th>Yd</th>
	        		<th>Var% Yd</th>
	        		<th>LW</th>
	        		<th>Var% LW</th>
	        	</tr>
	        	<tr style='color: red'>
	        		<td style='text-align: left'>Xld</td>
	        		<td>$<aval class='dynamic' tipo='xld' date='Td' op='total' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='xld' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='xld' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='xld' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='xld' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>Venta</td>
	        		<td>$<aval class='dynamic' tipo='monto' date='Td' op='total' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='monto' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='monto' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='monto' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='monto' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr tipo='av'>
	        		<td style='text-align: left' tipo='av'>Avg Tkt</td>
	        		<td>$<aval class='dynamic' tipo='monto' date='Td' op='av' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='monto' date='Y' op='av' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='monto' date='Y' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='monto' date='LW' op='av' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='monto' date='LW' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>Locs</td>
	        		<td><aval class='dynamic' tipo='loc' date='Td' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic' tipo='loc' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='loc' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td><aval class='dynamic' tipo='loc' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='loc' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>Hotel</td>
	        		<td>$<aval class='dynamic' tipo='Hotel' date='Td' op='total' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='Hotel' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Hotel' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='Hotel' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Hotel' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>Vuelo</td>
	        		<td>$<aval class='dynamic' tipo='Vuelo' date='Td' op='total' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='Vuelo' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Vuelo' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='Vuelo' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Vuelo' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>Paquete</td>
	        		<td>$<aval class='dynamic' tipo='Paquete' date='Td' op='total' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='Paquete' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Paquete' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='Paquete' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Paquete' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        </table> 
		</div>
    </div>
</div>
<div class='bloque_short'>
    <div class='title online'>Online</div>
    <div class='container conline' canal='ol'>
    	<div style='clear: both; width: 100%' canal='ol'>
	    	<table style='width: 95%; margin: auto;'><tr><th style='font-size: 74px; color: white;'>$<aval class='dynamic' tipo='monto' date='Td' op='total' compare='0'></aval></th></tr></table>
	        <table style='width: 95%; margin: auto; color: white; text-align: right'>
	        	<tr style='text-align: center'>
	        		<th>KPI</th>
	        		<th>Td</th>
	        		<th>Yd</th>
	        		<th>Var% Yd</th>
	        		<th>LW</th>
	        		<th>Var% LW</th>
	        	</tr>
	        	<tr style='color: red'>
	        		<td style='text-align: left'>Xld</td>
	        		<td>$<aval class='dynamic' tipo='xld' date='Td' op='total' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='xld' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='xld' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='xld' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='xld' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>Venta</td>
	        		<td>$<aval class='dynamic' tipo='monto' date='Td' op='total' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='monto' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='monto' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='monto' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='monto' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr tipo='av'>
	        		<td style='text-align: left' tipo='av'>Avg Tkt</td>
	        		<td>$<aval class='dynamic' tipo='monto' date='Td' op='av' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='monto' date='Y' op='av' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='monto' date='Y' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='monto' date='LW' op='av' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='monto' date='LW' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>Locs</td>
	        		<td><aval class='dynamic' tipo='loc' date='Td' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic' tipo='loc' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='loc' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td><aval class='dynamic' tipo='loc' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='loc' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>Hotel</td>
	        		<td>$<aval class='dynamic' tipo='Hotel' date='Td' op='total' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='Hotel' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Hotel' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='Hotel' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Hotel' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>Vuelo</td>
	        		<td>$<aval class='dynamic' tipo='Vuelo' date='Td' op='total' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='Vuelo' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Vuelo' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='Vuelo' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Vuelo' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>Paquete</td>
	        		<td>$<aval class='dynamic' tipo='Paquete' date='Td' op='total' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='Paquete' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Paquete' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='Paquete' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Paquete' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        </table> 
		</div>
    </div>
</div>
<div class='bloque'>
    <div class='title'>Inbound MT</div>
    <div class='container' canal='ibMT'>
		<div style='clear: both; width: 100%' canal='ibMT'>
	    	<table style='width: 95%; margin: auto;'><tr><th style='font-size: 74px; color: white;'>$<aval class='dynamic' tipo='monto' date='Td' op='total' compare='0'></aval></th></tr></table>
	        <table style='width: 95%; margin: auto; color: white; text-align: right'>
	        	<tr style='text-align: center'>
	        		<th>KPI</th>
	        		<th>Td</th>
	        		<th>Yd</th>
	        		<th>Var% Yd</th>
	        		<th>LW</th>
	        		<th>Var% LW</th>
	        	</tr>
	        	<tr style='color: red'>
	        		<td style='text-align: left'>Xld</td>
	        		<td>$<aval class='dynamic' tipo='xld' date='Td' op='total' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='xld' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='xld' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='xld' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='xld' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>Venta</td>
	        		<td>$<aval class='dynamic' tipo='monto' date='Td' op='total' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='monto' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='monto' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='monto' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='monto' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr tipo='av'>
	        		<td style='text-align: left' tipo='av'>Avg Tkt</td>
	        		<td>$<aval class='dynamic' tipo='monto' date='Td' op='av' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='monto' date='Y' op='av' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='monto' date='Y' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='monto' date='LW' op='av' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='monto' date='LW' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>Locs</td>
	        		<td><aval class='dynamic' tipo='loc' date='Td' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic' tipo='loc' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='loc' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td><aval class='dynamic' tipo='loc' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='loc' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>FC %</td>
	        		<td><aval class='dynamic' tipo='fc' date='Td' op='total' compare='0'></aval>%</td>
	        		<td><aval class='dynamic' tipo='fc' date='Y' op='total' compare='0'></aval>%</td>
	        		<td><aval class='dynamic var' tipo='fc' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td><aval class='dynamic' tipo='fc' date='LW' op='total' compare='0'></aval>%</td>
	        		<td><aval class='dynamic var' tipo='fc' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>Calls</td>
	        		<td><aval class='dynamic' tipo='callstotal' date='Td' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic' tipo='callstotal' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='callstotal' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td><aval class='dynamic' tipo='callstotal' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='callstotal' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>Hotel</td>
	        		<td>$<aval class='dynamic' tipo='Hotel' date='Td' op='total' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='Hotel' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Hotel' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='Hotel' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Hotel' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>Vuelo</td>
	        		<td>$<aval class='dynamic' tipo='Vuelo' date='Td' op='total' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='Vuelo' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Vuelo' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='Vuelo' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Vuelo' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        	<tr>
	        		<td style='text-align: left'>Paquete</td>
	        		<td>$<aval class='dynamic' tipo='Paquete' date='Td' op='total' compare='0'></aval></td>
	        		<td>$<aval class='dynamic' tipo='Paquete' date='Y' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Paquete' date='Td' op='var' compare='Y'></aval>%</td>
	        		<td>$<aval class='dynamic' tipo='Paquete' date='LW' op='total' compare='0'></aval></td>
	        		<td><aval class='dynamic var' tipo='Paquete' date='Td' op='var' compare='LW'></aval>%</td>
	        	</tr>
	        </table> 
		</div>
    </div>
</div>
<button class='button button_blue_w' id='zoom' status='0'>Zoom</button>