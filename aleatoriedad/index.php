<?php

include_once("../modules/modules.php");

initSettings::start(true,'reportes_aleatoriedad');
initSettings::printTitle('Aleatoriedad');

timeAndRegion::setRegion('Cun');

//GET Variables

if(isset($_POST['inicio'])){$inicio=date('Y-m-d',strtotime($_POST['inicio']));}else{$inicio=date('Y-m-d', strtotime('-14 days'));}
if(isset($_POST['fin'])){$final=date('Y-m-d',strtotime($_POST['fin']));}else{$final=date('Y-m-d');}
$tipo=$_POST['tipo'];
$tx=$_POST['tx'];
$nivel=$_POST['nivel'];
$cant=$_POST['cantidad'];
$nivel_opt=$_POST['nivel_opt'];

function listOps($variable){

	//Progs
   	$query="SELECT * FROM PCRCs WHERE Parent=1 ORDER BY Departamento";
	if($result=Queries::query($query)){
		echo "<option value'' title='Programa'>Selecciona...</option>";
		while($fila=$result->fetch_assoc()){

        	echo "<option value='".$fila['id']."' title='Programa' ";
        	if($variable==$fila['id']){echo "selected";}
        	echo ">".$fila['Departamento']."</option>";
		}
	}

	//Sups
    $query="SELECT id, `N Corto`, getPuesto(id,CURDATE()) as PuestoOK FROM Asesores WHERE Egreso>CURDATE() HAVING PuestoOK IN (11,17,18,21) ORDER BY `N Corto`";
	if($result=Queries::query($query)){
		 echo "<option value'' title='Supervisor'>Selecciona...</option>";
		 while($fila=$result->fetch_assoc()){
			echo "<option value='".$fila['id']."' title='Supervisor' ";
        	if($variable==$fila['id']){echo "selected";}
        	echo ">".utf8_encode($fila['N Corto'])."</option>";
		}
	}


}

?>

<script>



$(function(){
   $("#calls, #cases").tablesorter({
            theme: 'blue',
            headerTemplate: '{content}',
            stickyHeaders: "tablesorter-stickyHeader",
            // fix the column widths
            widthFixed: true,
            widgets: [ 'zebra','output'],
            widgetOptions: {
               uitheme: 'jui',
                columns: [
                    "primary",
                    "secondary",
                    "tertiary"
                    ],
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
                stickyHeaders: "tablesorter-stickyHeader",
                output_separator     : ',',         // ',' 'json', 'array' or separator (e.g. ';')
                output_hiddenColumns : false,       // include hidden columns in the output
                output_includeFooter : true,        // include footer rows in the output
                output_dataAttrib    : 'data-name', // data-attribute containing alternate cell text
                output_headerRows    : true,        // output all header rows (multiple rows)
                output_delivery      : 'd',         // (p)opup, (d)ownload
                output_saveRows      : 'a',         // (a)ll, (v)isible, (f)iltered, jQuery filter selector (string only) or filter function
                output_duplicateSpans: true,        // duplicate output data in tbody colspan/rowspan
                output_replaceQuote  : '\u201c;',   // change quote to left double quote
                output_includeHTML   : false,        // output includes all cell HTML (except the header cells)
                output_trimSpaces    : false,       // remove extra white-space characters from beginning & end
                output_wrapQuotes    : false,       // wrap every cell output in quotes
                output_popupStyle    : 'width=580,height=310',
                output_saveFileName  : 'aleatoriedad_<?php echo "$inicioa$finalquery_did_on".date('Y-m-d H:i:s');?>.csv',
                // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
                output_encoding      : 'data:application/octet-stream;charset=utf8,'

            }
        });


   $('#inicio').periodpicker({
		end: '#fin',
		lang: 'en',
		animation: true
	});


   $('#nivel').change(function(){
         if($(this).data('options') == undefined){
            /*Taking an array of all options-2 and kind of embedding it on the select1*/
            $(this).data('options',$('#nivel_opt option').clone());
            }
            var act_id = $(this).val();
        var options = $(this).data('options').filter('[title=' + act_id + ']');
        $('#nivel_opt').html(options);
        $('#nivopt').show();
        $('#nivopttitle').show();
    });

   $('#exportcalls').click(function(){
           $('#calls').trigger('outputTable');
       });
    $('#exportcases').click(function(){
           $('#cases').trigger('outputTable');
        });
});
</script>

