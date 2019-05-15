
<head>



<link rel="stylesheet" type="text/css"
          href="/styles/tables1.css"/>
<link rel="stylesheet" type="text/css"
          href="/styles/forms.css"/>
          <link rel="stylesheet" type="text/css"
          href="/styles/greentables.css"/>
<link rel="stylesheet" type="text/css"
          href="/styles/picker.css"/>
<link rel="stylesheet" type="text/css"
          href="/styles/express-table-style.css"/>
          <link rel="stylesheet" type="text/css"
          href="/js/tablesorter/css/theme.blue.css"/>


    <!-- calendar styling -->
    <link rel="stylesheet" type="text/css"
          href="/styles/calendar.css" />
  <link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
 <!-- <link rel="stylesheet" href="http://code.jquery.com/mobile/1.0b1/jquery.mobile-1.0b1.min.css">
  <link rel="stylesheet" href="http://code.jquery.com/mobile/1.0b1/jquery.mobile-1.0b1.min.js">
-->
  <script src="http://pt.comeycome.com/js/jquery.tools.min.js"></script>
  <script src="http://code.jquery.com/jquery-1.10.2.js"></script>
  <script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  <script type="text/javascript" src="/js/pnotify.custom.min.js"></script>
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript" src="/js/jquery.PopUpWindow.js"></script>
   <script type="text/javascript" src="/js/noty-2.3.8/js/noty/packaged/jquery.noty.packaged.min.js"></script>
   <script type="text/javascript" src="/js/tablesorter/js/jquery.tablesorter.js"></script>
   <script type="text/javascript" src="/js/tablesorter/js/jquery.tablesorter.widgets.js"></script>

   <script type="text/javascript" src="/js/jquery.filtertable.js"></script>



</head>
<style>
.bloque{
    height: 360;
    padding: 10;

}

