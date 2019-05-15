<?php
include("../connectMYSQLI.php");
include("../common/scripts.php");
date_default_timezone_set('America/Bogota');


for($x=1;$x<=5;$x++){

    switch($x){
        case 1:
            $dep="Ventas";
            $q="3";
            $aht=550;
            $comida=1800;
            break;
        case 2:
            $dep="SAC";
            $q="4";
            $aht=600;
            $comida=1800;
            break;
        case 3:
            $dep="Agencias";
            $q="7";
            $aht=550;
            $comida=3600;
            break;
        case 4:
            $dep="TMP";
            $q="9";
            $aht=300;
            $comida=1800;
            break;
        case 5:
            $dep="TMT";
            $q="8";
            $aht=261;
            $comida=3600;
            break;
        case 6:
            $dep="Upsell";
            $q="5";
            $aht=261;
            $comida=1800;
            break;
    }
}

?>
<script>

$(function(){

  setInterval(function(){
           sendRequest();
       },1000);

   function sendRequest(){
        var urlsend= "qm_vars.php?tipo=sla";
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
                var answ=[], unans=[], abn=[], sla=[];
                function writeData(skill,name,slaok){
                    //Answered
                    answ[skill]=text.match("-"+skill+"Answered- (.*) -"+skill+"Answered-");
                    $('#'+name+'-Answered').html("<img src='/images/avail.png' height='60' width='60'> "+answ[skill][1]);
                    
                    //Unanswered
                    unans[skill]=text.match("-"+skill+"Unanswered- (.*) -"+skill+"Unanswered-");
                    $('#'+name+'-Unanswered').html("<img src='/images/cross.png' height='60' width='60'> "+unans[skill][1]);
                    
                    //Total
                    total=parseInt(answ[skill][1])+parseInt(unans[skill][1]);
                    $('#'+name+'-Calls').html("<img src='/images/avail.png' height='60' width='60'> "+total);
                    
                    //Abandon
                    abn[skill]=text.match("-"+skill+"Abandon- (.*) -"+skill+"Abandon-");
                    $('#'+name+'-Abandon').html("<img src='/images/abandon.png' height='60' width='60'> "+abn[skill][1]);
                
                	//SLA
                    sla[skill]=text.match("-"+skill+"psla"+slaok+"- (.*) -"+skill+"psla"+slaok+"-");
                    $('#'+name+'-SLA').html("<img src='/images/sla.png' height='60' width='60'> "+sla[skill][1]);
				}
                
                try{
					writeData(3,"Ventas","20");
	                writeData(35,"Ventas_MP","20");
	                writeData(4,"SAC","30");
	                writeData(7,"Agencias","30");
	                writeData(9,"TMP","30");
	                writeData(8,"TMT","30");
	                writeData(5,"Upsell","30");	
				}
				catch(err){
					$('#error').text(err);
				}	
                

                lu=text.match("-lu- (.*) -lu-");
                $('#LU').text('Last Update: ' + lu[1]);
            }
        }
        xmlhttp.open("GET",urlsend,true);
        xmlhttp.send();

    }

   if($('#waitval').text()>0){
       if($('#waitval').text()>2){
        $('#waiting').css('background','#E8C44F');
       }
       if($('#waitval').text()>5){
        $('#waiting').css('background','#A80000');
       }
   }

   if($('#pauseval').text()>0){
       if($('#pauseval').text()>3){
        $('#pause').css('background','#E8C44F');
       }
       if($('#pauseval').text()>5){
        $('#pause').css('background','#A80000');
       }
   }

   qlongest=$('#longvalq').attr('valor');
   clongest=$('#longval').attr('valor');
   if(clongest!=''){
       setInterval(function(){
           clongest=parseInt(clongest)+1;
           $('#longval').html(clongest+" <span style='font-size:18px; margin:0'>seg.</span>");
       },1000);
   }else{
        $('#longval').html("0 <span style='font-size:18px; margin:0'>seg.</span>");
   }

   if(qlongest!=''){
       setInterval(function(){
           qlongest=parseInt(qlongest)+1;
           $('#longvalq').html(qlongest+" <span style='font-size:18px; margin:0'>seg.</span>");
       },1000);
   }else{
        $('#longvalq').html("0 <span style='font-size:18px; margin:0'>seg.</span>");
   }

});


