<?php

include_once("../modules/modules.php");

initSettings::start(true,'calendar');
timeAndRegion::setRegion('Cun');

$year=2017;

if(isset($_GET['year'])){
	$year=$_GET['year'];
}

initSettings::printTitle("Calendario de Ausentismos $year");

$tbody="<td>Año</td><td><select id='year_sel'><option value='2017'>2017</option></select></td><td><select id='skill'><option value=''>Selecciona</option>";
	$query="SELECT id, Departamento FROM PCRCs WHERE main=1 ORDER BY Departamento";
	if($result=Queries::query($query)){
		while($fila=$result->fetch_assoc()){
			$tbody.= "<option value='".$fila['id']."'>".$fila['Departamento']."</option>\n\t";
		}
	}
$tbody.="</select></td>";
Filters::showFilterNOFORM('search', 'Consultar' , $tbody);



 ?>
 
<script>

$(function(){
	dialogLoadCal=$( "#dialog-loadcal" ).dialog({
      modal: true,
      autoOpen: false
    });
    
    progressbarloadcal=$('#progressbarloadcal').progressbar({
	      value: false
	});
	
	$('.tablesorter').tablesorter({
        theme: 'jui',
        headerTemplate: '{content} {icon}',
		tableClass: 'center',
	    widthFixed: false,
        widgets: [ 'zebra','columns','uitheme' ],
        widgetOptions: {

           //Sticky
            columns_tfoot: false,
            columns_thead: true,
            sortable: false
           
        }
    });
    
    function addDiv(month, day, tipo, value){
    	var miniblocks=$('#m'+month).find('.'+day).find('p');
    	var miniHeight=0;
    	
    	$.each(miniblocks,function(){
    		miniHeight+=$(this).outerHeight()+2;
    	});
    	
    	var trHeight=$('#m'+month).find('.'+day).closest('tr').height();
    	//alert(parseInt(miniHeight)+50+" || "+trHeight);
    	if((parseInt(miniHeight)+50)>trHeight){
    		$('#m'+month).find('.'+day).closest('tr').height(parseInt(miniHeight)+50);
    		
    	}
    	
    	$('#m'+month).find('.'+day).append("<p class='mini miniblock_"+tipo+"'>"+value+"</p>");
    	
    }
    
    function getCal(year, skill){
    	$.ajax({
    		url: 'getCal.php',
    		type: 'POST',
    		data: {year: year, skill: skill},
    		dataType: 'json',
    		success: function(array){
    			errflag=false;
    			data=array;
    			
    			try{
	    			$.each(data['cal'], function(month, days){
	    				$.each(days, function(day,vals){
	    					if(vals['abierto']==1){
	    						if(vals['espacios']<=0){
	    							addDiv(month,day,'cerrado','('+vals['espacios']+') disponibles');
	    						}else{
	    							addDiv(month,day,'disponible','('+vals['espacios']+') disponibles');	
	    						}
	    						
	    					}else{
	    						addDiv(month,day,'cerrado','Cerrado');
	    					}
	    				});
	    			});
	    			//dialogLoadCal.dialog('close');
    			
    			}catch(e){
    				errflag=true;
    				errormsg="No hay fechas configuradas para este skill / año";
    			}
    			
    			try{
	    			$.each(data['asig'], function(month, days){
	    				$.each(days, function(day,vals){
	    					$.each(vals, function(key,info){
	    						addDiv(month,day,'ocupado',info);		
	    					});
	    				});
	    			});
	    			
    			
    			}catch(e){
    				errflag=true;
    				errormsg2="No hay ausentismos asignados para este skill / año";
    			}
    			
    			dialogLoadCal.dialog('close');
    			if(errflag){
    				alert(errormsg+" || "+errormsg2);	
    			}
    			
    		},
    		error: function(){
    			dialogLoadCal.dialog('close');
    			alert('Error al recibir info');
    		}
    	});
    }
    
    $('#search').click(function(){
    	dialogLoadCal.dialog('open');
    	$('.mini').remove();
    	var year=$('#year_sel').val(),
    		skill=$('#skill').val();
    	getCal(year,skill);
    })
});
		
		

</script>
<style>
	.divin{
		width: 100%;
		height: 30px;
	}
	
	.month{
		display: inline-block;
		vertical-align: top;
		margin-left: 10px;
		margin-right: 10px;
		padding: 0;
		width: 820px;
	}
	
	.main{
		width: 1700px;
		margin: auto;
	}
	
	.miniblock_disponible{
		background: #04a038;
		min-height: 16px;
		margin: 2 0 0 0;
		color: white;
		cursor: hand;
	}
	
	.miniblock_ocupado{
		background: #2067ad;
		min-height: 16px;
		margin: 2 0 0 0;
		color: white;
		cursor: hand;
	}
	
	.miniblock_cerrado{
		background: #a8a8a8;
		min-height: 16px;
		margin: 2 0 0 0;
		color: black;
	}
</style>
 		<?php
 			function printCal($y,$m){
 					$from=date('Y-m-d',strtotime($y.'-'.$m.'-01'));
					
 				echo "<table class='tablesorter' style='width: 100%; margin: auto;'>
				 	<thead>
				 		<tr>
				 			<th colspan=100>".date('F Y',strtotime($from))."</th>
				 		</tr>
				 		<tr>
					 		<th>Lunes</th>
					 		<th>Martes</th>
					 		<th>Miercoles</th>
					 		<th>Jueves</th>
					 		<th>Viernes</th>
					 		<th>Sabado</th>
					 		<th>Domingo</th>
				 		</tr>
				 	</thead>
				 	<tbody>";	
					
			
 			$inicio = date('N',strtotime($from))-1;
			$start = date('Y-m-d',strtotime($from.' -'.$inicio.' days'));
			$indice=0;
			for($i=1;$i<=6;$i++){
				echo "<tr>";
					for($x=1;$x<=7;$x++){
						
						$thisd = date('Y-m-d',strtotime($start.' +'.$indice.' days'));
						
						if(date('m',strtotime($thisd))!=date('m',strtotime($from))){
							$color="#bcbcbc";
							$bg="#898989";
							$class_day="";
						}else{
							$color="black";
							$bg='white';
							$class_day=date('j',strtotime($thisd));
						}
						
						echo "<td style='color: $color; background: $bg; height: auto; width: 111;'>";
							echo "<div class='divin $class_day'>";
							echo "<p>".date('d',strtotime($thisd))."</p>";
							echo "</div>";
						echo "</td>";
						$indice++;
					}
				echo "</tr>";
				if(date('m',strtotime($start.' +'.$indice.' days'))!=date('m',strtotime($from))){
					break;
				}
			}
			echo " 	</tbody>
 			</table><br>";
 		}

?>

<br>
<?php
echo "<div class='main'>";
for($i=1;$i<=12;$i++){
	echo "<div id='m$i' class='month'>\n\t";
	printCal($year,$i);
	echo "</div>\n";
}
echo "</div>";
?>
 <div id="dialog-loadcal" title="Downloading Data" style='text-align: center'>
	<div id="progressbarloadcal"></div>
</div>
