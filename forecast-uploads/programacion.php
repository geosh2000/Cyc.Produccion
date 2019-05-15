<?php

include_once("../modules/modules.php");

initSettings::start(true, 'payroll');
initSettings::printTitle('Programación de Horarios');
timeAndRegion::setRegion('Cun');

$connectdb=Connection::mysqliDB('CC');

if(isset($_POST['start'])){
	$inicio=date('Y-m-d', strtotime($_POST['start']));
}else{
	$inicio=date('Y-m-d', strtotime('-7 days'));
}

if(isset($_POST['end'])){
	$fin=date('Y-m-d', strtotime($_POST['end']));
}else{
	$fin=date('Y-m-d', strtotime('-1 days'));
}

if($_POST['copy']==1){
	if(isset($_POST['c_start'])){
		$c_inicio=date('Y-m-d', strtotime($_POST['c_start']));
	}else{
		$c_inicio=date('Y-m-d', strtotime('-7 days'));
	}

	if(isset($_POST['c_end'])){
		$c_fin=date('Y-m-d', strtotime($_POST['c_end']));
	}else{
		$c_fin=date('Y-m-d', strtotime('-1 days'));
	}
}

$skill=$_POST['skill'];
$draft=$_POST['fromdraft'];
$byPlace=$_POST['byplace'];

if($byPlace==1){
	$byPlaceCheck=" checked";
}

$tbody="<td>Periodo</td><td><input type='text' name='start' id='inicio' value='$inicio' required><input type='text' name='end' id='fin' value='$fin' required></td>"
		."<td>Programa</td><td><select name='skill' required><option value=''>Selecciona...</option>";
$query="SELECT * FROM PCRCs WHERE forecast=1 ORDER BY Departamento";
if($result=Queries::query($query)){
	while($fila=$result->fetch_assoc()){
		if($skill==$fila['id']){$selected="selected";}else{$selected="";}
		$tbody.= "<option value='".$fila['id']."' $selected>".$fila['Departamento']."</option>";
	}
}
$tbody.="</select></td><td>From Draft</td><td><input type='checkbox' value='1' name='fromdraft'></td><td>By Place</td><td><input type='checkbox' value='1' name='byplace' $byPlaceCheck></td>"
		."<td style='background: #962c2c;'>Copy</td><td style='background: #962c2c;'><input type='text' name='c_start' id='c_inicio' value='$c_inicio'><input type='text' name='c_end' id='c_fin' value='$c_fin'></td><td style='background: #962c2c;'><button class='button button_blue_w' id='b_copy'>Copy From</button></td><input type='hidden' value='0' name='copy' id='copy'>";
Filters::showFilter($_SERVER['PHP_SELF'], 'POST', 'submit', 'Consultar', $tbody);


if(isset($_POST['start']) || $_POST['copy']==1){
	$query="SELECT Departamento FROM PCRCs WHERE id=$skill";

	if($result=$connectdb->query($query)){
		$fila=$result->fetch_assoc();
		$depart=$fila['Departamento'];
	}else{
		echo $connectdb->error."<br> ON <br>$query<br>";
	}
	unset($result);

	//Data Asesores

	if($_POST['copy']==1){
		$q_inicio=$c_inicio;
	}else{
		$q_inicio=$inicio;
	}

	if($draft==1){
		$db="prog_draft";
		$db_id="a.asesor";
		$db_order="slot";
		$db_skill="AND skill='$skill'";
	}else{
		$db="`Historial Programacion`";
		$db_id="`Historial Programacion`";
		$db_order="`jornada start`, Nombre, a.dep, a.esquema_vacante";
		$db_skill="";
	}

	if($byPlace==1){
		$db_order='posicion';
	}

	$query="SELECT 
                a.asesor AS id,
                NOMBREASESOR(a.asesor, 1) AS 'N Corto',
                NOMBREASESOR(a.asesor, 2) AS Nombre,
                a.esquema_vacante AS Esquema,
                `jornada start`,
                `jornada end`,
                `comida start`,
                `comida end`,
                `extra1 start`,
                `extra1 end`,
                `extra2 start`,
                `extra2 end`,
                posicion,
                CASE
                    WHEN d.Ausentismo IS NOT NULL THEN IF(a=1, d.Ausentismo, IF(b=1,'Beneficio','Descanso'))
                    ELSE NULL
                END as Ausentismo,
                a.vacante AS Vacante,
                a.comida
            FROM
                dep_asesores a
                    LEFT JOIN
                $db b ON a.asesor = b.asesor
                    AND a.Fecha = b.Fecha $db_skill 
                    LEFT JOIN
                asesores_ausentismos c ON a.asesor = c.asesor
                    AND a.Fecha = c.Fecha
                    LEFT JOIN
                config_tiposAusentismos d ON c.ausentismo = d.id
                    LEFT JOIN
                horarios_position_select e ON a.asesor = e.asesor
                    AND IF(WEEK(a.Fecha, 1) = 0,
                    52,
                    WEEK(a.Fecha, 1)) = e.semana
                    AND IF(WEEK(a.Fecha, 1) = 0,
                    YEAR(a.Fecha) - 1,
                    YEAR(a.Fecha)) = e.year
            WHERE
                a.dep = $skill AND a.puesto IN (1,34)
                    AND a.vacante IS NOT NULL AND a.Fecha='$q_inicio'";
    
    $query.=" ORDER BY ".$db_order;

	if($result=$connectdb->query($query)){
		$info_field=$result->fetch_fields();
	   	while ($fila = $result->fetch_row()) {
			for($i=0;$i<$result->field_count;$i++){
				$asesor[$fila[0]][$info_field[$i]->name]=utf8_encode($fila[$i]);
			}
		}
	}else{
		echo $connectdb->error."<br> ON <br>$query<br>";
	}
	unset($result);
}

