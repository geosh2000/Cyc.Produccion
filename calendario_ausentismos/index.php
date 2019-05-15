<?php
include_once("../modules/modules.php");

initSettings::start(true,'calendar_edit');
timeAndRegion::setRegion('Cun');
Scripts::periodScript('inicio', 'fin');

if(isset($_POST['start'])){ $from=date('Y-m-d', strtotime($_POST['start'])); }
if(isset($_POST['end'])){ $to=date('Y-m-d', strtotime($_POST['end'])); }

initSettings::printTitle("Editor de Calendarios de Ausentismos ($from a $to)");

$tbody="<td>Periodo</td><td><input type='text' name='start' id='inicio' value='$from' required><input type='text' name='end' id='fin' value='$to' required></td>";
Filters::showFilter($_SERVER['PHP_SELF'], 'POST', 'submit', 'Editar' , $tbody);

?>

<script>

$(function(){
	$('#calendar').tablesorter({
        theme: 'jui',
        headerTemplate: '{content} {icon}',
		tableClass: 'center',
	    widthFixed: false,
        widgets: [ 'zebra','columns','uitheme','filter', 'stickyHeaders', 'editable' ],
        widgetOptions: {

           //Sticky
            stickyHeaders_attachTo : '#container-calendar',
            
            
            columns_tfoot: false,
            columns_thead: true,
            filter_childRows: false,
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
            editable_columns       : "1-100",       // or "0-2" (v2.14.2); point to the columns to make editable (zero-based index)
            editable_enterToAccept : true,          // press enter to accept content, or click outside if false
            editable_autoAccept    : true,          // accepts any changes made to the table cell automatically (v2.17.6)
            editable_autoResort    : false,         // auto resort after the content has changed.
            editable_validate      : function(txt, orig, columnIndex, $element){
										
										if(/^\d+$/g.test(txt)){
                                        	t=true;
                                        }else{
                                        	t=false;
                                        }
                                        
                                       
                                        
                                        validation=t;
                                        
                                        if(t==false){

                                            new noty({
                                                text: "Cambio no realizado, "+txt+" no corresponde al formato ##",
                                                type: "error",
                                                timeout: 10000,
                                                animation: {
                                                    open: {height: 'toggle'}, // jQuery animate function property object
                                                    close: {height: 'toggle'}, // jQuery animate function property object
                                                    easing: 'swing', // easing
                                                    speed: 500 // opening & closing animation speed
                                                }
                                            });
                                             return orig;
                                        }else{
                                            return txt;
                                        }
                                      },   
                                             
            editable_focused       : function(txt, columnIndex, $element) {
              // $element is the div, not the td
              // to get the td, use $element.closest('td')
              $element.addClass('focused');
            },
            editable_blur          : function(txt, columnIndex, $element) {
              // $element is the div, not the td
              // to get the td, use $element.closest('td')
              $element.removeClass('focused');
            },
            editable_selectAll     : function(txt, columnIndex, $element){
              // note $element is the div inside of the table cell, so use $element.closest('td') to get the cell
              // only select everthing within the element when the content starts with the letter "B"
              return /^b/i.test(txt) && columnIndex === 0;
            },
            editable_wrapContent   : '<div>',       // wrap all editable cell content... makes this widget work in IE, and with autocomplete
            editable_trimContent   : true,          // trim content ( removes outer tabs & carriage returns )
            editable_noEdit        : 'no-edit',     // class name of cell that is not editable
            editable_editComplete  : 'editComplete' // event fired after the table content has been edited

        }
    }).children('tbody').on('editComplete', 'td', function(event, config){
       var $this = $(this),
        	newContent = $this.text(),
			date = $this.closest('tr').attr('fecha'),
			skill = $this.attr('skill'),
			element = $this.next().find('input'),
			open = 0;
			
			
		if($this.next().find('input:checked').length>0){
			open=1;
		}
		
        if(validation==true){
            sendRequest(newContent,date,skill, 'espacios', open);
        }

      $this.addClass( 'editable_updated' ); // green background + white text
      setTimeout(function(){
        $this.removeClass( 'editable_updated' );
      }, 500);

    });
    
    $('.open').change(function(){
    	var element = $(this),
        	newContent = element.closest('td').prev().text(),
			date = element.closest('tr').attr('fecha'),
			skill = element.closest('td').attr('skill'),
			open = 0;
    	
    	if(element.prop('checked')){
    		open=1;
    	}
    	
    	sendRequest(newContent,date,skill, 'abierto', open, element);
    })
    
    function sendRequest(espacios, date, skill, tipo, open, checkbox){
    	$.ajax({
		  type: "POST",
		  url: 'query.php',
		  data: {content: espacios, open: open, fecha: date, skill: skill, tipo: tipo, asesor: <?php echo $_SESSION['asesor_id']; ?>},
		  dataType: 'html',
		  success: function(data){
		  	
		  				if(data=='Success'){
			  				new noty({
	                            text: "Cambio aplicado para Fecha: "+date+" y skill: "+skill,
	                            type: "success",
	                            timeout: 1000,
	                            animation: {
	                                open: {height: 'toggle'}, // jQuery animate function property object
	                                close: {height: 'toggle'}, // jQuery animate function property object
	                                easing: 'swing', // easing
	                                speed: 500 // opening & closing animation speed
	                            }
	                        });
	                        
	                        if(data=='Succesi'){
	                        	checkbox.prop('checked',true);
	                        }
	                        
	                   }else{
	                   	
	                   		new noty({
	                            text: data,
	                            type: "error",
	                            timeout: 10000,
	                            animation: {
	                                open: {height: 'toggle'}, // jQuery animate function property object
	                                close: {height: 'toggle'}, // jQuery animate function property object
	                                easing: 'swing', // easing
	                                speed: 500 // opening & closing animation speed
	                            }
	                        });
	                   }
		  			},
		  	error: function(data){
		  				new noty({
                            text: "Error en comunicacion con query",
                            type: "error",
                            timeout: 1000,
                            animation: {
                                open: {height: 'toggle'}, // jQuery animate function property object
                                close: {height: 'toggle'}, // jQuery animate function property object
                                easing: 'swing', // easing
                                speed: 500 // opening & closing animation speed
                            }
                        });
		  			},
		  
		});
    }
	
});
		
		

