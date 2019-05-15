<?php
include_once("../modules/modules.php");

initSettings::start(true,'payroll');
initSettings::printTitle('Asistencia');
timeAndRegion::setRegion('Mex');
Scripts::periodScript('from' , 'to');

$connectdb=Connection::mysqliDB('CC');

$cun_time = new DateTimeZone('America/Bogota');

//Get Variables
if(isset($_POST['from'])){
	$from=date('Y-m-d',strtotime($_POST['from']));
}else{
	$from=date('Y-m-d', strtotime('-15 days'));
}

if(isset($_POST['to'])){
	$to=date('Y-m-d',strtotime($_POST['to']));
}else{
	$to=date('Y-m-d', strtotime('-1 days'));
}

$show['horario']="checked";
$show['excepciones']="checked";
$p_dep="all";
if(isset($_POST['dep'])){
     if(isset($_POST['showh'])){$show['horario']="checked";}else{$show['horario']="";}
     if(isset($_POST['showexc'])){$show['excepciones']="checked";}else{$show['excepciones']="";}
     if(isset($_POST['showret'])){$show['retardos']="checked";}else{$show['retardos']="";}
	 if(isset($_POST['showcom'])){$show['comidas']="checked";}else{$show['comidas']="";}
	if(isset($_POST['showext'])){$show['extras']="checked";}else{$show['extras']="";}
     $p_dep=$_POST['dep'];
}

if($p_dep!="all"){$sel_dep=" AND `id Departamento`='$p_dep' ";};

$tbody="<td>Periodo</td><td><input type='text' id='from' name='from' value='$from' required><input type='text' id='to' name='to' value='$to' required></td>"
		."<td>Departamento</td><td><select name='dep' required><option value=''>Selecciona</option>";
	$query="SELECT id, Departamento FROM PCRCs ORDER BY Departamento";
	if($result=$connectdb->query($query)){
		while($fila=$result->fetch_assoc()){
			$tbody.= "<option value='".$fila['id']."'";
				if($p_dep==$fila['id']){$tbody.= "selected";}
			$tbody.= ">".$fila['Departamento']."</option>\n\t";
		}
	}
$tbody.="<option value='all' ";
if($p_dep=="all"){$tbody.= "selected";}
$tbody.=">Todos</option></select></td><td>Mostar</td><td>"
		."<label for='showh'>Horarios</label><input type='checkbox' id='showh' name='showh'  ".$show['horario']."></td>
                        <td><label for='showexc'>Excepciones</label><input type='checkbox' id='showexc' name='showexc'  ".$show['excepciones']."></td>
                        <td><label for='showret'>Retardos</label><input type='checkbox' id='showret' name='showret'  ".$show['retardos']."></td>
                        <td><label for='showcom'>Comidas</label><input type='checkbox' id='showcom' name='showcom' value='1'  ".$show['comidas']."></td>
                        <td><label for='showcom'>Extras</label><input type='checkbox' id='showext' name='showext' value='1'  ".$show['extras']."></td>";

Filters::showFilter($_SERVER['PHP_SELF'], 'POST', 'submit', 'Consultar', $tbody);
?>