</script>
<style>

.fila{
    width: 80%;
    height: 300px;
    margin:20px;
}

.resumen{
    margin: 0 10 -30 10;
    float: left;
    width: 100%;
    padding: 0;
    padding-top: 0;
    padding-bottom: 0;
}

.container{
    width: 315px;
    height: 123px;
    border-radius: 15px;
    background: #779ECB;
    vertical-align:middle;
    float: left;
    margin: 15px;
    border: solid 3px yellow;
    color: white;
    font-size: 90px;
    text-align: center;
    font-family: Arial, Helvetica, sans-serif;
    font-weight: bold;
    font-style: normal;
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
    padding-top: 15px;
}

.controsa{
    background: #ECA7B9;
}



.longcalls{
    width: 471;
    height: 150px;
    border-radius: 15px;
    font-size: 30px;
    margin: 0 6 10 4;
    float: right;
    border: solid 4px;
    background: #990000;
    text-align: center;
    font-style: normal;
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
    border: 1px solid;
    border-top: 1px solid;
    box-shadow: 0 0 10px rgba(0,0,0,0.35), 0px 1px 3px rgba(0,0,0,0.18), inset 0px -3px 0px rgba(0,0,0,0.35), inset 0px 14px 14px rgba(255,255,255,0.10);
    -webkit-box-shadow: 0 0 10px rgba(0,0,0,0.35), 0px 1px 3px rgba(0,0,0,0.18), inset 0px -3px 0px rgba(0,0,0,0.35), inset 0px 14px 14px rgba(255,255,255,0.10);
    -moz-box-shadow: 0 0 10px rgba(0,0,0,0.35), 0px 1px 3px rgba(0,0,0,0.18), inset 0px -3px 0px rgba(0,0,0,0.35), inset 0px 14px 14px rgba(255,255,255,0.10);
    -o-box-shadow: 0 0 10px rgba(0,0,0,0.35), 0px 1px 3px rgba(0,0,0,0.18), inset 0px -3px 0px rgba(0,0,0,0.35), inset 0px 14px 14px rgba(255,255,255,0.10);
    -ms-box-shadow: 0 0 10px rgba(0,0,0,0.35), 0px 1px 3px rgba(0,0,0,0.18), inset 0px -3px 0px rgba(0,0,0,0.35), inset 0px 14px 14px rgba(255,255,255,0.10);text-decoration: none;
    text-shadow: 0 -1px 1px rgba(0,0,0,0.50);
    color: white;
}

.pausas{
    width: 474;
    height: 150;
    border-radius: 15px;
    font-size: 30px;
    margin-left: 15px;
    margin-top: 15px;
    float: right;
    border: solid 4px yellow;
    background: #CB9F10;
    text-align: center;
    font-style: normal;
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
    border: 1px solid yellow;
    border-top: 1px solid yellow;
    box-shadow: 0 0 10px rgba(0,0,0,0.35), 0px 1px 3px rgba(0,0,0,0.18), inset 0px -3px 0px rgba(0,0,0,0.35), inset 0px 14px 14px rgba(255,255,255,0.10);
    -webkit-box-shadow: 0 0 10px rgba(0,0,0,0.35), 0px 1px 3px rgba(0,0,0,0.18), inset 0px -3px 0px rgba(0,0,0,0.35), inset 0px 14px 14px rgba(255,255,255,0.10);
    -moz-box-shadow: 0 0 10px rgba(0,0,0,0.35), 0px 1px 3px rgba(0,0,0,0.18), inset 0px -3px 0px rgba(0,0,0,0.35), inset 0px 14px 14px rgba(255,255,255,0.10);
    -o-box-shadow: 0 0 10px rgba(0,0,0,0.35), 0px 1px 3px rgba(0,0,0,0.18), inset 0px -3px 0px rgba(0,0,0,0.35), inset 0px 14px 14px rgba(255,255,255,0.10);
    -ms-box-shadow: 0 0 10px rgba(0,0,0,0.35), 0px 1px 3px rgba(0,0,0,0.18), inset 0px -3px 0px rgba(0,0,0,0.35), inset 0px 14px 14px rgba(255,255,255,0.10);text-decoration: none;
    text-shadow: 0 -1px 1px rgba(0,0,0,0.50);
    color: black;
}