<table width='100%' class='t2'><form action="<?php $_SERVER['PHP_SELF']; ?>" method='POST'>
    <tr class='title'>
        <td>Inicio</td>
        <td>Tipo</td>
        <td>Nivel</td>
        <td  id='nivopttitle'>Sup/Prog</td>
        <td>Cantidad</td>
        <td rowspan=2 class='total'><input type="submit" name='consulta' id='consulta' value='Consultar'/></td>
    </tr>
    <tr class='title'>
        <td class='pair'><input type='text' name='inicio' id='inicio' value='<?php echo $inicio; ?>' required/><input type='text' name='fin' id='fin' value='<?php echo $final; ?>' required /></td>
        <td class='pair'><select name="tipo" id="tipo" required>
            <option value="">Selecciona...</option>
            <option value="1" selected>Calidad</option>

        </select></td>
        <td class='pair'><select name="nivel" id="nivel" required>
        <option value="">Selecciona...</option>
        <option value="Programa" <?php if($nivel=='Programa'){echo "selected";} ?>>Programa</option>
        <option value="Supervisor" <?php if($nivel=='Supervisor'){echo "selected";} ?>>Supervisor</option>
        </select></td>
        <td class='pair' id='nivopt'><select name="nivel_opt" id="nivel_opt"><?php listOps($nivel_opt); ?></select></td>
        <td class='pair'><input type="number" name='cantidad' id='cantidad' value='<?php echo $cant; ?>' required/></td>

    </tr>
</form></table>

<br><br>
<?php if(!isset($_POST['consulta'])){exit;}