<script>
  $(function() {
     $(  '#0, sh:gt(0):lt(5000)' ).tooltip({

        track: true,
        show: {
            effect: "slideDown",
            delay: 250
        }
    });

    $('.block').tooltip({
    	items: "[title]",
    	content: function(){
    		var element=$(this);
    		if(element.is("[title]")){
    			return element.attr('title');
    		}
    	}

    });

    $('#horarios').tablesorter({
            theme: 'blue',
            sortList: [[0,0],[1,0]],
            headerTemplate: '{content}',
            stickyHeaders: "tablesorter-stickyHeader",
            cssChildRow : "tablesorter-childRow",
            // fix the column widths
            widthFixed: false,
            widgets: [ 'zebra','filter','output', 'stickyHeaders'],
            widgetOptions: {
               uitheme: 'jui',
               columns: [
                    "primary",
                    "secondary",
                    "tertiary"
                    ],
                columns_thead: true,
                filter_childRows: true,
                filter_columnFilters: true,
                filter_cssFilter: "tablesorter-filter",
                filter_functions: null,
                filter_hideFilters: false,
                filter_ignoreCase: true,
                filter_reset: null,
                filter_searchDelay: 300,
                filter_startsWith: false,
                filter_useParsedData: false,
                resizable: true,
                saveSort: true,
                stickyHeaders: "tablesorter-stickyHeader",
                 output_separator     : ',',         // ',' 'json', 'array' or separator (e.g. ';')
                  output_ignoreColumns : [],          // columns to ignore [0, 1,... ] (zero-based index)
                  output_hiddenColumns : false,       // include hidden columns in the output
                  output_includeFooter : true,        // include footer rows in the output
                  output_dataAttrib    : 'data-name', // data-attribute containing alternate cell text
                  output_headerRows    : true,        // output all header rows (multiple rows)
                  output_delivery      : 'd',         // (p)opup, (d)ownload
                  output_saveRows      : 'a',         // (a)ll, (v)isible, (f)iltered, jQuery filter selector (string only) or filter function
                  output_duplicateSpans: true,        // duplicate output data in tbody colspan/rowspan
                  output_replaceQuote  : '\u201c;',   // change quote to left double quote
                  output_includeHTML   : false,        // output includes all cell HTML (except the header cells)
                  output_trimSpaces    : true,       // remove extra white-space characters from beginning & end
                 output_wrapQuotes    : false,       // wrap every cell output in quotes
                  output_popupStyle    : 'width=580,height=310',
                  output_saveFileName  : 'nomina_cc_cun_<?php echo date('Ymd',strtotime($from))."-".date('Ymd',strtotime($to)); ?>.csv',
                  // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
                  output_encoding      : 'data:application/octet-stream;charset=utf8,'

            }
        });
         $('#Exporttable').click(function(){
	        $('#horarios').trigger('outputTable');

	    });

	    var element_chg;
	    var info=[];

	    $('.add_excep').click(function(){
	    	$('#case').removeClass( "ui-state-error" );
	    	element_chg=$(this);
	    	var name=element_chg.closest('tr').find('.nombre').text();
	    	var asesor_id=element_chg.closest('tr').attr('asesor');
	    	var fecha=element_chg.attr('fecha');

			info['horario_id']=element_chg.attr('horario_id');
			info['fecha']=element_chg.attr('fecha');
			info['asesor']=asesor_id;

	    	$('#name').val(name);
	    	$('#a_id').val(asesor_id);
	    	$('#date').val(fecha);
	    	dialog.dialog('open');
	    });

	    function addExcep(tipo, caso, nota, name_excep){

				console.log("tipo: "+tipo +" || caso: "+ caso+" || nota: "+nota +" || name_excep: "+name_excep );

	    	var thisflag=true;

	    	if($("#case").attr("required")=='required'){
	    		if(caso==""){
	    			$('#case').addClass("ui-state-error");
	    			thisflag=false;
	    		}else{
	    			thisflag=true;
	    		}
	    	}

	    	if(thisflag){

		    	$.ajax({
		            url: "/common/submit_exception.php",
		            type: 'POST',
		            data: { asesor: info['asesor'], aplica : "<?php echo $_SESSION['id']; ?>", horario_id: info['horario_id'], tipo: tipo, caso: caso, nota: nota, fecha: info['fecha'], name_excep: name_excep},
		            dataType: 'html', // will automatically convert array to JavaScript
		            success: function(data) {
									console.log("asesor: "+ info['asesor']+", aplica : <?php echo $_SESSION['id']; ?>, horario_id:"+ info['horario_id']+", tipo: "+tipo+", caso: "+caso+", nota: "+nota+", fecha: "+info['fecha']+", name_excep: "+name_excep);

									datos=data;

		            	element_chg.find('.modif').remove();
		            	element_chg.find('.newexc').remove();
		            	if(data=="Success"){
		            		element_chg.append("<p class='block newexc'>"+name_excep+"</p>");
		            		dialog.dialog('close');
		            	}else{
		            		alert("Error en query "+data);
		            	}

					}

		        });


		    }
	    }

	    //Excepciones Dialog
	    dialog = $( "#dialog-form" ).dialog({
	      autoOpen: false,
	      height: 400,
	      width: 620,
	      modal: true,

	      buttons: {
	        "Enviar": function(){
	        	addExcep($('#excep').val(), $('#case').val(), $('#notes').val(), $('#excep option:selected').text());
	        },
	        Cancel: function() {
	            dialog.dialog( "close" );
	        }
	      },
	      close: function() {
	            form[0].reset();
	            $('#case').removeClass( "ui-state-error" );
	      }
	    });

	    $("#excep").change(function(){
	       switch($(this).val()){
	           case '3':
	           case '8':
	           case '12':
	            $("#case").attr("readonly",false);
	            $("#case").attr("required",true);
	            break;
	           default:
	            $("#case").attr("readonly",true);
	            $("#case").attr("required",false);
	            break;


	       }


	    });

	    form = dialog.find( "form" ).on( "submit", function( event ) {
	      event.preventDefault();
	      addUser();
	    });
  });
