<?php
include("../connectDB.php");
include("../common/scripts.php");
date_default_timezone_set('America/Bogota');
//header('Content-Type: text/html; charset=utf-8');

$depart=$_GET['dep'];

switch($depart){
    case "Ventas":
        $q="224|227|232|234|259|207|206|208";
        $aht=550;
        $comida=1800;
        $dep=3;
        break;
    case "VentasMP":
        $q="207|206|208";
        $aht=550;
        $comida=1800;
        $dep=35;
        break;
    case "VentasMT":
        $q="224|227|232|234|259|";
        $aht=550;
        $comida=1800;
        break;
    case "SAC":
        $q="226|229|233|235|230|666";
        $aht=600;
        $comida=1800;
        $dep=4;
        break;
    case "Agencias":
        $q="222|223";
        $aht=550;
        $comida=3600;
        $dep=7;
        break;
    case "TMP":
        $q="231";
        $aht=300;
        $comida=1800;
        $dep=9;
        break;
    case "TMT":
        $q="236";
        $aht=241;
        $comida=3600;
        $dep=8;
        break;
   case "Upsell":
        $dep=5;
        $aht=261;
        $comida=1800;
        break;
}


?>
<script>

$(function(){

	sendRequest();
   formatBlocks();
   
   setInterval(function(){
           sendRequest();
           formatBlocks();
       },200000);

   function sendRequest(){
        var urlsend= "qm_vars.php?tipo=rtasesor&skill=<?php echo $dep; ?>";
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
                var ol=[], aux=[], avail=[], incalls=[], outcalls=[], waiting=[], aht=[], ahtseg=[], longc=[], longw=[], wv=[], wvmp=[], ws=[], wu=[], wmp=[], wmt=[], wag=[];
                function writeData(skill,name){
                    //agents online
                    ol[skill]=text.match("-agents"+skill+"- (.*) -agents"+skill+"-");
                    $('#agentsval').html("<img src='/images/online.png' height='60' width='60'> "+ol[skill][1]);

                    //agents aux
                    aux[skill]=text.match("-paused"+skill+"- (.*) -paused"+skill+"-");
                    $('#pauseval').html("<img src='/images/paused.png' height='60' width='60'> "+aux[skill][1]);

                    //agents avail
                    avail[skill]=text.match("-avail"+skill+"- (.*) -avail"+skill+"-");
                    $('#availval').html("<img src='/images/avail.png' height='60' width='60'> "+avail[skill][1]);

                    //online calls
                    incalls[skill]=text.match("-callsin"+skill+"- (.*) -callsin"+skill+"-");
                    $('#inval').html("<img src='/images/inbound.png' height='60' width='60'> "+incalls[skill][1]);

					//out calls
                    outcalls[skill]=text.match("-callsout"+skill+"- (.*) -callsout"+skill+"-");
                    $('#outval').html("<img src='/images/outbound.png' height='60' width='60'> "+outcalls[skill][1]);

                    //waiting calls
                    waiting[skill]=text.match("-waitingcalls"+skill+"- (.*) -waitingcalls"+skill+"-");
                    $('#waitval').html("<img src='/images/waiting.png' height='60' width='60'> "+waiting[skill][1]);

                    //aht
                    aht[skill]=text.match("-aht"+skill+"- (.*) -aht"+skill+"-");
                    ahtseg[skill]=text.match("-ahtseg"+skill+"- (.*) -ahtseg"+skill+"-");
                    $('#ahtval').html(aht[skill][1]);
                    $('#ahtval').attr('seg',ahtseg[skill][1]);
                    
                    //longest call
                    longc[skill]=text.match("-longestcalli"+skill+"- (.*) -longestcalli"+skill+"-");
                    $('#longval').html(longc[skill][1]);
                    $('#longval').attr('valor',longc[skill][1]);
                    
                    //longest wait
                    longw[skill]=text.match("-longestcallw"+skill+"- (.*) -longestcallw"+skill+"-");
                    $('#longvalq').html(longw[skill][1]);
                    $('#longvalq').attr('valor',longw[skill][1]);
                    
                    //Colas Wait
	                    //VENTAS
	                    wv[skill]=text.match("-waitingcalls3- (.*) -waitingcalls3-");
	                    $('#w_ventas_val').text(wv[skill][1]);
	                    
	                    //VENTASMP
	                    wvmp[skill]=text.match("-waitingcalls35- (.*) -waitingcalls35-");
	                    $('#w_ventasmp_val').text(wvmp[skill][1]);
	                    
	                    //SAC
	                    ws[skill]=text.match("-waitingcalls4- (.*) -waitingcalls4-");
	                    $('#w_sac_val').text(ws[skill][1]);
	                    
	                    //UPSELL
	                    try{ 
		                	wu[skill]=text.match("-waitingcalls5- (.*) -waitingcalls5-");
		                	$('#w_upsell_val').text(wu[skill][1]);
	                    }
						catch(err){
							$('#w_upsell_val').text("0");
						}
						
	                    
	                    //TMP
	                    wmp[skill]=text.match("-waitingcalls9- (.*) -waitingcalls9-");
	                    $('#w_tmp_val').text(wmp[skill][1]);
	                    
	                    //TMT
	                    wmt[skill]=text.match("-waitingcalls8- (.*) -waitingcalls8-");
	                    $('#w_tmt_val').text(wmt[skill][1]);
	                    
	                    //AGENCIAS
	                    wag[skill]=text.match("-waitingcalls7- (.*) -waitingcalls7-");
	                    $('#w_agencias_val').text(wag[skill][1]);
		   			

                }

                try{ 
                	writeData(<?php echo "$dep, '$depart'"; ?>);
                }
				catch(err){
					$('#error').text(err);
				}
				
				$('#res_asesores').empty();
				
				var p_total=[], p_asesor=[], p_status=[], p_avail=[], p_caller=[], p_queue=[], p_calldur=[], p_callseg=[], p_pausem=[], p_pauseseg=[], p_pausedur=[];
				function asesores(skill,name){
					//total
					p_total[skill]=text.match("-t- (.*) -t-");
					total=parseInt(p_total[skill][1]);
					
					var i=0;
					while(i<total){
						//Agent
							tmpagent=text.match("-asesor"+i+"- (.*) -asesor"+i+"-");
							
						//Status
							tmpstatus=text.match("-status"+i+"- (.*) -status"+i+"-");
							
						//avail
							tmpavail=text.match("-savail"+i+"- (.*) -savail"+i+"-");
							
						//availseg
							tmpavailseg=text.match("-availseg"+i+"- (.*) -availseg"+i+"-");
						
						//caller
							tmpcaller=text.match("-caller"+i+"- (.*) -caller"+i+"-");
							
						//queue
							tmpqueue=text.match("-queue"+i+"- (.*) -queue"+i+"-");
							
						//calldur
							tmpcalldur=text.match("-calldur"+i+"- (.*) -calldur"+i+"-");
							
						//callseg
							tmpcallseg=text.match("-callseg"+i+"- (.*) -callseg"+i+"-");
							
						//pausem
							tmppausem=text.match("-pausem"+i+"- (.*) -pausem"+i+"-");
							
						//pauseseg
							tmppauseseg=text.match("-pauseseg"+i+"- (.*) -pauseseg"+i+"-");
						
						//pausedur
							tmppausedur=text.match("-pausedur"+i+"- (.*) -pausedur"+i+"-");
							
						switch(parseInt(tmpstatus[1])){
							//Avail
							case 0:
								agent_block="<div class='asesor avail' status='"+tmpstatus[1]+"'><div></div><div class='name'>"+tmpagent[1]+"</div><div class='availtime' seg='"+tmpavailseg[1]+"'>"+tmpavail[1]+"</div><div class='callqueue'>Tiempo Disponible</div</div>";
	                    		$('#res_asesores').append(agent_block);
	                    		break;
	                    	//Incall
							case 1:
								agent_block="<div class='asesor incall' status='"+tmpstatus[1]+"'><div class='name'>"+tmpagent[1]+"</div><div class='calldetails' seg='"+tmpcallseg[1]+"'>"+tmpcaller[1]+" || "+tmpcalldur[1]+"</div><div class='callqueue'>"+tmpqueue[1]+"</div</div>";
	                    		$('#res_asesores').append(agent_block);
	                    		break;
	                    	//Onpause
							case 2:
								agent_block="<div class='asesor onpause' status='"+tmpstatus[1]+"'><div class='name'>"+tmpagent[1]+"</div><div class='pausetime' seg='"+tmppauseseg[1]+"' mot='"+tmppausem[1]+"'>"+tmppausedur[1]+"</div><div class='pausereason'>"+tmppausem[1]+"</div</div>";
	                    		$('#res_asesores').append(agent_block);
	                    		break;
	                    	//OnPause OnCall
							case 3:
								agent_block="<div class='asesor incall' status='"+tmpstatus[1]+"'><div class='name'>"+tmpagent[1]+"</div><div class='calldetails' seg='"+tmpcallseg[1]+"'>"+tmpcaller[1]+" || "+tmpcalldur[1]+"</div><div class='callqueue'>"+tmpqueue[1]+"</div</div>";
	                    		$('#res_asesores').append(agent_block);
	                    		break;
	                    	//Outcall
							case 4:
								agent_block="<div class='asesor outcall' status='"+tmpstatus[1]+"'><div class='name'>"+tmpagent[1]+"</div><div class='calldetails' seg='"+tmpcallseg[1]+"'>"+tmpcaller[1]+" || "+tmpcalldur[1]+"</div><div class='callqueue'>"+tmpqueue[1]+"</div</div>";
	                    		$('#res_asesores').append(agent_block);
	                    		break;
	                    	default:
	                    		agent_block="<div class='asesor avail' status='"+tmpstatus[1]+"' seg='"+tmpcallseg[1]+"'><div class='name'>"+tmpagent[1]+"</div><div class='bottomT'>"+tmpcaller[1]+"<br>"+tmpcalldur[1]+"</div></div>";
	                    		$('#res_asesores').append(agent_block);
	                    		break;	
						}
							
						
	                    i++;
	                    
					}
				}
					
				
				try{ 
                	asesores(<?php echo "$dep, '$depart'"; ?>);
                }
				catch(err){
					$('#error').text(err);
				}
				
                
                lu=text.match("-lu- (.*) -lu-");
                $('#LU').text(lu[1]);
                formatBlocks();
            }
        }
        xmlhttp.open("GET",urlsend,true);
        xmlhttp.send();

    }
    
   function formatBlocks(){
   		if($('#waitval').text()>0){
		       if($('#waitval').text()>2){
		        $('#waiting').css('background','#E8C44F');
		       }
		       if($('#waitval').text()>5){
		        $('#waiting').css('background','#A80000');
		       }
		       if($('#waitval').text()<=2){
		        $('#waiting').css('background','#779ECB');
		       }
		   }
		   
		   if(parseInt($('#ahtval').attr('seg'))><?php echo $aht*1.2; ?>){
		       $('#aht').addClass('flash');
		   }else{
		   		$('#aht').removeClass('flash');
		   }
		
		   if($('#pauseval').text()>0){
		       if($('#pauseval').text()>3){
		        $('#pause').css('background','#E8C44F');
		       }
		       if($('#pauseval').text()>5){
		        $('#pause').css('background','#A80000');
		       }
		       if($('#pauseval').text()<=3){
		        $('#pause').css('background','#779ECB');
		       }
		   }
		
		   qlongest=$('#longvalq').attr('valor');
		   clongest=$('#longval').attr('valor');
		   if(clongest!=''){
		       /*setInterval(function(){
		           clongest=parseInt(clongest)+1;
		           $('#longval').html(clongest+" <span style='font-size:18px; margin:0'>seg.</span>");
		       },1000);*/
		   }else{
		        $('#longval').html("0 <span style='font-size:18px; margin:0'>seg.</span>");
		   }
		
		   if(qlongest!=''){
		       /*setInterval(function(){
		           qlongest=parseInt(qlongest)+1;
		           $('#longvalq').html(qlongest+" <span style='font-size:18px; margin:0'>seg.</span>");
		       },1000);*/
		   }else{
		        $('#longvalq').html("0 <span style='font-size:18px; margin:0'>seg.</span>");
		   }
		   
		   $('.calldetails').each(function(index){
		   		var seg=$(this).attr('seg');
		   		if(parseInt(seg)><?php echo $aht*1.2; ?>){
		   			$(this).addClass('flash');
		   		}
		   });

			$('.pausetime').each(function(index){
		   		var seg=$(this).attr('seg');
		   		var mot=$(this).attr('mot');
		   		switch(mot){
		   			case "Comida":
		   				if(parseInt(seg)><?php if($dep==8){echo (3600*1.2);}else{echo (1800*1.2);} ?>){
		   					$(this).addClass('flash');
		   				}		
		   				break;
		   			case "Pausa No Productiva":
		   				if(parseInt(seg)>300){
		   					$(this).addClass('flash');
		   				}
		   				break;
		   			case "ACW":
		   				if(parseInt(seg)>120){
		   					$(this).addClass('flash');
		   				}
		   				break;
		   			case "Charla con Supervisor":
		   				if(parseInt(seg)>3600){
		   					$(this).addClass('flash');
		   				}
		   				break;
		   				
		   		}
		   		
		   });


   }
});