$totalAsesores=0;
foreach($asesor as $id => $info){
	if($info['Ausentismo']==NULL && date('H:i',strtotime($info['jornada start']))>date('H:i',strtotime('02:00:00'))){
		$totalAsesores++;
		$esquemas.=$info['Esquema'].",";
	}
}

substr($esquemas,0,-1);

//Selector Horarios
$query="SELECT * FROM prog_horariosDisponibles ORDER BY esquema, hora_inicio";
if($result_horarios=$connectdb->query($query)){
	while ($fila = $result_horarios->fetch_assoc()) {
		$selector_horarios.="<option value='".$fila['esquema']."_".$fila['hora_inicio']."' inicio='".$fila['hora_inicio']."' fin='".$fila['hora_fin']."'>(".$fila['esquema'].") ".$fila['horario']."</option>\n";
	}
}else{
	echo $connectdb->error."<br> ON <br>$query<br>";
}
unset($result);

$indexSelect=0;

function selectHorario($data){
	global $selector_horarios,$indexSelect;

	if($data['Ausentismo']!=NULL || date('H:i',strtotime($data['jornada start']))<date('H:i',strtotime('04:00:00'))){
		$indice="NO";
	}else{
		$indice=$indexSelect;
	}

	$result="<select class='select_h' id='sel_$indice'><option value=''>Selecciona...</option>";
	$result.=$selector_horarios;

	if($data['jornada start']!=NULL){
		if(date('i',strtotime($data['jornada start']))>0){
			$min_s=1;
		}else{
			$min_s=0;
		}

		if(date('i',strtotime($data['jornada end']))>0){
			$min_e=1;
		}else{
			$min_e=0;
		}

		$start=intVal(date('H',strtotime($data['jornada start'])))*2+$min_s;
		$end=intVal(date('H',strtotime($data['jornada end'])))*2+$min_e;

		/*if($end<10){
			if($start==0 && $end==0){

			}else{
				$end=$end+48;
			}
		}
		*/

		if($data['Ausentismo']!=NULL){
			$result.="<option value='default' inicio='0' fin='0' selected>*".$data['Ausentismo']."</option>\n";
		}else{
			$result.="<option value='default' inicio='$start' fin='$end' selected>*".date('H:i',strtotime($data['jornada start']))." - ".date('H:i',strtotime($data['jornada end']))."</option>\n";

		}

	}

	if($data['Ausentismo']!=NULL || date('H:i',strtotime($data['jornada start']))<date('H:i',strtotime('04:00:00'))){
	}else{
		$indexSelect++;
	}

	$result.="</select>";


	return $result;
}