</script>
  <style>
    .ui-tooltip {
    width: 220px;
    height: auto;
    padding: 10px 20px;
    color: black;
    border-radius: 20px;
    font: bold 14px "Helvetica Neue", Sans-Serif;
    text-align: left;
    box-shadow: 0 0 7px black;
  }

  .block{
  	margin: auto;
  	width: 120px;
  }

  .horario{
  	background: #ddf3ff;
  }

  .comida{
  	background: #ebffdd;
  }

  .extra{
  	background: #f5ddff;
  }

  .excepciones{
  	background: #fcc4a6;
  }

  .asistencia{
  	background: #27db6c;
  }

  .falta{
  	background: #8e1700;
  	color: white;
  }

  .dt{
  	background: #fff02b;
  }

  .retardoA{
  	background: #c076c1;
  }

  .retardoB{
  	background: #a447a5;
  	color: white;
  }

  .retardoJ{
  	background: #4f47a5;
  	color: white;
  }

  .newexc{
  	background: yellow;
  	color: black;
  }
</style>



<br>

<?php
if(!isset($_POST['dep'])){exit;}

//$query="SELECT a.id, Nombre, num_colaborador, Departamento, Egreso, Esquema FROM Asesores a LEFT JOIN PCRCs b ON a.`id Departamento`=b.id WHERE (Egreso >= '$from' AND Egreso IS NOT NULL) $sel_dep AND `id Departamento` NOT IN (29,30,31,33) ORDER BY Nombre";

if($p_dep!="all"){
	$sel_dep="dep IN ($p_dep)";
}else{
	$sel_dep="dep NOT IN (29,30,31,33)";
}

$query="SELECT a.*, Departamento, Puesto
FROM
	(SELECT 
		a.id, a.Fecha, Nombre, num_colaborador, 
		dep,
		puestoOK as pues, 
		Egreso, Esquema 
	FROM 
		(SELECT c.Fecha, a.*, dep, b.puesto as puestoOK FROM Fechas c JOIN Asesores a LEFT JOIN dep_asesores b ON a.id=b.asesor AND c.Fecha=b.Fecha WHERE c.Fecha BETWEEN '$from' AND '$to' AND $sel_dep AND Egreso>='$from') a ) a
		LEFT JOIN PCRCs b ON a.dep=b.id 
		LEFT JOIN PCRCs_puestos c ON a.pues=c.id";
		
if($result=$connectdb->query($query)){
	$field_count=$result->field_count;
	$fields=$result->fetch_fields();
	while($fila=$result->fetch_row()){
		for($x=0;$x<$field_count;$x++){
			$asesor[$fila[0]][$fields[$x]->name]=utf8_encode($fila[$x]);

		}
		$depAsesor[$fila[0]][utf8_encode($fila[8])]=$fila[8];
		$puesAsesor[$fila[0]][utf8_encode($fila[9])]=$fila[9];
	}
}else{
	echo "Error al obtener información de los asesores -> ".$connectdb->error."<br>";
}
unset($result);