.title{
    float: left;
    margin: 0;
    width: 40%;
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

.header{
    background: #215086;
    height: 205;
    margin-bottom: 50px;
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
       },1000);

  function nC(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

   function sendRequest(){
        var urlsend= "query_venta.php";
        var xmlhttp;
        var text;

        if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        } else { // code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

        xmlhttp.onreadystatechange=function(){
            if (xmlhttp.readyState==4 && xmlhttp.status==200){
                text= xmlhttp.responseText;
                //PIN
                pin_monto_td=text.match("pinmontoTd- (.*) -pinmontoTd");
                pin_monto_yd=text.match("pinmontoY- (.*) -pinmontoY");
                pin_monto_lw=text.match("pinmontoLW- (.*) -pinmontoLW");
                $('#pin-monto').text("$"+pin_monto_td[1]);
                pin_locs_td=text.match("pinlocTd- (.*) -pinlocTd");
                $('#pin-locs').text(pin_locs_td[1]);
                pin_calls_td=text.match("pincallsTd- (.*) -pincallsTd");
                pin_calls_yd=text.match("pincallsY- (.*) -pincallsY");
                pin_calls_lw=text.match("pincallsLW- (.*) -pincallsLW");
                pin_fc_td=parseInt(pin_locs_td[1])/parseInt(pin_calls_td[1])*100;
                monto = pin_monto_td[1].replace(/,/g, "");
                montoyd = pin_monto_yd[1].replace(/,/g, "");
                montolw = pin_monto_lw[1].replace(/,/g, "");
                pin_avtkt_td=parseInt(monto)/parseInt(pin_locs_td[1]);
                $('#pin-fc').text(pin_fc_td.toFixed(2)+"%");
                $('#pin-avtkt').text("$"+nC(pin_avtkt_td.toFixed(2)));
                pin_calls_vyd=parseInt(pin_calls_td[1])/parseInt(pin_calls_yd[1])*100-100;
                pin_calls_vlw=parseInt(pin_calls_td[1])/parseInt(pin_calls_lw[1])*100-100;
                $('#pin-clw').text(pin_calls_vlw.toFixed(2)+"%");
                $('#pin-cyd').text(pin_calls_vyd.toFixed(2)+"%");
                if(pin_calls_vlw>=10){$('#pin-clw').removeClass(); $('#pin-clw').addClass('upvar');}else{
                    if(pin_calls_vlw<=-10){$('#pin-clw').removeClass(); $('#pin-clw').addClass('downvar');}else{
                        $('#pin-clw').removeClass();
                    }
                }
                if(pin_calls_vyd>=10){$('#pin-cyd').removeClass(); $('#pin-cyd').addClass('upvar');}else{
                    if(pin_calls_vyd<=-10){$('#pin-cyd').removeClass(); $('#pin-cyd').addClass('downvar');}else{
                        $('#pin-cyd').removeClass();
                    }
                }
                pin_monto_vyd=monto/montoyd*100-100;
                pin_monto_vlw=monto/montolw*100-100;
                $('#pin-mlw').text(pin_monto_vlw.toFixed(2)+"%");
                $('#pin-myd').text(pin_monto_vyd.toFixed(2)+"%");
                if(pin_monto_vlw>=10){$('#pin-mlw').removeClass(); $('#pin-mlw').addClass('upvar');}else{
                    if(pin_monto_vlw<=-10){$('#pin-mlw').removeClass(); $('#pin-mlw').addClass('downvar');}else{
                        $('#pin-mlw').removeClass();
                    }
                }
                if(pin_monto_vyd>=10){$('#pin-myd').removeClass(); $('#pin-myd').addClass('upvar');}else{
                    if(pin_monto_vyd<=-10){$('#pin-myd').removeClass(); $('#pin-myd').addClass('downvar');}else{
                        $('#pin-myd').removeClass();
                    }
                }
                //IT
                it_monto_td=text.match("itmontoTd- (.*) -itmontoTd");
                it_monto_yd=text.match("itmontoY- (.*) -itmontoY");
                it_monto_lw=text.match("itmontoLW- (.*) -itmontoLW");
                $('#it-monto').text("$"+it_monto_td[1]);
                it_locs_td=text.match("itlocTd- (.*) -itlocTd");
                $('#it-locs').text(it_locs_td[1]);
                it_calls_td=text.match("itcallsTd- (.*) -itcallsTd");
                it_calls_yd=text.match("itcallsY- (.*) -itcallsY");
                it_calls_lw=text.match("itcallsLW- (.*) -itcallsLW");
                it_fc_td=parseInt(it_locs_td[1])/parseInt(it_calls_td[1])*100;
                monto = it_monto_td[1].replace(/,/g, "");
                montoyd = it_monto_yd[1].replace(/,/g, "");
                montolw = it_monto_lw[1].replace(/,/g, "");
                it_avtkt_td=parseInt(monto)/parseInt(it_locs_td[1]);
                $('#it-fc').text(it_fc_td.toFixed(2)+"%");
                $('#it-avtkt').text("$"+nC(it_avtkt_td.toFixed(2)));
                it_calls_vyd=parseInt(it_calls_td[1])/parseInt(it_calls_yd[1])*100-100;
                it_calls_vlw=parseInt(it_calls_td[1])/parseInt(it_calls_lw[1])*100-100;
                $('#it-clw').text(it_calls_vlw.toFixed(2)+"%");
                $('#it-cyd').text(it_calls_vyd.toFixed(2)+"%");
                if(it_calls_vlw>=10){$('#it-clw').removeClass(); $('#it-clw').addClass('upvar');}else{
                    if(it_calls_vlw<=-10){$('#it-clw').removeClass(); $('#it-clw').addClass('downvar');}else{
                        $('#it-clw').removeClass();
                    }
                }
                if(it_calls_vyd>=10){$('#it-cyd').removeClass(); $('#it-cyd').addClass('upvar');}else{
                    if(it_calls_vyd<=-10){$('#it-cyd').removeClass(); $('#it-cyd').addClass('downvar');}else{
                        $('#it-cyd').removeClass();
                    }
                }
                it_monto_vyd=monto/montoyd*100-100;
                it_monto_vlw=monto/montolw*100-100;
                $('#it-mlw').text(it_monto_vlw.toFixed(2)+"%");
                $('#it-myd').text(it_monto_vyd.toFixed(2)+"%");
                if(it_monto_vlw>=10){$('#it-mlw').removeClass(); $('#it-mlw').addClass('upvar');}else{
                    if(it_monto_vlw<=-10){$('#it-mlw').removeClass(); $('#it-mlw').addClass('downvar');}else{
                        $('#it-mlw').removeClass();
                    }
                }
                if(it_monto_vyd>=10){$('#it-myd').removeClass(); $('#it-myd').addClass('upvar');}else{
                    if(it_monto_vyd<=-10){$('#it-myd').removeClass(); $('#it-myd').addClass('downvar');}else{
                        $('#it-myd').removeClass();
                    }
                }

                //PUS
                pus_monto_td=text.match("pusmontoTd- (.*) -pusmontoTd");
                pus_monto_yd=text.match("pusmontoY- (.*) -pusmontoY");
                pus_monto_lw=text.match("pusmontoLW- (.*) -pusmontoLW");
                $('#pus-monto').text("$"+pus_monto_td[1]);
                pus_locs_td=text.match("puslocTd- (.*) -puslocTd");
                $('#pus-locs').text(pus_locs_td[1]);
                monto = pus_monto_td[1].replace(/,/g, "");
                montoyd = pus_monto_yd[1].replace(/,/g, "");
                montolw = pus_monto_lw[1].replace(/,/g, "");
                pus_avtkt_td=parseInt(monto)/parseInt(pus_locs_td[1]);
                $('#pus-avtkt').text("$"+nC(pus_avtkt_td.toFixed(2)));
                pus_monto_vyd=monto/montoyd*100-100;
                pus_monto_vlw=monto/montolw*100-100;
                $('#pus-mlw').text(pus_monto_vlw.toFixed(2)+"%");
                $('#pus-myd').text(pus_monto_vyd.toFixed(2)+"%");
                if(pus_monto_vlw>=10){$('#pus-mlw').removeClass(); $('#pus-mlw').addClass('upvar');}else{
                    if(pus_monto_vlw<=-10){$('#pus-mlw').removeClass(); $('#pus-mlw').addClass('downvar');}else{
                        $('#pus-mlw').removeClass();
                    }
                }
                if(pus_monto_vyd>=10){$('#pus-myd').removeClass(); $('#pus-myd').addClass('upvar');}else{
                    if(pus_monto_vyd<=-10){$('#pus-myd').removeClass(); $('#pus-myd').addClass('downvar');}else{
                        $('#pus-myd').removeClass();
                    }
                }

                //PDV
                ppdv_monto_td=text.match("ppdvmontoTd- (.*) -ppdvmontoTd");
                ppdv_monto_yd=text.match("ppdvmontoY- (.*) -ppdvmontoY");
                ppdv_monto_lw=text.match("ppdvmontoLW- (.*) -ppdvmontoLW");
                $('#ppdv-monto').text("$"+ppdv_monto_td[1]);
                ppdv_locs_td=text.match("ppdvlocTd- (.*) -ppdvlocTd");
                $('#ppdv-locs').text(ppdv_locs_td[1]);
                monto = ppdv_monto_td[1].replace(/,/g, "");
                montoyd = ppdv_monto_yd[1].replace(/,/g, "");
                montolw = ppdv_monto_lw[1].replace(/,/g, "");
                ppdv_avtkt_td=parseInt(monto)/parseInt(ppdv_locs_td[1]);
                $('#ppdv-avtkt').text("$"+nC(ppdv_avtkt_td.toFixed(2)));
                ppdv_monto_vyd=monto/montoyd*100-100;
                ppdv_monto_vlw=monto/montolw*100-100;
                $('#ppdv-mlw').text(ppdv_monto_vlw.toFixed(2)+"%");
                $('#ppdv-myd').text(ppdv_monto_vyd.toFixed(2)+"%");
                if(ppdv_monto_vlw>=10){$('#ppdv-mlw').removeClass(); $('#ppdv-mlw').addClass('upvar');}else{
                    if(ppdv_monto_vlw<=-10){$('#ppdv-mlw').removeClass(); $('#ppdv-mlw').addClass('downvar');}else{
                        $('#ppdv-mlw').removeClass();
                    }
                }
                if(ppdv_monto_vyd>=10){$('#ppdv-myd').removeClass(); $('#ppdv-myd').addClass('upvar');}else{
                    if(ppdv_monto_vyd<=-10){$('#ppdv-myd').removeClass(); $('#ppdv-myd').addClass('downvar');}else{
                        $('#ppdv-myd').removeClass();
                    }
                }

                //LU
                lu=text.match("lu- (.*) -lu");
                $('#LU').text(lu[1]);
            }
        }
        xmlhttp.open("GET",urlsend,true);
        xmlhttp.send();

    }
});
</script>