function selectOther($data,$tipo){

	$result="<select class='$tipo' style='width: 97px;'><option value=''>$tipo...</option>";

	if($data[$tipo.' start']!=NULL){
		if(date('i',strtotime($data[$tipo.' start']))>0){
			$min_s=1;
		}else{
			$min_s=0;
		}

		if(date('i',strtotime($data[$tipo.' end']))>0){
			$min_e=1;
		}else{
			$min_e=0;
		}

		$start=intVal(date('H',strtotime($data[$tipo.' start'])))*2+$min_s;
		$end=intVal(date('H',strtotime($data[$tipo.' end'])))*2+$min_e;



		$result.="<option value='default' inicio='$start' fin='$end' selected>*".date('H:i',strtotime($data[$tipo.' start']))." - ".date('H:i',strtotime($data[$tipo.' end']))."</option>\n";
	}

	$result.="</select>";

	return $result;
}

//Parametros
function printHalfs($id,$ro="",$asesor="",$index=""){
	global $selector_horarios;

		if($ro==""){
			$slot="slot_horarios";
			$id_slot='id=sl_'.$index;
		}

		if($asesor!=""){
			switch($asesor['Departamento']){
				case 43:
					$color=" border: dotted 2px #7b1099;";
					break;
				case '-1':
					$color=" border: dotted 2px #ffc9d4;";
					break;

				default:
					$color="";
					break;
			}
		}

		echo "<div class='line_horarios $slot' style='$color' slot='$index' $id_slot>";
    
    if(isset($asesor['comida'])){
      if($asesor['comida']==1 || $asesor['comida']==NULL){
        $comidasAsesor=1;
      }else{
        switch($asesor['Esquema']){
          case '8':
            $comidasAsesor=0;
            break;
          case '10':
            $comidasAsesor=0;
          default:
            $comidasAsesor=1;
        }
      }
    }else{
      $comidasAsesor='NA';
    }

		if($ro!='readonly'){
			echo "<div set='$id' class='parametros' >"
				."<div class='param par_nombre' style='width: 194px;' id='pn_$id'><input id_asesor='".$asesor['id']."' esquema='".$asesor['Esquema']."' comida30='".$comidasAsesor."' type='text' value='".$asesor['N Corto']."' class='par_input' style='width: 194; height: 30px; border: 2px solid #ccc; border-radius: 5px;'></div>\n"
				."<div class='param par_esquema' style='width: 35px;' id='pe_$id'><input type='text' value='".$asesor['Esquema']."' class='par_input' style='width: 35; height: 30px; border: 2px solid #ccc; border-radius: 5px; text-align: center'></div>\n"
				."<div class='param par_jornada' style='width: 112px' id='pj_$id'>".selectHorario($asesor)."</div>\n"
				."<div class='param par_comida' id='pc_$id'>".selectOther($asesor,'comida')."</div>\n"
				."<div class='param par_x1' id='px1_$id'>".selectOther($asesor,'extra1')."</div>\n"
				."<div class='param par_x2' id='px2_$id'>".selectOther($asesor,'extra2')."</div>\n"
				."</div><div id='$id' style='display: inline-block'>";
		}else{
			echo "<div set='$id' class='parametros' style='text-align:right'>$id</div><div id='$id' style='display: inline-block'>";
		}



		if($ro!='readonly'){
			for($i=0;$i<48;$i++){
				if($i<10){
					$x=$i+48;
				}else{
					$x=$i;
				}
				echo "<input type='text' class='num_in h_$x' hora='$x'  readonly>";
			}
		}else{
			for($i=0;$i<48;$i++){
				if($i<10){
					$x=$i+48;
				}else{
					$x=$i;
				}
				echo "<input type='text' class='$id h_$x' hora='$x' $ro>";
			}
		}

		echo "</div>\n</div>\n\n";
}