</script>

<div id='flag' hidden>0</div>

<br>
<?php if(!isset($_POST['start'])){ exit; } 

//Departamentos
$query="SELECT Departamento, id FROM PCRCs WHERE main=1 ORDER BY Departamento";
		if($result=Queries::query($query)){
			while($fila=$result->fetch_assoc()){
				$deps[$fila['id']]=$fila['Departamento'];
			}
		}
		
//Ausentismos Disponibles
$query="SELECT * FROM ausentismos_calendario WHERE Fecha BETWEEN '$from' AND '$to'";
		if($result=Queries::query($query)){
			while($fila=$result->fetch_assoc()){
				$ausentismos[$fila['Fecha']][$fila['Departamento']]['espacios']=$fila['espacios'];
				$ausentismos[$fila['Fecha']][$fila['Departamento']]['abierto']=$fila['abierto'];
			}
		}

?>
<div id='container-calendar' style='width: 95%; margin: auto; height: 800px; overflow-y: auto; position: relative'>
<table id='calendar' style='text-align: center'>
	<thead>
		<tr style='text-align: center'>
			<th>Fecha</th>
			<th>DOW</th>
			<?php
				foreach($deps as $id => $departamento){
					echo "<th colspan=2>$departamento</th>\n\t";
				}
			?>
		</tr>
	</thead>
	<tbody>
		<?php
			for($i=date('Y-m-d',strtotime($from));$i<=date('Y-m-d',strtotime($to));$i=date('Y-m-d',strtotime($i.' +1 day'))){
				echo "<tr fecha='$i'>\n\t";
				echo "<td>$i</td>\n\t";
				echo "<td>".date('D',strtotime($i))."</td>\n\t";
					foreach($deps as $id => $departamento){
							
						if($ausentismos[$i][$id]['abierto']==1){
							$check='checked';
						}else{
							$check='';
						}
						
						if(isset($ausentismos[$i][$id]['espacios'])){
							$espacios=$ausentismos[$i][$id]['espacios'];
						}else{
							$espacios=0;
						}
						
						echo "<td skill='$id' tipo='espacios' class='espacios'>$espacios</td>"
							."<td skill='$id' tipo='open' class='no-edit'><input class='open' type='checkbox' $check></td>\n\t";
					}
				echo "</tr>";
			}
		?>
	</tbody>
</table>
</div>