</script>
<style>
body{
	zoom: .75;
}

.fila{
    width: 80%;
    height: 300px;
    margin:20px;
}

.resumen{
    margin: auto;
    float: left;
    width: 95%;
    padding: 3%;
    padding-top: 0;
    padding-bottom: 0;
}

.container{
    width: 428;
    height: 120px;
    border-radius: 15px;
    background: #779ECB;
    vertical-align:middle;
    float: left;
    margin: 7px;
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
    padding-top: 38px;
}

.longcalls{
    width: 471;
    height: 150px;
    border-radius: 15px;
    font-size: 30px;
    margin: 0 6 10 4;
    float: left;
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

.waits{
    width: 168;
    height: 125;
    border-radius: 15px;
    font-size: 30px;
    margin-left: 17px;
    margin-top: -4px;
    margin-bottom: 15px;
    float: left;
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
    border-radius: 3px;
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
    color: black;
}

.w_val{
	font-size: 60px;
	font-weight: bold;
	color: white;
	margin-top: 33px;	
}

.w_label{
	margin-top: -21px;	
}

.asesor{
    width: 314;
    height: 125;
    border-radius: 15px;
    font-size: 30px;
    margin-left: 15px;
    margin-top: 15px;
    float: left;
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
    border-radius: 3px;
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
    color: black;
}

.name{
    width: 100%;
    margin: 0;
    margin-top: 10;
    line-height: normal;
    text-align:center;
    font-size: 29px;
    float:left;
    color: black;
    /* Mozilla Firefox */ 
	background-image: -moz-linear-gradient(left, #f6f8f9 0%, #E5EBEE 20%, #D7DEE3 51%, #f5f7f9 100%);
	/* Webkit (Chrome 11+) */ 
	background-image: -webkit-linear-gradient(left, #f6f8f9 0%, #E5EBEE 20%, #D7DEE3 51%, #f5f7f9 100%);
	/* Webkit (Safari/Chrome 10) */ 
	background-image: -webkit-gradient(linear, left top, right top, color-stop(0, #f6f8f9), color-stop(20, #E5EBEE), color-stop(51, #D7DEE3), color-stop(100, #f5f7f9));
}

.calldetails{
    width: 314px;
    padding-top: 10px;
    padding-bottom: 10px;
    line-height: normal;
    text-align:center;
    font-size: 21px;
    float:left;
    
}

.callqueue{
    width: 314px;
    margin-top: 1px;
    line-height: normal;
    text-align:center;
    font-size: 30;
    float:left;
    
}

.pausetime{
    width: 314px;
    padding-top: 10px;
    padding-bottom: 10px;
    line-height: normal;
    text-align:center;
    font-size: 21px;
    float:left;
    
}

.pausereason{
    width: 314px;
    margin-top: 1px;
    line-height: normal;
    text-align:center;
    font-size: 30;
    float:left;
    
}

.availtime{
    width: 314px;
    padding-top: 10px;
    padding-bottom: 10px;
    line-height: normal;
    text-align:center;
    font-size: 21px;
    float:left;
    
}

.availtitle{
    width: 314px;
    margin-top: 1px;
    line-height: normal;
    text-align:center;
    font-size: 30;
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
<div class='titles'><?php echo "$depart // <lu id='LU'>".date('d-m-Y H:i:s'); ?></lu></div>
<br>
<div class='resumen'>
    <div class='container'><p id='agentsval' style='margin: 0;'><img src="/images/online.png" height="60" width="60"> <?php echo $data[' all selected ']['agentes']; ?></p><br><p style='font-size: 40px; color: black; margin-top: 0'>Conectados</p></div>
    <div class='container'><p id='availval' style='margin: 0;'><img src="/images/avail.png" height="60" width="60"> <?php echo $data[' all selected ']['disponibles']; ?></p><br><p style='font-size: 40px; color: black; margin-top: 0'>Disponibles</p></div>
    <div class='container' id='pause'><p id='pauseval' style='margin: 0;'><img src="/images/paused.png" height="60" width="60"> <?php echo $data[' all selected ']['pausa']; ?></p><br><p style='font-size: 40px; color: black; margin-top: 0'>Pausados</p></div>
    <div class='container' ><p id='inval' style='margin: 0;'><img src="/images/inbound.png" height="60" width="60"> <?php echo $data[' all selected ']['in']; ?></p><br><p style='font-size: 40px; color: black; margin-top: 0'>Entrantes</p></div>
    <div class='container' ><p id='outval' style='margin: 0;'><img src="/images/outbound.png" height="60" width="60"> <?php echo $data[' all selected ']['out']; ?></p><br><p style='font-size: 40px; color: black; margin-top: 0'>Salientes</p></div>
    <div class='container' id='waiting'><p id='waitval' style='margin: 0;'><img src="/images/waiting.png" height="60" width="60"> <?php echo $data[' all selected ']['espera']; ?></p><br><p style='font-size: 40px; color: black; margin-top: 0'>En Espera</p></div>
    <div class='container' id='aht'><p id='ahtval' style='margin: 0;margin-bottom: -25; font-size:65px;'><?php echo number_format($avgaht,0); ?></p><br><p style='font-size: 40px; color: black; margin-top: 31;text-align: middle'><img src="/images/aht.png" height="60" width="60">AHT</p></div>
    <div class='container' id='longest'><p id='longval' valor='<?php echo $maxcall; ?>' style='margin: 0;margin-bottom: -25; font-size:65px;'><?php echo $maxcall; ?> <span style='font-size:18px; margin:31'>seg.</span></p><br><p style='font-size: 40px; color: black'>Longest Call</p></div>
    <div class='container' id='longestq'><p id='longvalq' valor='<?php echo $maxwait; ?>' style='margin: 0;margin-bottom: -25; font-size:65px;'><?php echo $maxwait; ?> <span style='font-size:18px; margin:31'>seg.</span></p><br><p style='font-size: 40px; color: black'>Longest Wait</p></div>
</div>
<br>
<div class='titles'>Espera en Colas</div>
<br>
<div class='resumen' id='res_esperas'>
<div class='waits' id="w_ventas"><p class='w_val' id='w_ventas_val'></p><p class='w_label'>Ventas</p></div>
<div class='waits' id="w_ventasmp"><p class='w_val' id='w_ventasmp_val'></p><p class='w_label'>Ventas MP</p></div>
<div class='waits' id="w_sac"><p class='w_val' id='w_sac_val'></p><p class='w_label'>SAC</p></div>
<div class='waits' id="w_upsell"><p class='w_val' id='w_upsell_val'></p><p class='w_label'>Upsell</p></div>
<div class='waits' id="w_tmp"><p class='w_val' id='w_tmp_val'></p><p class='w_label'>T MP</p></div>
<div class='waits' id="w_tmt"><p class='w_val' id='w_tmt_val'></p><p class='w_label'>T MT</p></div>
<div class='waits' id="w_agencias"><p class='w_val' id='w_agencias_val'></p><p class='w_label'>Agencias</p></div>
</div>
<br>
<div class='titles'>Status Asesores</div>
<br>
<div class='resumen' id='res_asesores'>

</div>


</body>