?>
<script>
sum_ok=[];
dif_ok=[];
ned_ok=[];

	$(function(){
	
    left=window.screenX*5;
		winobj=window.open("prog_graph.php","_blank",'height=600,width=1300,status=yes,toolbar=no,menubar=no,location=no,left=' + left);
		var sw=true;

		dialogLoad=$( "#dialog-load" ).dialog({
	      modal: true,
	      autoOpen: false
	    });

	    progressbarload=$('#progressbarload').progressbar({
		      value: false
		});

		//Get Dynamic Info
		function sendRequest(){
	        $.ajax({
	            url: "qet_data.php",
	            type: 'POST',
	            data: { skill: "<?php echo $skill; ?>", start : "<?php echo $inicio; ?>", end: "<?php echo $inicio; ?>", submit: "1", tipo: "Needed"},
	            dataType: 'json', // will automatically convert array to JavaScript
	            success: function(array) {
	                var data=array;
	                needed_data=data;
	                //Dynamic Fields
	                $('.Needed').each(function(){
	                	var hora=$(this).attr('hora');
	                	if(hora>=48){
	                		hora=hora-48;
	                	}
	                	$(this).val(data[hora]);

	                });
				}

	        });
	    }

	    function getSuggest(){

	    	showLoader('Obteniendo Sugerencias');

	        $.ajax({
	            url: "test_asesores.php",
	            type: 'POST',
	            data: { skill: "<?php echo $skill; ?>", start : "<?php echo $inicio; ?>", end: "<?php echo $inicio; ?>", submit: "1", tipo: "Needed", tope: <?php echo $totalAsesores; ?>, esquemas: '<?php echo $esquemas; ?>'},
	            dataType: 'json', // will automatically convert array to JavaScript
	            success: function(array) {
	                var data=array;
	                suggest_data=data;
	                $.each(suggest_data,function(index,value){
                    var esquema=$('#sel_'+index).closest('.parametros').find('.par_nombre input').attr('esquema');
                    var comidas = $('#sel_'+index).closest('.parametros').find('.par_nombre input').attr('comida30');
                    if(comidas=='1' || comidas=='NA'){
                      var newEsquema=esquema+"_"+value;
                    }else{
                      if(parseInt(value)>=27){
                        var newVal=parseInt(value)+1;
                        var newEsquema=newEsquema="sc_"+esquema+"_"+newVal;
                      }else{
                        var newEsquema=newEsquema="sc_"+esquema+"_"+value;
                      } 
                    }
                    console.log(newEsquema);
                    $('#sel_'+index).val(newEsquema);
	                });
	                $('.comida').val('');
	                dialogLoad.dialog('close');
								},
							error: function( jqXHR, textStatus, errorThrown ) {
                dialogLoad.dialog('close');
                if (jqXHR.status === 0) {

                  showNoty('error','Not connect: Verify Network.',4000);

                } else if (jqXHR.status == 404) {

                  showNoty('error','Requested page not found [404]',4000);

                } else if (jqXHR.status == 500) {

                  showNoty('error','Internal Server Error [500].',4000);

                } else if (textStatus === 'parsererror') {

                  showNoty('error','Requested JSON parse failed.',4000);

                } else if (textStatus === 'timeout') {

                  showNoty('error','Time out error.',4000);

                } else if (textStatus === 'abort') {

                  showNoty('error','Ajax request aborted.',4000);

                } else {

                  showNoty('error','Uncaught Error: ' + jqXHR.responseText,4000);

                }

              }

	        });
	    }

	    sendRequest();

	    //Get default horarios
	    function startHorarios(xtra,flag){
	    	$('.select_h').each(function(){
	    		var id=$(this).parent().parent().attr('set');
				var inicio = $('option:selected', this).attr('inicio');
				var fin = $('option:selected', this).attr('fin');

				var tmp_flag = (typeof flag === 'undefined') ? 1 : flag;
				var tmp_xtra = (typeof xtra === 'undefined') ? 0 : xtra;

				setHoras(id,inicio,fin,tmp_xtra,tmp_flag);
			});

	    }

	    function init(){

	    }

	    //Funcion Sumar asesores asignados
		function sumAll(){
			sum=[];
			dif=[];
			ned=[];



			//Suma todo lo que tenca class .num_in
			$('.num_in').each(function(){
				var this_h=$(this).attr('hora');
				var real_h;
				if(this_h>=48){
					real_h=this_h-48;
				}else{
					real_h=this_h;
				}
				//showNoty('error',this_h);
				var tmp = (typeof sum[this_h] === 'undefined') ? 0 : sum[this_h];
				var need = (typeof $('#Needed .h_'+this_h).val() === 'undefined' || $('#Needed .h_'+this_h).val()=="") ? 0 : $('#Needed .h_'+this_h).val();
				var tmp_sum = (typeof $(this).val() === 'undefined' || $(this).val()=="") ? 0 : $(this).val();
				if(need==""){
					ned[this_h] = 0;
					ned_ok[real_h] = 0;
				}else{
					ned[this_h] = need;
					ned_ok[real_h] = parseInt(need);
				}
				sum[this_h] = parseInt(tmp) + parseInt(tmp_sum);
				sum_ok[real_h] = parseInt(tmp) + parseInt(tmp_sum);
				dif[this_h] = parseInt(need) - 	parseInt(sum[this_h]);
			});

			//Imprime totales
			$('.Total').each(function(){
				var this_h=$(this).attr('hora');
				$(this).val(sum[this_h]);
				$('#Difference .h_'+this_h).val(dif[this_h]);
			});

			if(typeof winobj === 'undefined'){

			}else if($('#flag').html()=="1"){
				if (winobj.closed) {
			        winobj=window.open("prog_graph.php","_blank","height=600,width=1300,status=yes,toolbar=no,menubar=no,location=no");
			    } else {
			        winobj.drawChart(ned_ok,sum_ok);
			    }
			}


		}

		//Cambia cuando se cambia un cuadro
		$('.num_in').keyup(function(){
			sumAll();
		});


		//Funcion para asignación de horarios predefinidos
		function setHoras(id, inicio, fin, xtra,flag){



				if(parseInt(fin)<10){
					if(inicio!=fin){
						fin=parseInt(fin)+48;
					}
				}

				var xtra = (typeof xtra === 'undefined') ? 0 : xtra;

				for (i = 0; i < 58; i++) {

					if(i>=inicio && i<fin){
				    	$('#'+id+' .h_'+i).val(1);
				    	if(xtra!=0){
				    		$('#'+id+' .h_'+i).addClass("xtra_assign");
				    		$('#'+id+' .h_'+i).attr("xtra"+xtra,1);
				    	}else{
				    		$('#'+id+' .h_'+i).removeClass("xtra_assign");
				    		$('#'+id+' .h_'+i).attr("extra1",0);
				   			$('#'+id+' .h_'+i).attr("extra2",0);
				    	}
				   }else{
				   		if(xtra!=0){
				   			if(xtra==1){
				   				if($('#'+id+' .h_'+i).attr("extra2")==1){
				   					$('#'+id+' .h_'+i).val("");
						   			$('#'+id+' .h_'+i).attr("extra1",0);
						   			$('#'+id+' .h_'+i).attr("extra2",0);
						   			$('#'+id+' .h_'+i).removeClass("xtra_assign");
				   				}
				   			}

				   			if($('#'+id+' .h_'+i).attr("xtra"+xtra)==1){
				   				$('#'+id+' .h_'+i).val("");
				   				$('#'+id+' .h_'+i).attr("xtra"+xtra,0);
				   				$('#'+id+' .h_'+i).removeClass("xtra_assign");
				   			}
				   		}else{
				   			$('#'+id+' .h_'+i).val("");
				   			$('#'+id+' .h_'+i).attr("extra1",0);
				   			$('#'+id+' .h_'+i).attr("extra2",0);
				   			$('#'+id+' .h_'+i).removeClass("xtra_assign");
				   		}
				   }
				}

				var tmp_flag = (typeof flag === 'undefined') ? 1 : flag;
				if(tmp_flag==1){
					if(sw){
						sumAll();
					}
				}

		}

		//Constructor de opciones para horas extra
		function setXOpt(id){
			var opts = [];
			var opt_start = [];
			var opt_end = [];
			var opt_dur = [];
			var start = $('#pj_'+id+' select option:selected').attr('inicio');
			var end = $('#pj_'+id+' select option:selected').attr('fin');

			var x=1;

			for(i=4;i>=1;i--){
				var hora = start-i;
				opts[x]=(hora/2)+' - '+(start/2);
				opt_start[x]=start-i;
				opt_end[x]=start;
				opt_dur[x]=i;
				x++;
			}

			x++;

			for(i=1;i<=4;i++){
				var hora = parseInt(end)+parseInt(i);
				opts[x]=(end/2)+' - '+(hora/2);
				opt_start[x]=end;
				opt_end[x]=parseInt(end)+parseInt(i);
				opt_dur[x]=i;
				x++;
			}

			/* Remove all options from the select list */
			$('#px1_'+id+' select').empty().append("<option value=''>X1...</option>");

			/* Insert the new ones from the array above */
			opts.forEach(function(item,index){
				$('#px1_'+id+' select').append("<option value='"+item+"' inicio='"+opt_start[index]+"' fin='"+opt_end[index]+"' dur='"+opt_dur[index]+"'>"+item+"</option>");
			});
		}

		function setXOpt2(id,dur){
			var opts = [];
			var opt_start = [];
			var opt_end = [];
			var opt_dur = [];
			var start = $('#pj_'+id+' select option:selected').attr('inicio');
			var end = $('#pj_'+id+' select option:selected').attr('fin');

			var x=1;

			if($('#px1_'+id+' select option:selected').attr('fin')==start){
				for(i=1;i<=(4-dur);i++){
					var hora = parseInt(end)+parseInt(i);
					opts[x]=(end/2)+' - '+(hora/2);
					opt_start[x]=end;
					opt_end[x]=parseInt(end)+parseInt(i);
					opt_dur[x]=i/2;
					x++;
				}
			}else{
				for(i=(4-dur);i>=1;i--){
					var hora = start-i;
					opts[x]=(hora/2)+' - '+(start/2);
					opt_start[x]=start-i;
					opt_end[x]=start;
					opt_dur[x]=i/2;
					x++;
				}
			}



			/* Remove all options from the select list */
			$('#px2_'+id+' select').empty().append("<option value=''>X2...</option>");

			/* Insert the new ones from the array above */
			opts.forEach(function(item,index){
				$('#px2_'+id+' select').append("<option value='"+item+"' inicio='"+opt_start[index]+"' fin='"+opt_end[index]+"'>"+item+"</option>");
			});
		}

		//Constructor Comidas
		function setComida(id){
			var opts = [];
			var opt_start = [];
			var opt_end = [];
			var start = $('#pj_'+id+' select option:selected').attr('inicio');
			var end = $('#pj_'+id+' select option:selected').attr('fin');
			var thisComida = $('#pn_'+id+' input').attr('comida30');


			if(parseInt(end)<10){
				end=parseInt(end)+48;
			}

			var dur=end-start;

			var x=1;

      if(thisComida!=0){

        if(dur>=13 && dur <=16){
          for(i=1;i<=5;i++){
            var hora = parseInt(start)+parseInt(i)+5;
            opts[x]=(hora/2)+' - '+((parseInt(hora)+1)/2)+' (0.5)';
            opt_start[x]=hora;
            opt_end[x]=parseInt(hora)+1;
            x++;
          }
        }else if(dur==20){
          for(i=1;i<=5;i++){
            var hora = parseInt(start)+parseInt(i)+7;
            opts[x]=(hora/2)+' - '+((parseInt(hora)+1)/2)+' (0.5)';
            opt_start[x]=hora;
            opt_end[x]=parseInt(hora)+1;
            x++;
          }

          for(i=1;i<=5;i++){
            var hora = parseInt(start)+parseInt(i)+7;
            opts[x]=(hora/2)+' - '+((parseInt(hora)+2)/2)+' (1.0)';
            opt_start[x]=hora;
            opt_end[x]=parseInt(hora)+2;
            x++;
          }
        }
			
			}

			/* Remove all options from the select list */
			$('#pc_'+id+' select').empty().append("<option value=''>Comida...</option>");

			/* Insert the new ones from the array above */
			opts.forEach(function(item,index){
				$('#pc_'+id+' select').append("<option value='"+item+"' inicio='"+opt_start[index]+"' fin='"+opt_end[index]+"'>"+item+"</option>");
			});

		}

		//Display Comida
		function printComida(id, inicio, fin,flag){

				$('#'+id+' .num_in').each(function(){
					if($(this).attr('comida')==1){
						$(this).val(1).attr('comida',0);
					}
				});

				for (i = 0; i < 58; i++) {
					if(i>=inicio && i<fin){
				    	$('#'+id+' .h_'+i).attr('comida',1).val(0);
				    	$('#'+id+' .h_'+i).addClass('comida_assign');
				   	}else{
				   		$('#'+id+' .h_'+i).removeClass('comida_assign');
				   	}
				}

				var tmp_flag = (typeof flag === 'undefined') ? 1 : flag;
				if(tmp_flag==1){
					if(sw){
						sumAll();
					}
				}


		}

		//Asigna horarios seleccionados
		$('.select_h').change(function(){
			dialogLoad.dialog('open');
			var id=$(this).parent().parent().attr('set');
			var inicio = $('option:selected', this).attr('inicio');
			var fin = $('option:selected', this).attr('fin');
			setHoras(id,inicio,fin);
			setXOpt(id);
			setXOpt2(id);
			setComida(id);
			dialogLoad.dialog('close');
		});

		//Asigna horarios extra1 seleccionados
		$('.extra1').change(function(){
			dialogLoad.dialog('open');
			var id=$(this).parent().parent().attr('set');
			var inicio = $('option:selected', this).attr('inicio');
			var fin = $('option:selected', this).attr('fin');
			var dur = $('option:selected', this).attr('dur');
			setHoras(id,inicio,fin,1);
			setXOpt2(id,dur);
			dialogLoad.dialog('close');
		});

		//Asigna horarios extra2 seleccionados
		$('.extra2').change(function(){
			dialogLoad.dialog('open');
			var id=$(this).parent().parent().attr('set');
			var inicio = $('option:selected', this).attr('inicio');
			var fin = $('option:selected', this).attr('fin');
			setHoras(id,inicio,fin,2);
			dialogLoad.dialog('close');
		});

		//Asigna comida seleccionada
		$('.comida').change(function(){
			dialogLoad.dialog('open');
			var id=$(this).parent().parent().attr('set');
			var inicio = $('option:selected', this).attr('inicio');
			var fin = $('option:selected', this).attr('fin');
			printComida(id,inicio,fin);
			dialogLoad.dialog('close');
		});



		function displayComidas(){
			$('.comida').each(function(){
				var original=$('option:selected', this);
				var id=$(this).parent().parent().attr('set');
				var inicio = $('option:selected', this).attr('inicio');
				var fin = $('option:selected', this).attr('fin');
				var id=$(this).parent().parent().attr('set');
				setComida(id);
				$(this).append(original);
				printComida(id,inicio,fin,0);
			});
		}

		function displayXtra(indice){
			$('.extra'+indice).each(function(){
				var original=$('option:selected', this);
				var id=$(this).parent().parent().attr('set');
				var inicio = $('option:selected', this).attr('inicio');
				var fin = $('option:selected', this).attr('fin');
				var dur = $('option:selected', this).attr('dur');
				setHoras(id,inicio,fin,indice,0);
				if(indice==1){
					setXOpt(id);
				}else{
					setXOpt2(id);
				}
				$(this).append(original);

			});
		}

		$('#b_start').click(function(){
			dialogLoad.dialog('open');
			startHorarios(0,0);
	    	sumAll();
	    	dialogLoad.dialog('close');
	    });

		$('#b_comidas').click(function(){
			dialogLoad.dialog('open');
			displayComidas();

			sumAll();
			dialogLoad.dialog('close');
		});

		$('#b_extra1').click(function(){
			dialogLoad.dialog('open');
			displayXtra(1);
			sumAll();
			dialogLoad.dialog('close');
		});

		$('#b_extra2').click(function(){
			dialogLoad.dialog('open');
			displayXtra(2);
			sumAll();
			dialogLoad.dialog('close');
		});

		$('#b_copy').click(function(){
			winobj.close();
			$('#copy').val(1);
			$('#form_consulta').submit();
		});

		$('#b_save').click(function(){
			savedraft();
		});

		$('#b_suggest').click(function(){
			getSuggest();
		});

		$('#b_apply').click(function(){
			savedraft(1);
		});

		$('#inicio').periodpicker({
			end: '#fin',
			lang: 'en',
			norange: true,
			animation: true
		});

		$('#b_res_extra').click(function(){
			$('.extra1, .extra2').each(function(){
				$(this).val("");
			})
		});


		$('#c_inicio').periodpicker({
			end: '#c_fin',
			lang: 'en',
			norange: true,
			animation: true
		});

		$('#submit').click(function(){
			winobj.close();
		});

		//Save draft
		function savedraft(apply){

			showLoader('Guardando Cambios');

			var ap_ch = (typeof apply === 'undefined') ? 0 : apply;
			var post_data={};
			var index=1;
			var url, msg_text;

			if(ap_ch==0){
				url="save_draft.php";
				msg_text="draft succesfully saved!";
			}else{
				url="apply_changes.php";
				msg_text="Schedules succesfully applied!";
			}

			save_msg=[];

			$('.slot_horarios').each(function(){
				var id_data={};
				id_data.id=$(this).find('.parametros .par_nombre .par_input').attr('id_asesor');
				id_data.js=$(this).find('.parametros .par_jornada .select_h option:selected').attr('inicio');
				id_data.je=$(this).find('.parametros .par_jornada .select_h option:selected').attr('fin');
				id_data.cs=$(this).find('.parametros .comida option:selected').attr('inicio');
				id_data.ce=$(this).find('.parametros .comida option:selected').attr('fin');
				id_data.x1s=$(this).find('.parametros .extra1 option:selected').attr('inicio');
				id_data.x1e=$(this).find('.parametros .extra1 option:selected').attr('fin');
				id_data.x2s=$(this).find('.parametros .extra2 option:selected').attr('inicio');
				id_data.x2e=$(this).find('.parametros .extra2 option:selected').attr('fin');

				post_data[index]=id_data;
				index++;
			});

			post_data['fecha']='<?php echo $inicio;?>';
			post_data['skill']='<?php echo $skill;?>';

	        $.ajax({
	            url: url,
	            type: 'POST',
	            data: post_data,
	            dataType: 'json', // will automatically convert array to JavaScript,
	            cache: false,
	            success: function(array) {
	            	var data=array;
	            	var error=0;
	            	$.each(data,function(index, value){
	            		if(value['msg']=='error'){
	            			$('#sl_'+index).addClass('error_update');
	            			error++;
	            		}else{
	            			$('#sl_'+index).removeClass('error_update');
	            		}
	            	});

								dialogLoad.dialog('close');

	            	if(error==0){
	            		new noty({
		                    text: msg_text,
		                    type: 'success',
		                    timeout: 5000,
		                    animation: {
		                        open: {height: 'toggle'}, // jQuery animate function property object
		                        close: {height: 'toggle'}, // jQuery animate function property object
		                        easing: 'swing', // easing
		                        speed: 500 // opening & closing animation speed
		                    }
		                });
	            	}
	            },
							error: function(){
								dialogLoad.dialog('close');
								showNoty('error','Error en conexión',4000);
							}

	        });
	    }

	    $('#b_pause').click(function(){
	    	if(sw){
	    		$(this).text('Render OFF');
	    		sw=false;
	    	}else{
	    		$(this).text('Render ON');
	    		sw=true;
	    	}
	    });


	});