<div class='header'>
KPIS Venta<br>
<aval id='LU'></aval>
</div>

<div class='bloque'>
    <div class='title pin'>PriceTravel Inbound</div>
    <div class='container cpin'>
        <p><aval id='pin-monto'></aval><br>
            <a>Locs: <aval id='pin-locs'></aval><br>
                FC: <aval id='pin-fc'></aval><br>
                Avg Tkt: <aval id='pin-avtkt'></aval><br>
                VarCalls LW: <aval class='upvar' id='pin-clw'></aval> | VarCalls Yd: <aval class='upvar' id='pin-cyd'></aval><br>
                VarMonto LW: <aval class='downvar' id='pin-mlw'></aval> | VarMonto Yd: <aval class='upvar' id='pin-myd'></aval></a>
        </p>
    </div>
</div>
<div class='bloque'>
    <div class='title pus'>PriceTravel Upsell</div>
    <div class='container cpus'><p><aval id='pus-monto'></aval><br>
            <a>Locs: <aval id='pus-locs'></aval><br>
                Avg Tkt: <aval id='pus-avtkt'></aval><br>
                VarMonto LW: <aval class='downvar' id='pus-mlw'></aval><br>
                VarMonto Yd: <aval class='upvar' id='pus-myd'>%</aval></a>
        </p>
    </div>
</div>
<div class='bloque'>
    <div class='title ppdv'>PriceTravel PDV</div>
    <div class='container cppdv'><p><aval id='ppdv-monto'></aval><br>
            <a>Locs: <aval id='ppdv-locs'></aval><br>
                Avg Tkt: <aval id='ppdv-avtkt'></aval><br>
                VarMonto LW: <aval class='downvar' id='ppdv-mlw'></aval><br>
                VarMonto Yd: <aval class='upvar' id='ppdv-myd'></aval></a>
        </p>
    </div>
</div>
<div class='bloque'>
    <div class='title'>Intertours de Interjet</div>
    <div class='container'><p><aval id='it-monto'></aval><br>
            <a>Locs: <aval id='it-locs'></aval><br>
                FC: <aval id='it-fc'></aval><br>
                Avg Tkt: <aval id='it-avtkt'></aval><br>
                VarCalls LW: <aval class='upvar' id='it-clw'></aval> | VarCalls Yd: <aval class='upvar' id='it-cyd'></aval><br>
                VarMonto LW: <aval class='downvar' id='it-mlw'></aval> | VarMonto Yd: <aval class='upvar' id='it-myd'></aval></a>
        </p>
    </div>
</div>