if($show['horario']=='checked' || $show['retardos']=='checked' || $show['excepciones']=='checked' || $show['comidas']=='checked' || $show['extras']=='checked'){
	$query="SELECT
				id, a.asesor, Fecha, `jornada start`, `jornada end`, `comida start`, `comida end`, `extra1 start`, `extra1 end`, `extra2 start`, `extra2 end`,
				Codigo, Excepcion, caso,  Nota, `Last Update`, username
			FROM
				`Historial Programacion` a
			LEFT JOIN
				PyA_Exceptions b ON a.id=b.horario_id AND a.asesor=b.asesor
			LEFT JOIN
				`Tipos Excepciones` c ON b.tipo=c.exc_type_id
			LEFT JOIN
				userDB d ON b.changed_by=d.userid
			WHERE
				Fecha BETWEEN '$from' AND '$to'";
	if($result=$connectdb->query($query)){
		$fields=$result->fetch_fields();
		while($fila=$result->fetch_array(MYSQLI_BOTH)){
			if(isset($asesor[$fila['asesor']])){
				for($x=0;$x<$result->field_count;$x++){
					$asesor[$fila['asesor']]['Horarios'][$fila['Fecha']][$fields[$x]->name]=utf8_encode($fila[$x]);
				}
			}
		}
	}else{
		echo "Error al obtener información de los horarios -> ".$connectdb->error."<br>";
	}

	if($show['retardos']=='checked' || $show['excepciones']=='checked'){
		$query="SELECT a.id, Fecha, LogAsesor(Fecha,a.id,'in') as Login, LogAsesor(Fecha,a.id,'out') as Logout "
			."FROM Asesores a JOIN Fechas b WHERE Fecha BETWEEN '$from' AND '$to' "
			//."HAVING (Login IS NOT NULL AND Logout IS NOT NULL)"
			."";
		if($result=$connectdb->query($query)){
			while($fila=$result->fetch_assoc()){
				if(isset($asesor[$fila['id']])){
					$asesor[$fila['id']]['Sesiones'][$fila['Fecha']]['Login']=$fila['Login'];
					$asesor[$fila['id']]['Sesiones'][$fila['Fecha']]['Logout']=$fila['Logout'];
				}
			}
		}else{
			echo "Error al obtener información de las Sesiones -> ".$connectdb->error."<br>";
		}
	}

	if($show['excepciones']=='checked'){
		$query="SELECT
					a.id, Inicio, Fin, Ausentismo, Code,  DATEDIFF(Fin,Inicio)+1 as Dias, Descansos, Beneficios, caso, Moper, Comments, `Last Update`, username
				FROM
					Fechas x
				JOIN
					Asesores a
				LEFT JOIN
					Ausentismos c ON c.asesor=a.id AND (x.Fecha BETWEEN Inicio AND Fin)
				LEFT JOIN
				   `Tipos Ausentismos` b ON c.tipo_ausentismo=b.id
				LEFT JOIN
					userDB d ON c.User=d.userid
				WHERE
				(Fecha BETWEEN '$from' AND '$to')";
		if($result=$connectdb->query($query)){
			$fields=$result->fetch_fields();
			while($fila=$result->fetch_array(MYSQLI_BOTH)){
				if(isset($asesor[$fila['id']])){
					for($x=0;$x<$result->field_count;$x++){
						$asesor[$fila['id']]['Excepciones'][$fila['Inicio']][$fields[$x]->name]=utf8_encode($fila[$x]);
					}
				}
			}
		}else{
			echo "Error al obtener información de los Excepciones -> ".$connectdb->error."<br>";
		}
	}
}