function updateChart(){
	winobj.drawChart(ned_ok,sum_ok);
}
</script>
<style>
	.line_horarios{
		width: 1620px;
		margin: auto;
		background: #f7f7f7;
	}

	.parametros{
		display: inline-block;
		width: 650px;
	}

	.num_in, .Total, .header, .Needed, .Difference{
		width: 20px;
		height: 20px;
		text-align: center;
	}

	.Total{
		background: cyan;
	}

	.header{
		font-size: 9px;
	}

	.param{
		width: 97px;
		display: inline-block;
	}

	.xtra_assign{
		background: #f4b342;
	}

	.comida_assign{
		background: #93002e;
		color: white;
	}

	input[type='text']{
		padding: 0;
		border: 1px solid #ccc;
		border-radius: 0px;
	}

	.error_update{
		background: red;
	}

</style>
<div id='flag' hidden>0</div>


<br><br>
<div style='text-align: right'>
<button class='button button_blue_w' id='b_start'>Jornadas</button>
<button class='button button_blue_w' id='b_comidas'>Comidas</button>
<button class='button button_blue_w' id='b_extra1'>Extra 1</button>
<button class='button button_blue_w' id='b_extra2'>Extra 2</button>
<button class='button button_blue_w' id='b_suggest' style='background: #8342f4;'>Sugeridos</button>
<button class='button button_green_w' id='b_pause'>Render ON</button>
<button class='button button_blue_w' id='b_res_extra' style='background: #8342f4;'>Reset xtra</button>
<button class='button button_red_w' id='b_save'>Save draft</button>
<button class='button button_red_w' style='background: #f4e842; color: black; font-weight: bold' id='b_apply'>Apply</button>
</div>
<br>
<?php

	echo "<div class='line_horarios'>"
			."<div set='$header' class='parametros'>Hora</div><div id='$header' style='display: inline-block'>";

		for($i=0;$i<48;$i++){
			echo "<input type='text' class='header h_$i' value='".($i/2)."' hora='$i' $ro>";
		}

	echo "</div>\n</div>\n\n";

	/*for($i=1;$i<=20;$i++){
			printHalfs($i);
	}
	*/

	$i=1;

	foreach($asesor as $id => $info){
		printHalfs($id,"",$info,$i);
		$i++;
	}

	printHalfs('Total','readonly');
	printHalfs('Needed','readonly');
	printHalfs('Difference','readonly');
	echo "<div class='line_horarios'>"
			."<div set='$header' class='parametros'>Hora</div><div id='$header' style='display: inline-block'>";

		for($i=0;$i<48;$i++){
			echo "<input type='text' class='header h_$i' value='".($i/2)."' hora='$i' $ro>";
		}

	echo "</div>\n</div>\n\n";

	$connectdb->close();
?>
<div id='resultados'></div>
<div id="dialog-load" title="Sesión Finalizada" style='text-align: center'>
	<div id="progressbarload"></div>
</div>