if($tipo==1){

    switch($nivel){
        case 'Programa':
            $query="SELECT * FROM PCRCs WHERE id=$nivel_opt";
			if($result=Queries::query($query)){
				$fila=$result->fetch_assoc();
				$programa=$fila['Departamento'];
			}

			if($nivel_opt==6){
                $query="SELECT localizador as Fecha FROM bo_reembolsos WHERE date_created>='$inicio' AND date_created<=DATE_ADD('$final', INTERVAL 1 DAY)";
            }else{
                $query="SELECT Fecha, Llamante, Hora, AsteriskID FROM t_Answered_Calls a, Cola_Skill b, Asesores c
		                WHERE a.Cola=b.Cola AND a.asesor=c.id AND Fecha>='$inicio' AND Fecha<='$final'
		                AND b.Skill=$nivel_opt";
            }

			if($result=Queries::query($query)){
				$numCalls=$result->num_rows;
				$i=0;
				while($fila=$result->fetch_assoc()){
					$call[$i]=$fila['Fecha'];
					$call_caller[$i]=$fila['Llamante'];
					$call_asterisk[$i]=$fila['AsteriskID'];
					$call_hora[$i]=$fila['Hora'];

				$i++;
				}
			}

			$query="SELECT IF(em IS NULL, localizador, em) as caso FROM bo_tipificacion
            		WHERE CAST(date_created as DATE)>='$inicio' AND CAST(date_created as DATE)<='$final' AND status!=8";
            if($result=Queries::query($query)){
            	$numCasos=$result->num_rows;
				$i=0;
				if($nivel_opt==6){
					while($fila=$result->fetch_assoc()){
						$caso[$i]=$fila['caso'];
					$i++;
					}
				}
			}


            $tabla="<tr class='pair'><td width='10%'>$programa</td>";
            $tablacaso="<tr class='pair'><td width='10%'>$programa</td>";

            $i=1;
            while($i<=$cant){
                $temp=rand(0,$numCasos-1);
                $tablacaso.="<td>$caso[$temp]</td>";
            $i++;
            }

            $i=1;
            while($i<=$cant){
                $temp=rand(0,$numCalls-1);
                $tabla.="<td>$call[$temp] $call_hora[$temp]<br>$call_caller[$temp]<br>$call_asterisk[$temp]</td>";
            $i++;
            }

            $tabla.="</tr>";
            $tablacaso.="</tr>";
            break;

        case 'Supervisor':
            $query="SELECT * FROM Asesores WHERE id=$nivel_opt";
			if($result=Queries::query($query)){
				$fila=$result->fetch_assoc();
				$super=$fila['N Corto'];
			}else{
				echo "Error: ".Queries::error($query);
			}

            $query="SELECT * FROM
											(
												SELECT asesor, `N Corto`, AsteriskID, Fecha, Llamante, Hora, FindSuper(".date('m',strtotime($final)).",".date('Y',strtotime($final)).",a.asesor) as Super
													FROM
													(
														SELECT asesor, Cola, AsteriskID, Fecha, Llamante, Hora
														FROM t_Answered_Calls
														WHERE Fecha>='$inicio' AND Fecha<='$final'
													) a
													LEFT JOIN Cola_Skill b ON a.Cola=b.Cola
													LEFT JOIN Asesores c ON a.asesor=c.id
													HAVING Super='$super'
											) calls
											ORDER BY `N Corto`";

			if($result=Queries::query($query)){
				//echo $query."<br>";
				$numCalls=$result->num_rows;
				$i=0;
				$flag=0;
				while($fila=$result->fetch_assoc()){
					if($i==0){
						$ases[$flag]=$fila['N Corto'];
						$mincall[$flag]=$i;
					}

	                if($ases[$flag]!=$fila['N Corto']){
	                    $flag++;
	                    $ases[$flag]=$fila['N Corto'];
	                    $mincall[$flag]=$i;
	                }

	                $call[$i]=$fila['Fecha'];
	                $call_caller[$i]=$fila['Llamante'];
	                $call_asterisk[$i]=$fila['AsteriskID'];
	                $call_hora[$i]=$fila['Hora'];

	                $ases[$flag]=$fila['N Corto'];
	                $maxcall[$flag]=$i;
	            $i++;
				}
			}else{
				echo "Error: ".Queries::error($query);
			}

            $query="SELECT `N Corto`, IF(em IS NULL, localizador, em) as caso, FindSuperDay(DAY(date_created),MONTH(date_created),YEAR(date_created),asesor) as Super "
            			."FROM bo_tipificacion a LEFT JOIN Asesores b ON a.asesor=b.id "
            			."WHERE cast(date_created as DATE)>='$inicio' AND cast(date_created as DATE)<='$final' AND a.status!=8 "
            			."HAVING Super='$super' ORDER BY `N Corto`";

			if($result=Queries::query($query)){
				$numCasos=$result->num_rows;
				$i=0;
				$flag=0;
				while($fila=$result->fetch_assoc()){
					if($i==0){
						$asescaso[$flag]=$fila['N Corto'];
						$mincaso[$flag]=$i;
					}

	                if($asescaso[$flag]!=$fila['N Corto']){
	                    $flag++;
	                    $asescaso[$flag]=$fila['N Corto'];
	                    $mincaso[$flag]=$i;
	                }

	                $caso[$i]=$fila['caso'];
	                $asescaso[$flag]=$fila['N Corto'];
	                $maxcaso[$flag]=$i;
	            $i++;
				}
			}else{
				echo "Error: ".Queries::error($query);
			}



			if(isset($ases)){
	            foreach($ases as $key => $as){
	                $tabla.="<tr class='pair'><td width='10%'>$as</td>";
	                $i=1;
	                while($i<=$cant){
	                    $temp=rand($mincall[$key],$maxcall[$key]);
	                    $tabla.="<td>$call[$temp] $call_hora[$temp]<br>$call_caller[$temp]<br>$call_asterisk[$temp]</td>";
	                $i++;
	                }

	                $tabla.="</tr>";
	            }
	            unset($key,$as);
	        }

            if(isset($asescaso)){
	            foreach($asescaso as $key => $as){
        	        $tablacaso.="<tr class='pair'><td width='10%'>$as</td>";
                	$i=1;
                	while($i<=$cant){
	                    $temp=rand($mincaso[$key],$maxcaso[$key]);
        	            $tablacaso.="<td>$caso[$temp]</td>";
                	$i++;
	                }

        	        $tablacaso.="</tr>";
            	}
	    	}

            break;
    }
}

?>

<div style='text-align:right'><button class='button button_blue_w' id='exportcalls'>Export</button></div>
<table width='100%' id='calls' style='text-align: center'>
    <thead>
    <tr class='title'>

        <td>Programa /<br>Asesor</td>

        <?php

        $i=1;

        while($i<=$cant){
            if($nivel=='Programa' && $nivel_opt==6){$title="Localizador";}else{$title="Llamada";}
            echo "<th>$title $i</th>";

        $i++;
       }
        ?>
    </tr>
    </thead>
    <tbody>

    <?php
        echo $tabla;
    ?>
    </tbody>
</table>



<br><br>
<div style='text-align:right'><button class='button button_blue_w' id='exportcases'>Export</button></div>
<table width='100%' id='cases' style='text-align: center'>
    <thead>
    <tr class='title'>
        <td>Programa /<br>Asesor</td>
        <?php
        $i=1;
        while($i<=$cant){
            echo "<td>Caso $i</td>";
        $i++;
        }
        ?>
    </tr>
    </thead>
    <tbody>
    <?php
        echo $tablacaso;
    ?>
    </tbody>
</table>