.leftT{
    width: 100%;
    height:50%;
    margin: 0;
    line-height: normal;
    text-align:center;
    padding-top:5px;
    padding-left: 14px;
    font-size: 38px;
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
    width: 570;
    height: 100px;
    border-radius: 15px;
    background: #215086;
    vertical-align:middle;
    float: left;
    margin: 15px;
    border: solid 3px yellow;
    color: white;
    font-size: 90px;
    text-align: left;
    font-family: Arial, Helvetica, sans-serif;
    font-weight: bold;
    font-style: normal;
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
    padding-top: 38px;
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

.sla{
	width: 395px;
}


</style>

<body style='background: white; zoom: .23;overflow: hidden;'>
<br>

    <?php
    function printRow($depart){
        switch($depart){
            case "Ventas":
                $tmpTitle="Ventas - MT";
                $bgstyle="";
                $bgstylec="";
                break;
            case "Ventas_MP":
                $tmpTitle="Ventas - MP";
                $bgstyle="style='background:#D6436A'";
                $bgstylec=" controsa";
                break;
            case "SAC":
                $tmpTitle="SAC";
                $bgstyle="";
                $bgstylec="";
                break;
            case "TMP":
                $tmpTitle="Trafico MP";
                $bgstyle="";
                $bgstylec="";
                break;
            case "TMT":
                $tmpTitle="Trafico MT";
                $bgstyle="";
                $bgstylec="";
                break;
            default:
                $tmpTitle=$depart;
                $bgstyle="";
                $bgstylec="";
                break;
        }
        echo "<div class='resumen'>";
        echo "<div class='container$bgstylec' ><p id='$depart-Calls' style='margin: 0;'><img src='/images/inbound.png' height='60' width='60'> ".$info[' all selected ']['Calls']."</p><br><p style='font-size: 40px; color: black; margin-top: -22; text-align:center'>T. Calls</p></div>";
        echo "<div class='container$bgstylec'  id='pause' $bgp><p id='$depart-Answered' style='margin: 0;'><img src='/images/avail.png' height='60' width='60'> ".$info[' all selected ']['Answered']."</p><br><p style='font-size: 40px; color: black; margin-top: -22; text-align:center'>Answered</p></div>";
        echo "<div class='container$bgstylec' ><p id='$depart-Unanswered' style='margin: 0;'><img src='/images/cross.png' height='60' width='60'> ".$info[' all selected ']['Unanswered']."</p><br><p style='font-size: 40px; color: black; margin-top: -22; text-align:center'>Unanswered</p></div>";
        echo "<div class='container$bgstylec sla'  id='waiting'><p id='$depart-Abandon' style='margin: 0;'><img src='/images/abandon.png' height='60' width='60'> ".$info[' all selected ']['Abandon']."%</p><br><p style='font-size: 40px; color: black; margin-top: -22; text-align:center'>Abandon</p></div>";
        echo "<div class='container$bgstylec sla'  id='waiting' $bg><p id='$depart-SLA' style='margin: 0;'><img src='/images/sla.png' height='60' width='60'> ".$info[' all selected ']['SLA']."%</p><br><p style='font-size: 40px; color: black; margin-top: -22; text-align:center'>SLA</p></div>";
        echo "<div class='titles' $bgstyle>$tmpTitle</div>";
        echo "</div>
            <br>
            </div>";
    }

    printRow("Ventas");
    printRow("Ventas_MP");
    printRow("Upsell");
    printRow("SAC");
    printRow("Agencias");
    printRow("TMP");
    printRow("TMT");
    
    $connectdb->close();
    $connectdbcc->close();

    ?>
    
<br>

<div class='resumen'>
    <div class='titles' style='width: 100%; text-align: center;' id='LU'>Last Update: </div>

</div>

<div id='error'></div>
</body>