?>
<button type="button" class='button button_blue_w'id="Exporttable">Export</button>
<br>
<table id='horarios' class='t2' style='text-align: center;'>
    <thead>
    <tr>
        <th>Asesor</th><th>Colaborador</th><th>Departamento</th><th>Puesto</th>
        <?php
        for($i=date('Y-m-d',strtotime($from));$i<=date('Y-m-d',strtotime($to));$i=date('Y-m-d',strtotime($i.' +1 day'))){
        	echo "\t\t<th>".date('D', strtotime($i))."<br>".$i."</th>\n";
        }
        ?>

    </tr>
    </thead>
    <tbody>
    	<?php
    		if(isset($asesor)){
    			foreach($asesor as $id => $info){
    				echo "<tr asesor='".$info['id']."'>";

						//Print Asesores
						echo "<td class='nombre'>".$info['Nombre']."</td>"
							."<td>".$info['num_colaborador']."</td>"
							."<td>";

							//Deps
							$tmp="";
							foreach($depAsesor[$id] as $index => $depart){
								$tmp.="$index / ";
							}

							echo substr($tmp,0,-3);

							echo "</td>";

							//Puestos
							echo "<td>";
							$tmp="";
							foreach($puesAsesor[$id] as $index => $depart){
								$tmp.="$index / ";
							}

							echo substr($tmp,0,-3);

							echo "</td>";

						//Print Bloques
						for($i=date('Y-m-d',strtotime($from));$i<=date('Y-m-d',strtotime($to));$i=date('Y-m-d',strtotime($i.' +1 day'))){
				        	echo "<td style='vertical-align: middle' class='add_excep' horario_id='".$info['Horarios'][$i]['id']."' fecha='$i' >";

							//If Baja
							if(date('Y-m-d',strtotime($i))>date('Y-m-d',strtotime($asesor[$id]['Egreso']))){
								echo "BAJA";
							}else{

								//Print Horarios

									if(!isset($asesor[$id]['Horarios'][$i]['id'])){ //Check if captured, if not, display *
										echo "*";
									}else{
										if($info['Horarios'][$i]['jornada start']==$info['Horarios'][$i]['jornada end']){ //If js = je then DESCANSO
											echo "Descanso";
										}else{

											//Show Horarios
											if($show['horario']=='checked'){
												$js = new DateTime(date('Y-m-d', strtotime($i.' +0 day')).' '.$info['Horarios'][$i]['jornada start'].' America/Mexico_City');
												$js -> setTimezone($cun_time);
												$je = new DateTime(date('Y-m-d', strtotime($i.' +0 day')).' '.$info['Horarios'][$i]['jornada end'].' America/Mexico_City');
												$je -> setTimezone($cun_time);
												echo "<p class='block horario'>J: ".$js->format('H:i')." - ".$je->format('H:i')."</p>";
											}

											//Show Comidas
											if($show['comidas']=='checked'){

												$js = new DateTime(date('Y-m-d', strtotime($i.' +0 day')).' '.$info['Horarios'][$i]['comida start'].' America/Mexico_City');
												$js -> setTimezone($cun_time);
												$je = new DateTime(date('Y-m-d', strtotime($i.' +0 day')).' '.$info['Horarios'][$i]['comida end'].' America/Mexico_City');
												$je -> setTimezone($cun_time);
												if($info['Horarios'][$i]['comida start']!=$info['Horarios'][$i]['comida end']){
													echo "<p class='block comida'>C: ".$js->format('H:i')." - ".$je->format('H:i')."</p>";
												}else{
													echo "<p class='block comida'>C: N/A</p>";
												}
											}

											//Show Extras
											if($show['extras']=='checked'){
												if($info['Horarios'][$i]['extra1 start']!=$info['Horarios'][$i]['extra1 end']){//Check extra time
													$js = new DateTime(date('Y-m-d', strtotime($i.' +0 day')).' '.$info['Horarios'][$i]['extra1 start'].' America/Mexico_City');
													$js -> setTimezone($cun_time);
													$je = new DateTime(date('Y-m-d', strtotime($i.' +0 day')).' '.$info['Horarios'][$i]['extra1 end'].' America/Mexico_City');
													$je -> setTimezone($cun_time);
													echo "<p class='block extra'>X1: ".$js->format('H:i')." - ".$je->format('H:i')."</p>";
												}

												if($info['Horarios'][$i]['extra2 start']!=$info['Horarios'][$i]['extra2 end']){//Check extra time
													$js = new DateTime(date('Y-m-d', strtotime($i.' +0 day')).' '.$info['Horarios'][$i]['extra2 start'].' America/Mexico_City');
													$js -> setTimezone($cun_time);
													$je = new DateTime(date('Y-m-d', strtotime($i.' +0 day')).' '.$info['Horarios'][$i]['extra2 end'].' America/Mexico_City');
													$je -> setTimezone($cun_time);
													echo "<p class='block extra'>X2: ".$js->format('H:i')." - ".$je->format('H:i')."</p>";
												}
											}
										}

										//Show Retardos
										if($show['retardos']=='checked'){
											if(isset($info['Sesiones'][$i]['Login']) && $info['Horarios'][$i]['jornada start']!=$info['Horarios'][$i]['jornada end']){

												$login = new DateTime(date('Y-m-d', strtotime($i.' +0 day')).' '.date('H:i:s',strtotime($info['Sesiones'][$i]['Login'])).' America/Mexico_City');
												$login -> setTimezone($cun_time);

												if(date('H:i:s',strtotime($info['Sesiones'][$i]['Login']))>=date('H:i:s',strtotime($info['Horarios'][$i]['jornada start'].' +1 minutes'))){
													if($info['Horarios'][$i]['Codigo']=='RJ'){
														echo "<p class='block modif retardoJ' title='Login: ".$login->format('H:i:s')."<br><br>".$info['Horarios'][$i]['Excepcion']."<br>Asignado por: ".$info['Horarios'][$i]['username']."<br>el dia: ".$info['Horarios'][$i]['Last Update']."<br>Caso: ".$info['Horarios'][$i]['caso']."<br>Notas: ".$info['Horarios'][$i]['Nota']."'>RJ</p>";
													}else{
														if($info['Horarios'][$i]['Excepcion']!=NULL){
															$title="<br><br>".$info['Horarios'][$i]['Excepcion']."<br>Asignado por: ".$info['Horarios'][$i]['username']."<br>el dia: ".$info['Horarios'][$i]['Last Update']."<br>Caso: ".$info['Horarios'][$i]['caso']."<br>Notas: ".$info['Horarios'][$i]['Nota'];
														}else{
															$title="";
														}

														if(date('H:i:s',strtotime($info['Sesiones'][$i]['Login']))>=date('H:i:s',strtotime($info['Horarios'][$i]['jornada start'].' +13 minutes'))){
															echo "<p class='block modif retardoB' title='Login: ".$login->format('H:i:s')."$title'>RT-B</p>";
														}else{
															echo "<p class='block modif retardoA' title='Login: ".$login->format('H:i:s')."$title'>RT-A</p>";
														}
													}
												}
											}
										}

										//Show Excepciones
										if($show['excepciones']=='checked'){
											$flag=0;
											if(isset($info['Excepciones'])){

												foreach($info['Excepciones'] as $exc_start => $info2){
													if(date('Y-m-d',strtotime($i))>=date('Y-m-d', strtotime($info2['Inicio'])) && date('Y-m-d',strtotime($i))<=date('Y-m-d', strtotime($info2['Fin']))){

														$tmp_title=$info2['Ausentismo']."<br>Asignado por: ".$info2['username']."<br>".$info2['Last Update']."<br>Caso: ".$info2['Caso']."<br>Moper: ".$info2['Moper']."<br>Comments: ".$info2['Comments'];
														$print_aus="<p class='block excepciones' title='$tmp_title'>".$info2['Code']."</p>";

														//Validate Descansos & Beneficios
														if(($info2['Dias']-$info2['Descansos']-$info2['Beneficios'])<5){
															if($info2['Fin']==$i || $info['Inicio']==$i){
																if($info2['Descansos']!=0){
																	echo "<p class='block excepciones' title='$tmp_title'>D</p>";
																}else{
																	echo $print_aus;
																}
															}else{
																echo $print_aus;
															}
														}else{
															$v_difdays=date('z',strtotime($i))-date('z',strtotime($info2['Inicio']));
									                        $v_domingos=intval($v_difdays/7);
									                        $v_thisdow=$v_difdays%7;
									                        $v_tomadas=($v_difdays-intval($v_difdays/7))-(intval(($v_difdays-intval($v_difdays/7))/5));
									                        $v_ben_tom=intval($v_tomadas/5);


																switch($info['Esquema']){
																	case 10:
																		if((date('N',strtotime($i))==7 || date('N',strtotime($i))==6) && $v_domingos<$info2['Descansos']){
																			echo "<p class='block excepciones' title='$tmp_title'>D</p>";
																		}else{
																			echo $print_aus;
																		}
																		break;
																	default:
																		if(date('N',strtotime($i))==7 && $v_domingos<$info2['Descansos']){
												                            echo "<p class='block excepciones' title='$tmp_title'>D</p>";
												                        }else{
												                            	if($v_ben_tom<$info2['Beneficios'] && date('N',strtotime($i))==6){
												                                    echo "<p class='block excepciones' title='$tmp_title'>B</p>";
																				}else{
																					echo $print_aus;
																				}
																		}
																		break;
																}

														}

														//echo "<p class='block excepciones' title='$tmp_title'>".$info2['Code']."</p>";
														$flag=1;
													}
												}
											}

											//if(!isset($info['Sesiones'][$i]['Login']) && !isset($info['Sesiones'][$i]['Logout'])){
											if(!isset($info['Sesiones'][$i]['Login'])){
												if(date('Y-m-d')>date('Y-m-d',strtotime($i))){
													if($info['Horarios'][$i]['jornada start']!=$info['Horarios'][$i]['jornada end'] && $flag==0){
														if($info['Horarios'][$i]['Codigo']=='FA' || $info['Horarios'][$i]['Codigo']=='FJ'){
															if($info['Horarios'][$i]['Codigo']=='FA'){
																echo "<p class='block excepciones modif falta' title='".$info['Horarios'][$i]['Excepcion']."<br>Asignado por: ".$info['Horarios'][$i]['username']."<br>el dia: ".$info['Horarios'][$i]['Last Update']."<br>Caso: ".$info['Horarios'][$i]['caso']."<br>Notas: ".$info['Horarios'][$i]['Nota']."'>FA</p>";
															}else{
																echo "<p class='block excepciones modif' title='".$info['Horarios'][$i]['Excepcion']."<br>Asignado por: ".$info['Horarios'][$i]['username']."<br>el dia: ".$info['Horarios'][$i]['Last Update']."<br>Caso: ".$info['Horarios'][$i]['caso']."<br>Notas: ".$info['Horarios'][$i]['Nota']."'>FJ</p>";
															}
														}else{
															echo "<p class='block excepciones modif falta'>FA</p>";
														}
													}elseif($info['Horarios'][$i]['jornada start']==$info['Horarios'][$i]['jornada end'] && $flag==0){
														echo "<p class='block excepciones asistencia'>D</p>";
													}
												}
											}else{
												if($info['Horarios'][$i]['Codigo']=='FA' || $info['Horarios'][$i]['Codigo']=='FJ' || $info['Excepciones'][$i]['Code']=='FA' || $info['Excepciones'][$i]['Code']=='FJ'){
													if($info['Horarios'][$i]['Codigo']=='FA' || $info['Excepciones'][$i]['Code']=='FA'){
														if($info['Excepciones'][$i]['Code']=='FA'){
															//echo "<p class='block excepciones modif falta' title='".$info['Horarios'][$i]['Excepcion']."<br>Asignado por: ".$info['Horarios'][$i]['username']."<br>el dia: ".$info['Horarios'][$i]['Last Update']."<br>Caso: ".$info['Horarios'][$i]['caso']."<br>Notas: ".$info['Horarios'][$i]['Nota']."'>FA</p>";
														}else{
															echo "<p class='block excepciones modif falta' title='".$info['Horarios'][$i]['Excepcion']."<br>Asignado por: ".$info['Horarios'][$i]['username']."<br>el dia: ".$info['Horarios'][$i]['Last Update']."<br>Caso: ".$info['Horarios'][$i]['caso']."<br>Notas: ".$info['Horarios'][$i]['Nota']."'>FA</p>";
														}
													}else{
														if($info['Excepciones'][$i]['Code']=='FJ'){
															//echo "<p class='block excepciones modif falta' title='".$info['Horarios'][$i]['Excepcion']."<br>Asignado por: ".$info['Horarios'][$i]['username']."<br>el dia: ".$info['Horarios'][$i]['Last Update']."<br>Caso: ".$info['Horarios'][$i]['caso']."<br>Notas: ".$info['Horarios'][$i]['Nota']."'>FA</p>";
														}else{
															echo "<p class='block excepciones modif falta' title='".$info['Horarios'][$i]['Excepcion']."<br>Asignado por: ".$info['Horarios'][$i]['username']."<br>el dia: ".$info['Horarios'][$i]['Last Update']."<br>Caso: ".$info['Horarios'][$i]['caso']."<br>Notas: ".$info['Horarios'][$i]['Nota']."'>FA</p>";
														}

													}
												}else{
													if($info['Horarios'][$i]['jornada start']==$info['Horarios'][$i]['jornada end']){
														echo "<p class='block excepciones dt'>A</p>";
													}else{
														echo "<p class='block excepciones asistencia'>A</p>";
													}
												}
											}


										}




									}


							}

							echo "</td>\n";
				        }

					echo "</tr>\n";
    			}
    		}
    	?>
    </tbody>
