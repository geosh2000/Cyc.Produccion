<?php

session_start();
$this_page=$_SERVER['PHP_SELF'];
if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}

date_default_timezone_set('America/Mexico_City');
$credential="schedules_change";
$menu_programaciones="class='active'";
header("Content-Type: text/html;charset=utf-8");

?>

<?php
include("../connectDB.php");
include("../common/scripts.php");
include("../common/menu.php");

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
if(isset($_POST['submit'])){
     if(isset($_POST['showh'])){$show['horario']="checked";}else{$show['horario']="";}
     if(isset($_POST['showexc'])){$show['excepciones']="checked";}else{$show['excepciones']="";}
     if(isset($_POST['showret'])){$show['retardos']="checked";}else{$show['retardos']="";}
	 if(isset($_POST['showcom'])){$show['comidas']="checked";}else{$show['comidas']="";}
	if(isset($_POST['showext'])){$show['extras']="checked";}else{$show['extras']="";}
     $p_dep=$_POST['dep'];
}

if($p_dep!="all"){$sel_dep=" AND `id Departamento`='$p_dep' ";};


?>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-output.js"></script>
<link rel="stylesheet" href="/js/periodpicker/build/jquery.periodpicker.min.css">
<script src="/js/periodpicker/build/jquery.periodpicker.full.min.js"></script>
<script>
  $(function() {
    $('#from').periodpicker({
		end: '#to',
		lang: 'en',
		animation: true
	});

  });
  </script>
  <script>
  $(function() {
     $('.block').tooltip({
    	items: "[title]",
    	content: function(){
    		var element=$(this);
    		if(element.is("[title]")){
    			return element.attr('title');
    		}
    	}
    	
    });
    
    dialogLoad=$( "#dialog-load" ).dialog({
      modal: true,
      autoOpen: false
    });
    
    progressbarload=$('#progressbarload').progressbar({
	      value: false
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
	    
	    $('.sel_asesor').change(function(){
	    	var id=$(this).val();
	    	$('.sel_asesor').each(function(){
	    		if($(this).val()==id){
	    			$(this).val('').closest('tr').attr('asesor','');
	    		}
	    	});
	    	$(this).val(id).closest('tr').attr('asesor',id);
	    	
	    	
	    });
	    
	    function saveHorarios(){
	    	
	    	flag=true;
	    	done=true;
	    	
	    	$('.sel_asesor').each(function(){
	    		$(this).removeClass('error');
	    		if($(this).val()==''){
	    			flag=false;
	    			$(this).addClass('error');
	    		}
	    		
	    	});
	    	
	    	if(flag==true){
		    	$('.sel_asesor').each(function(){
		    		var horarios = $(this).closest('tr').find('.horarioid');
		    		var id = $(this).closest('tr').attr('asesor');
		    		var origs = $("#original_"+id).closest('tr').find('.horarioid');
		    		var sets="";
		    		var origsets="";
		    		$.each(horarios,function(){
			    		sets = sets+$(this).attr('horario_id')+",";
			    	});
			    	
			    	$.each(origs,function(){
			    		origsets = origsets+$(this).attr('horario_id')+",";
			    	});
			    	
		    		$.ajax({
		    			url: 'saveHorarios.php',
			            type: 'POST',
			            data: {asesor: id, horarios: sets, originales: origsets, inicio: <?php echo "'$from'"; ?>,fin: <?php echo "'$to'"; ?>},
			            dataType: 'html', // will automatically convert array to JavaScript,
			            cache: false,
			            success: function(data) {
			            	if(data!='OK'){
			            		done=false;
			            	}
			            }
		    		});
		    		
		    		
		    		
		    	});
		    	
		    	if(done){
		    		dialogLoad.dialog('close');
		    		alert('Done');
		    	}else{
		    		dialogLoad.dialog('close');
		    		alert('Error');
		    	}
		    }else{
		    	dialogLoad.dialog('close');
		    	alert("Todos los horarios deben estar asignados");
		    }
	    	
	    }
	    
	    $('#Save').click(function(){
	    	dialogLoad.dialog('open');
	    	saveHorarios();
	    });
	    
  });
</script>
  <style>
  .error{
	    background: #FFE8E0;
	    color: black;
	}
	
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

<table style='width:600px; margin:auto' class='t2'><form action="<?php $_SERVER['PHP_SELF'] ?>" method='post'>
    <tr class='title'>
        <th colspan=100 id='demotitle'>Consulta de Asistencia CC</th>
    </tr>
    <tr class='title'>
        <th>Periodo:</th>
        <td class='pair'><input type="text" id="from" name="from" value='<?php echo $from ?>' required><input type="text" id="to" name="to" value='<?php echo $to ?>' required></td>
        <th>Departamento:</th>
        <td class='pair'><select name="dep" required><option value=''>Selecciona</option>
        <?php
			$query="SELECT id, Departamento FROM PCRCs ORDER BY Departamento";
			if($result=$connectdb->query($query)){
				while($fila=$result->fetch_assoc()){
					echo "<option value='".$fila['id']."'";
						if($p_dep==$fila['id']){echo "selected";}
					echo ">".$fila['Departamento']."</option>\n\t";
				}
			}
        ?>
        <th>Mostrar</th>
        <td class='pair' style='text-align:right'><label for="showh">Horarios</label><input type="checkbox" id="showh" name="showh" <?php echo $show['horario'] ?>><br>
                        <label for="showexc">Excepciones</label><input type="checkbox" id="showexc" name="showexc" <?php echo $show['excepciones'] ?>><br>
                        <label for="showret">Retardos</label><input type="checkbox" id="showret" name="showret" <?php echo $show['retardos'] ?>><br>
                        <label for="showcom">Comidas</label><input type="checkbox" id="showcom" name="showcom" value='1' <?php echo $show['comidas'] ?>><br>
                        <label for="showcom">H. Extra</label><input type="checkbox" id="showext" name="showext" value='1' <?php echo $show['extras'] ?>>
        </td>
        <td class='total'><input type="submit" name="submit" value="consulta" /></td>
    </tr>
</form></table>

<br>

<?php
if(!isset($_POST['submit'])){exit;}

$query="SELECT a.id, Nombre, num_colaborador, Departamento, Egreso, Esquema FROM Asesores a LEFT JOIN PCRCs b ON a.`id Departamento`=b.id WHERE (Egreso >= '$from' AND Egreso IS NOT NULL) $sel_dep AND `id Departamento` NOT IN (29,30,31,33) ORDER BY Nombre";
if($result=$connectdb->query($query)){
	$field_count=$result->field_count;
	$fields=$result->fetch_fields();
	while($fila=$result->fetch_row()){
		for($x=0;$x<$field_count;$x++){
			$asesor[$fila[0]][$fields[$x]->name]=utf8_encode($fila[$x]);
		}
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
			."FROM Asesores a JOIN Fechas b WHERE Fecha BETWEEN '$from' AND '$to' $sel_dep "
			."AND `id Departamento` NOT IN (29,30,31,33) "
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
					a.id, Inicio, Fin, Ausentismo, Code,  DATEDIFF(Fin,Inicio) as Dias, Descansos, Beneficios, caso, Moper, Comments, `Last Update`, username
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
				(Fecha BETWEEN '$from' AND '$to')
				$sel_dep 
				AND `id Departamento` NOT IN (29,30,31,33)";
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
<button type="button" class='button button_green_w'id="Save">Save</button>
<br>
<table id='horarios' class='t2' style='text-align: center;'>
    <thead>
    <tr>
        <th>Asesor</th>
        <?php
        for($i=date('Y-m-d',strtotime($from));$i<=date('Y-m-d',strtotime($to));$i=date('Y-m-d',strtotime($i.' +1 day'))){
        	echo "\t\t<th>".date('D', strtotime($i))."<br>".$i."</th>\n";
        }
        ?>

    </tr>
    </thead>
    <tbody>
    	<?php
    		$query="SELECT id, Nombre FROM 
    				Asesores a 
    				WHERE (Egreso >= '$from' AND Egreso IS NOT NULL) AND `id Departamento`=$p_dep ORDER BY Nombre";
			if($selectAsesoresList=$connectdb->query($query)){
				while($fila=$selectAsesoresList->fetch_assoc()){
					$selectable[$fila['id']]=utf8_encode($fila['Nombre']);
				}
    		}
    		if(isset($asesor)){
    			foreach($asesor as $id => $info){
    				echo "<tr asesor='".$info['id']."' id='original_".$info['id']."'>";
						
						//Print Asesores
						echo "<td class='nombre'><select class='sel_asesor'><option value=''>Selecciona</option>";
						foreach($selectable as $selid => $selname){
							if($selid==$info['id']){
								$selected="selected";
							}else{
								$selected="";
							}
							
							echo "<option class='selas_$selid'  value='$selid' $selected>$selname</option>\n\t";
						}
						
						echo "</select></td>";
							
						//Print Bloques
						for($i=date('Y-m-d',strtotime($from));$i<=date('Y-m-d',strtotime($to));$i=date('Y-m-d',strtotime($i.' +1 day'))){
				        	echo "<td style='vertical-align: middle' class='add_excep horarioid' horario_id='".$info['Horarios'][$i]['id']."' fecha='$i' >";
							
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
												echo "<p class='block comida'>C: ".$js->format('H:i')." - ".$je->format('H:i')."</p>";
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
											if(isset($info['Sesiones'][$i]['Login'])){
													
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
															if($info2['Fin']=$i || $info['Inicio']==$i){
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
												if($info['Horarios'][$i]['Codigo']=='FA' || $info['Horarios'][$i]['Codigo']=='FJ'){
													if($info['Horarios'][$i]['Codigo']=='FA'){
														echo "<p class='block excepciones modif falta' title='".$info['Horarios'][$i]['Excepcion']."<br>Asignado por: ".$info['Horarios'][$i]['username']."<br>el dia: ".$info['Horarios'][$i]['Last Update']."<br>Caso: ".$info['Horarios'][$i]['caso']."<br>Notas: ".$info['Horarios'][$i]['Nota']."'>FA</p>";
													}else{
														echo "<p class='block excepciones modif' title='".$info['Horarios'][$i]['Excepcion']."<br>Asignado por: ".$info['Horarios'][$i]['username']."<br>el dia: ".$info['Horarios'][$i]['Last Update']."<br>Caso: ".$info['Horarios'][$i]['caso']."<br>Notas: ".$info['Horarios'][$i]['Nota']."'>FJ</p>";
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
<div id="dialog-load" title="Guardando Registro" style='text-align: center'>
	<div id="progressbarload"></div>
</div>
