<?php
//header('Location: venta_2.php');
 session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
include("../common/scripts.php");

?>

<style>
.bloque{
    height: 360;
    padding: 10;

}

.bloque_short{
    height: 225;
    padding: 10;

}

.title{
    float: left;
    margin: 0;
    width: 30%;
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

  function nC(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

   function sendRequest(){
        var urlsend= "_query_venta.php";
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
                pin_montocc_td=text.match("pinccmontoTd- (.*) -pinccmontoTd");
                pin_montonocc_td=text.match("pinnoccmontoTd- (.*) -pinnoccmontoTd");
                pin_monto_yd=text.match("pinmontoY- (.*) -pinmontoY");
                pin_monto_lw=text.match("pinmontoLW- (.*) -pinmontoLW");
                $('#pin-monto').text("$"+pin_monto_td[1]);
                $('#pin-montocc').text("$"+pin_montocc_td[1]);
                $('#pin-montonocc').text("$"+pin_montonocc_td[1]);
                pin_locs_td=text.match("pinlocTd- (.*) -pinlocTd");
                $('#pin-locs').text(pin_locs_td[1]);
                pin_calls_td=text.match("pincallsTd- (.*) -pincallsTd");
                pin_abn_td=text.match("pinuncallsTd- (.*) -pinuncallsTd");
                pin_calls_all_td=parseInt(pin_calls_td[1])+parseInt(pin_abn_td[1]);
                pin_calls_yd=text.match("pincallsY- (.*) -pincallsY");
                pin_calls_lw=text.match("pincallsLW- (.*) -pincallsLW");
                pin_fc_td=parseInt(pin_locs_td[1])/parseInt(pin_calls_td[1])*100;
                monto = pin_monto_td[1].replace(/,/g, "");
                montoyd = pin_monto_yd[1].replace(/,/g, "");
                montolw = pin_monto_lw[1].replace(/,/g, "");
                pin_avtkt_td=parseInt(monto)/parseInt(pin_locs_td[1]);
                $('#pin-calls').text(parseInt(pin_calls_td[1]));
                $('#pin-abn').text(parseInt(pin_abn_td[1]));
                $('#pin-calls_all').text(parseInt(pin_calls_all_td));
                $('#pin-fc').text(pin_fc_td.toFixed(2)+"%");
                $('#pin-avtkt').text("$"+nC(pin_avtkt_td.toFixed(2)));
                pin_calls_vyd=parseInt(pin_calls_all_td)/parseInt(pin_calls_yd[1])*100-100;
                pin_calls_vlw=parseInt(pin_calls_all_td)/parseInt(pin_calls_lw[1])*100-100;
                $('#pin-clw').text(pin_calls_vlw.toFixed(2)+"%");
                $('#pin-cyd').text(pin_calls_vyd.toFixed(2)+"%");
                $('#pin-cclw').text(pin_calls_lw[1]);
                $('#pin-ccyd').text(pin_calls_yd[1]);
                if(pin_calls_vlw>=10){$('#pin-clw, #pin-cclw').removeClass(); $('#pin-clw, #pin-cclw').addClass('upvar');}else{
                    if(pin_calls_vlw<=-10){$('#pin-clw, #pin-cclw').removeClass(); $('#pin-clw, #pin-cclw').addClass('downvar');}else{
                        $('#pin-clw, #pin-cclw').removeClass();
                    }
                }
                if(pin_calls_vyd>=10){$('#pin-cyd, #pin-ccyd').removeClass(); $('#pin-cyd, #pin-ccyd').addClass('upvar');}else{
                    if(pin_calls_vyd<=-10){$('#pin-cyd, #pin-ccyd').removeClass(); $('#pin-cyd, #pin-ccyd').addClass('downvar');}else{
                        $('#pin-cyd, #pin-ccyd').removeClass();
                    }
                }
                $('#pin-mmyd').text("$"+nC(montoyd));
                $('#pin-mmlw').text("$"+nC(montolw));
                pin_monto_vyd=monto/montoyd*100-100;
                pin_monto_vlw=monto/montolw*100-100;
                $('#pin-mlw').text(pin_monto_vlw.toFixed(2)+"%");
                $('#pin-myd').text(pin_monto_vyd.toFixed(2)+"%");
                if(pin_monto_vlw>=10){$('#pin-mlw, #pin-mmlw').removeClass(); $('#pin-mlw, #pin-mmlw').addClass('upvar');}else{
                    if(pin_monto_vlw<=-10){$('#pin-mlw, #pin-mmlw').removeClass(); $('#pin-mlw, #pin-mmlw').addClass('downvar');}else{
                        $('#pin-mlw, #pin-mmlw').removeClass();
                    }
                }
                if(pin_monto_vyd>=10){$('#pin-myd, #pin-mmyd').removeClass(); $('#pin-myd, #pin-mmyd').addClass('upvar');}else{
                    if(pin_monto_vyd<=-10){$('#pin-myd, #pin-mmyd').removeClass(); $('#pin-myd, #pin-mmyd').addClass('downvar');}else{
                        $('#pin-myd, #pin-mmyd').removeClass();
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
                it_abn_td=text.match("ituncallsTd- (.*) -ituncallsTd");
                it_calls_all_td=parseInt(it_calls_td[1])+parseInt(it_abn_td[1]);
                it_calls_yd=text.match("itcallsY- (.*) -itcallsY");
                it_calls_lw=text.match("itcallsLW- (.*) -itcallsLW");
                it_fc_td=parseInt(it_locs_td[1])/parseInt(it_calls_td[1])*100;
                monto = it_monto_td[1].replace(/,/g, "");
                montoyd = it_monto_yd[1].replace(/,/g, "");
                montolw = it_monto_lw[1].replace(/,/g, "");
                it_avtkt_td=parseInt(monto)/parseInt(it_locs_td[1]);
                $('#it-calls').text(parseInt(it_calls_td[1]));
                $('#it-abn').text(parseInt(it_abn_td[1]));
                $('#it-calls_all').text(parseInt(it_calls_all_td));
                $('#it-fc').text(it_fc_td.toFixed(2)+"%");
                $('#it-avtkt').text("$"+nC(it_avtkt_td.toFixed(2)));
                it_calls_vyd=parseInt(it_calls_td[1])/parseInt(it_calls_yd[1])*100-100;
                it_calls_vlw=parseInt(it_calls_td[1])/parseInt(it_calls_lw[1])*100-100;
                $('#it-clw').text(it_calls_vlw.toFixed(2)+"%");
                $('#it-cyd').text(it_calls_vyd.toFixed(2)+"%");
                $('#it-cclw').text(it_calls_lw[1]);
                $('#it-ccyd').text(it_calls_yd[1]);
                if(it_calls_vlw>=10){$('#it-clw, #it-cclw').removeClass(); $('#it-clw, #it-cclw').addClass('upvar');}else{
                    if(it_calls_vlw<=-10){$('#it-clw, #it-cclw').removeClass(); $('#it-clw, #it-cclw').addClass('downvar');}else{
                        $('#it-clw, #it-cclw').removeClass();
                    }
                }
                if(it_calls_vyd>=10){$('#it-cyd, #it-ccyd').removeClass(); $('#it-cyd, #it-ccyd').addClass('upvar');}else{
                    if(it_calls_vyd<=-10){$('#it-cyd, #it-ccyd').removeClass(); $('#it-cyd, #it-ccyd').addClass('downvar');}else{
                        $('#it-cyd, #it-ccyd').removeClass();
                    }
                }
                $('#it-mmyd').text("$"+nC(montoyd));
                $('#it-mmlw').text("$"+nC(montolw));
                it_monto_vyd=monto/montoyd*100-100;
                it_monto_vlw=monto/montolw*100-100;
                $('#it-mlw').text(it_monto_vlw.toFixed(2)+"%");
                $('#it-myd').text(it_monto_vyd.toFixed(2)+"%");
                if(it_monto_vlw>=10){$('#it-mlw, #it-mmlw').removeClass(); $('#it-mlw, #it-mmlw').addClass('upvar');}else{
                    if(it_monto_vlw<=-10){$('#it-mlw, #it-mmlw').removeClass(); $('#it-mlw, #it-mmlw').addClass('downvar');}else{
                        $('#it-mlw, #it-mmlw').removeClass();
                    }
                }
                if(it_monto_vyd>=10){$('#it-myd, #it-mmyd').removeClass(); $('#it-myd, #it-mmyd').addClass('upvar');}else{
                    if(it_monto_vyd<=-10){$('#it-myd, #it-mmyd').removeClass(); $('#it-myd, #it-mmyd').addClass('downvar');}else{
                        $('#it-myd, #it-mmyd').removeClass();
                    }
                }

                //Upsell
                pus_monto_td=text.match("pusmontoTd- (.*) -pusmontoTd");
                pus_montonocc_td=text.match("uspdvmontoTd- (.*) -uspdvmontoTd");
                pus_montototal_td=text.match("ustotalmontoTd- (.*) -ustotalmontoTd");
                pus_monto_yd=text.match("pusmontoY- (.*) -pusmontoY");
                pus_montonocc_yd=text.match("uspdvmontoY- (.*) -uspdvmontoY");
                pus_montototal_yd=text.match("ustotalmontoY- (.*) -ustotalmontoY");
                pus_monto_lw=text.match("pusmontoLW- (.*) -pusmontoLW");
                pus_montonocc_lw=text.match("uspdvmontoLW- (.*) -uspdvmontoLW");
                pus_montototal_lw=text.match("ustotalmontoLW- (.*) -ustotalmontoLW");
                
                pus_locs_td=text.match("puslocTd- (.*) -puslocTd");
                pus_locsnocc_td=text.match("uspdvlocTd- (.*) -uspdvlocTd");
                pus_locstotal_td=text.match("ustotallocTd- (.*) -ustotallocTd");
                pus_locs_y=text.match("puslocY- (.*) -puslocY");
                pus_locsnocc_y=text.match("uspdvlocY- (.*) -uspdvlocY");
                pus_locstotal_y=text.match("ustotallocY- (.*) -ustotallocY");
                pus_locs_lw=text.match("puslocLW- (.*) -puslocLW");
                pus_locsnocc_lw=text.match("uspdvlocLW- (.*) -uspdvlocLW");
                pus_locstotal_lw=text.match("ustotallocLW- (.*) -ustotallocLW");
                
                
                $('#pus-locs').text(pus_locstotal_td[1]);
                $('#pus-locsy').text(pus_locstotal_y[1]);
                $('#pus-locslw').text(pus_locstotal_lw[1]);
                
                monto = pus_montototal_td[1].replace(/,/g, "");
                montoyd = pus_montototal_yd[1].replace(/,/g, "");
                montolw = pus_montototal_lw[1].replace(/,/g, "");
                
                pus_montototal_vyd=monto/montoyd*100-100;
                pus_montototal_vlw=monto/montolw*100-100;
                                                
                $('#pus-monto').text("$"+pus_monto_td[1]);
                $('#pus-montonocc').text("$"+pus_montonocc_td[1]);
                $('#pus-montototal').text("$"+pus_montototal_td[1]);
                $('#pus-mmyd').text("$"+nC(montoyd));
                $('#pus-mmlw').text("$"+nC(montolw));
                $('#pus-mlw').text(pus_montototal_vlw.toFixed(2)+"%");
                $('#pus-myd').text(pus_montototal_vyd.toFixed(2)+"%");
                
                pus_avtkt_td=parseInt(monto)/parseInt(pus_locstotal_td[1]);
                $('#pus-avtkt').text("$"+nC(pus_avtkt_td.toFixed(2)));
                if(pus_montototal_vlw>=10){$('#pus-mlw, #pus-mmlw').removeClass(); $('#pus-mlw, #pus-mmlw').addClass('upvar');}else{
                    if(pus_montototal_vlw<=-10){$('#pus-mlw, #pus-mmlw').removeClass(); $('#pus-mlw, #pus-mmlw').addClass('downvar');}else{
                        $('#pus-mlw, #pus-mmlw').removeClass();
                    }
                }
                if(pus_montototal_vyd>=10){$('#pus-myd, #pus-mmyd').removeClass(); $('#pus-myd, #pus-mmyd').addClass('upvar');}else{
                    if(pus_montototal_vyd<=-10){$('#pus-myd, #pus-mmyd').removeClass(); $('#pus-myd, #pus-mmyd').addClass('downvar');}else{
                        $('#pus-myd, #pus-mmyd').removeClass();
                    }
                }
                
                //ol
                ol_monto_td=text.match("olmontoTd- (.*) -olmontoTd");
                ol_monto_yd=text.match("olmontoY- (.*) -olmontoY");
                ol_monto_lw=text.match("olmontoLW- (.*) -olmontoLW");
                $('#ol-monto').text("$"+ol_monto_td[1]);
                ol_locs_td=text.match("ollocTd- (.*) -ollocTd");
                $('#ol-locs').text(ol_locs_td[1]);
                monto = ol_monto_td[1].replace(/,/g, "");
                montoyd = ol_monto_yd[1].replace(/,/g, "");
                montolw = ol_monto_lw[1].replace(/,/g, "");
                ol_avtkt_td=parseInt(monto)/parseInt(ol_locs_td[1]);
                $('#ol-avtkt').text("$"+nC(ol_avtkt_td.toFixed(2)));
                $('#ol-mmyd').text("$"+nC(montoyd));
                $('#ol-mmlw').text("$"+nC(montolw));
                ol_monto_vyd=monto/montoyd*100-100;
                ol_monto_vlw=monto/montolw*100-100;
                $('#ol-mlw').text(ol_monto_vlw.toFixed(2)+"%");
                $('#ol-myd').text(ol_monto_vyd.toFixed(2)+"%");
                if(ol_monto_vlw>=10){$('#ol-mlw, #ol-mmlw').removeClass(); $('#ol-mlw, #ol-mmlw').addClass('upvar');}else{
                    if(ol_monto_vlw<=-10){$('#ol-mlw, #ol-mmlw').removeClass(); $('#ol-mlw, #ol-mmlw').addClass('downvar');}else{
                        $('#ol-mlw, #ol-mmlw').removeClass();
                    }
                }
                if(ol_monto_vyd>=10){$('#ol-myd, #ol-mmyd').removeClass(); $('#ol-myd, #ol-mmyd').addClass('upvar');}else{
                    if(ol_monto_vyd<=-10){$('#ol-myd, #ol-mmyd').removeClass(); $('#ol-myd, #ol-mmyd').addClass('downvar');}else{
                        $('#ol-myd, #ol-mmyd').removeClass();
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
                $('#ppdv-mmyd').text("$"+nC(montoyd));
                $('#ppdv-mmlw').text("$"+nC(montolw));
                ppdv_monto_vyd=monto/montoyd*100-100;
                ppdv_monto_vlw=monto/montolw*100-100;
                $('#ppdv-mlw').text(ppdv_monto_vlw.toFixed(2)+"%");
                $('#ppdv-myd').text(ppdv_monto_vyd.toFixed(2)+"%");
                if(ppdv_monto_vlw>=10){$('#ppdv-mlw, #ppdv-mmlw').removeClass(); $('#ppdv-mlw, #ppdv-mmlw').addClass('upvar');}else{
                    if(ppdv_monto_vlw<=-10){$('#ppdv-mlw, #ppdv-mmlw').removeClass(); $('#ppdv-mlw, #ppdv-mmlw').addClass('downvar');}else{
                        $('#ppdv-mlw, #ppdv-mmlw').removeClass();
                    }
                }
                if(ppdv_monto_vyd>=10){$('#ppdv-myd, #ppdv-mmyd').removeClass(); $('#ppdv-myd, #ppdv-mmyd').addClass('upvar');}else{
                    if(ppdv_monto_vyd<=-10){$('#ppdv-myd, #ppdv-mmyd').removeClass(); $('#ppdv-myd, #ppdv-mmyd').addClass('downvar');}else{
                        $('#ppdv-myd, #ppdv-mmyd').removeClass();
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
    <div class='container cpin'>
        <p><aval id='pin-monto'></aval><br>
            <a>(CC: <aval id='pin-montocc'></aval> || Otros: <aval id='pin-montonocc'></aval>)<br>
            	Locs: <aval id='pin-locs'></aval><br>
                Calls: <aval id='pin-calls_all'></aval> (Ans: <aval id='pin-calls'></aval> || Abn: <aval id='pin-abn'></aval>)<br>
                FC: <aval id='pin-fc'></aval><br>
                Avg Tkt: <aval id='pin-avtkt'></aval><br>
                Calls LW: <aval id='pin-cclw'></aval> | VarCalls LW: <aval class='upvar' id='pin-clw'></aval><br>
                Calls Yd: <aval id='pin-ccyd'></aval> | VarCalls Yd: <aval class='upvar' id='pin-cyd'></aval><br>
                Monto LW: <aval id='pin-mmlw'></aval> | VarMonto LW: <aval class='downvar' id='pin-mlw'></aval><br>
                Monto Yd: <aval id='pin-mmyd'></aval> | VarMonto Yd: <aval class='upvar' id='pin-myd'></aval></a>
        </p>
    </div>
</div>
<div class='bloque_short' style='height: 260px'>
    <div class='title pus'>Upsell</div>
    <div class='container cpus'><p><aval id='pus-montototal'></aval><br>
            <a>
            	(CC: <aval id='pus-monto'></aval> || PDV: <aval id='pus-montonocc'></aval>)<br>
            	Locs: <aval id='pus-locs'></aval> ( Y: <aval id='pus-locsy'></aval> || LW: <aval id='pus-locslw'></aval> )<br>
                Avg Tkt: <aval id='pus-avtkt'></aval><br>
                Monto LW: <aval id='pus-mmlw'></aval> | VarMonto LW: <aval class='downvar' id='pus-mlw'></aval><br>
                Monto Yd: <aval id='pus-mmyd'></aval> |  VarMonto Yd: <aval class='upvar' id='pus-myd'>%</aval>
                </a>
        </p>
    </div>
</div>
<div class='bloque_short'>
    <div class='title ppdv'>PDV</div>
    <div class='container cppdv'><p><aval id='ppdv-monto'></aval><br>
            <a>Locs: <aval id='ppdv-locs'></aval><br>
                Avg Tkt: <aval id='ppdv-avtkt'></aval><br>
                Monto LW: <aval id='ppdv-mmlw'></aval> | VarMonto LW: <aval class='downvar' id='ppdv-mlw'></aval><br>
                Monto Yd: <aval id='ppdv-mmyd'></aval> | VarMonto Yd: <aval class='upvar' id='ppdv-myd'></aval>
                </a>
        </p>
    </div>
</div>
<div class='bloque_short'>
    <div class='title online'>Online</div>
    <div class='container conline'><p><aval id='ol-monto'></aval><br>
            <a>Locs: <aval id='ol-locs'></aval><br>
                Avg Tkt: <aval id='ol-avtkt'></aval><br>
                Monto LW: <aval id='ol-mmlw'></aval> | VarMonto LW: <aval class='downvar' id='ol-mlw'></aval><br>
                Monto Yd: <aval id='ol-mmyd'></aval> | VarMonto Yd: <aval class='upvar' id='ol-myd'></aval>
                </a>
        </p>
    </div>
</div>
<div class='bloque'>
    <div class='title'>Inbound MT</div>
    <div class='container'><p><aval id='it-monto'></aval><br>
            <a>Locs: <aval id='it-locs'></aval><br>
                Calls: <aval id='it-calls_all'></aval> (Ans: <aval id='it-calls'></aval> || Abn: <aval id='it-abn'></aval>)<br>
                FC: <aval id='it-fc'></aval><br>
                Avg Tkt: <aval id='it-avtkt'></aval><br>
                Calls LW: <aval id='it-cclw'></aval> | VarCalls LW: <aval class='upvar' id='it-clw'></aval><br>
                Calls Yd: <aval id='it-ccyd'></aval> | VarCalls Yd: <aval class='upvar' id='it-cyd'></aval><br>
                Monto LW: <aval id='it-mmlw'></aval> | VarMonto LW: <aval class='downvar' id='it-mlw'></aval><br>
                Monto Yd: <aval id='it-mmyd'></aval> | VarMonto Yd: <aval class='upvar' id='it-myd'></aval></a>
        </p>
    </div>
</div>
<button class='button button_blue_w' id='zoom' status='0'>Zoom</button>