</table>
<div id="dialog-form" title="Nueva Excepcion">
  <p class="validateTips">Fill the required Fields.</p>

  <form id='formulario'>
    <fieldset>
        <table width='550px'>
            <tr>
                <td width='30%'><label for="date">Fecha</label></td>
                <td><input type="text" name="date" id="date" value="" class="text ui-widget-content ui-corner-all" readonly>
                <input type="text" name="target" id="target" value="" hidden />
                <input type="text" name="hid" id="hid" value="" hidden /></td>
            </tr>
            <tr>
                <td width='30%'><label for="a_id">ID</label></td>
                <td><input type="text" name="a_id" id="a_id" value="" class="text ui-widget-content ui-corner-all" readonly></td>
            </tr>
            <tr>
                <td width='30%'><label for="name">Asesor</label></td>
                <td><input type="text" name="name" id="name" value="" class="text ui-widget-content ui-corner-all" readonly></td>
            </tr>
      <tr><td width='30%'><label for="excep">Excepcion</label></td>
      <td><select  class="option ui-widget-content ui-corner-all" name="excep" id="excep" required>
      	<?php
			echo "<option value='0'>Selecciona...</option>";
			$query="SELECT exc_type_id, Excepcion FROM `Tipos Excepciones` ORDER BY Excepcion";
			if($result=$connectdb->query($query)){
				while($fila=$result->fetch_assoc()){
					echo "<option value='".$fila['exc_type_id']."'>".$fila['Excepcion']."</option>";
				}
			}else{
				echo "Error: ".$connectdb->error;
			}
		?></select></td></tr>
      <tr><td width='30%'><label for="case">Caso</label></td>
      <td><input type="text" name="case" id="case" value="" class="text ui-widget-content ui-corner-all" required='true' readonly></td></tr>
      <tr><td width='30%'><label for="notes">Notas</label></td>
      <td><input type="text" name="notes" id="notes" value="" class="text ui-widget-content ui-corner-all" required='true'>
      <input type="text" name='flag' value='' id='flag' hidden />
      <input type="text" name='reg' value='' id='reg' hidden />
      <input type="text" name='user' value='' id='user' hidden />
      <input type="text" name='iden' value='' id='iden' hidden />
      </td></tr>
      </table>
      <!-- Allow form submission with keyboard without duplicating the dialog button -->
      <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
    </fieldset>
  </form>


</div>
<?php $connectdb->close(); ?>
