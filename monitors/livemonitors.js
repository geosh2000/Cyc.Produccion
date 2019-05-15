$(function(){

  //Links
  $('.waits').click(function(){
    window.location.replace(uri+$(this).attr('skill'));
  });


  function resizeDiv(){
        var w = parseInt($('body').css('width'));
        var new_w = w-parseInt($('#resumen_display').css('width'));
        $('#blocks_display').css('width',new_w*0.99);
    }

    resizeDiv();

    $( window ).resize(function() {
      resizeDiv();
    });

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

    var departamento = (typeof asesores[data['asesor']] === 'undefined') ? 'PDV' : asesores[data['asesor']];
    
    switch(departamento){
      case 'PDV':
        pdv = "<div class='pdv'>PDV</div>";
        break;
      case 'Apoyo':
        pdv = "<div class='apoyo'>Apoyo</div>";
        break;
      default:
        if(departamento.length>=12){
          departamento=departamento.substr(0,10)+"...";
        }else{
          departamento=departamento;
        }
        pdv="<div class='cc'>"+departamento+"</div>";
    }

		
		if(detalle.length>=20){
			detalle=detalle.replace("Servicio a Cliente","SAC").substr(0,18)+"...";
		}

		agent_block="<div class='asesor "+tipo+"' status='"+data['status']+"'>"+pdv+"<div class='comida'>"+data['comida']+"</div><div class='extension'>"+data['ext']+"</div><div class='name'>"+data['asesor']+"</div><div class='calldetails' seg='"+segundos+"' mot='"+motivo+"'>"+tiempo+"</div><div class='callqueue'>"+detalle+"</div</div>";

    switch(departamento){
      case 'PDV':
        $('#res_asesores_pdv').append(agent_block);
        break;
      case 'Apoyo':
        $('#res_asesores_apoyo').append(agent_block);
        break;
      default:
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
   			url: path+"qm_vars.php",
   			type: "GET",
   			data: {tipo: qType, skill: skill},
   			dataType: 'json',
   			success: function(array){
   						data=array;

   						//Clear Contents
   						$('#res_asesores, #res_asesores_pdv, #res_asesores_apoyo').empty();

						$.each(data['asesor'],function(index,value){
							printAsesor(value);
						});

            if(qType=='cola'){
              $('#monTitle').text(data['Title']);
            }

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
