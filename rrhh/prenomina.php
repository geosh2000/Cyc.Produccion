<?php

include_once("../modules/modules.php");

//Ingresar permiso de prenomina
initSettings::start(true);

initSettings::printTitle('Prenomina Operaciones');

$connectdb=Connection::mysqliDB('Test');

$inicio='2017-05-19';
$fin='2017-06-05';

$query="DROP TEMPORARY TABLE IF EXISTS date_asesor;
        DROP TEMPORARY TABLE IF EXISTS log_asesor;
        DROP TEMPORARY TABLE IF EXISTS xtra_time;
        DROP TEMPORARY TABLE IF EXISTS prenomina;

        CREATE TEMPORARY TABLE date_asesor (SELECT a.Fecha, id, dep, c.puesto, c.esquema_vacante, num_colaborador as numcolaborador, Ingreso as Ingreso_, Egreso as Egreso_ FROM Fechas a JOIN Asesores b LEFT JOIN dep_asesores c ON a.Fecha=c.Fecha AND b.id=c.asesor WHERE a.Fecha BETWEEN '$inicio' AND '$fin' AND dep IS NOT NULL AND dep NOT IN (29,1));

        CREATE TEMPORARY TABLE log_asesor (SELECT 
          a.*, `jornada start`, `jornada end`, `comida start`, `comida end`, `extra1 start`, `extra1 end`, `extra2 start`, `extra2 end`,
          LogAsesor(a.Fecha, a.id, 'in') as login, LogAsesor(a.Fecha, a.id, 'out') as logout
        FROM 
          date_asesor a 
        LEFT JOIN 
          `Historial Programacion` b ON a.id=b.asesor AND a.Fecha=b.Fecha);
          

        CREATE TEMPORARY TABLE xtra_time (SELECT Fecha as xtra_fecha, asesor as xtra_asesor, 
        TIME_TO_SEC(CAST(ADDTIME(
          IF(x1_start!=x1_logout,ADDTIME(CAST(if(x1_logout<'05:00:00',ADDTIME(x1_logout,'24:00:00'),x1_logout) as TIME),-CAST(if(x1_start<'05:00:00',ADDTIME(x1_start,'24:00:00'),x1_start) as TIME)),'00:00:00'),
          IF(x2_start!=x2_logout,ADDTIME(CAST(if(x2_logout<'05:00:00',ADDTIME(x2_logout,'24:00:00'),x2_logout) as TIME),-CAST(if(x2_start<'05:00:00',ADDTIME(x2_start,'24:00:00'),x2_start) as TIME)),'00:00:00')) as TIME))/60/60 as total
        FROM
        (SELECT 
          a.Fecha, asesor, num_colaborador, Departamento, x1_inicio, x1_end, x2_login, x2_end, login, logout,
          IF(x1_inicio!=x1_end,IF(login<IF(x1_end<'05:00:00',ADDTIME(x1_end,'24:00:00'),x1_end) AND IF(logout<'05:00:00',ADDTIME(logout,'24:00:00'),logout)>=x1_inicio,IF(login<IF(x1_inicio<'05:00:00',ADDTIME(x1_inicio,'24:00:00'),x1_inicio),x1_inicio,login),NULL),NULL) as x1_start,
          
          IF(x1_inicio!=x1_end,
            IF(login<IF(x1_end<'05:00:00',ADDTIME(x1_end,'24:00:00'),x1_end) AND IF(logout<'05:00:00',ADDTIME(logout,'24:00:00'),logout)>=x1_inicio,
              IF(IF(logout<'05:00:00',ADDTIME(logout,'24:00:00'),logout)>IF(x1_end<'05:00:00',ADDTIME(x1_end,'24:00:00'),x1_end),
                x1_end,
                IF(logout>='24:00:00',ADDTIME(logout,'-24:00:00'),logout)),
              NULL),
            NULL) as x1_logout,
          
          IF(x2_login!=x2_end,IF(login<IF(x2_end<'05:00:00',ADDTIME(x2_end,'24:00:00'),x2_end) AND IF(logout<'05:00:00',ADDTIME(logout,'24:00:00'),logout)>=x2_login,IF(login<IF(x2_login<'05:00:00',ADDTIME(x2_login,'24:00:00'),x2_login),x2_login,login),NULL),NULL) as x2_start,
          IF(x2_login!=x2_end,IF(login<IF(x2_end<'05:00:00',ADDTIME(x2_end,'24:00:00'),x2_end) AND IF(logout<'05:00:00',ADDTIME(logout,'24:00:00'),logout)>=x2_login,IF(IF(logout<'05:00:00',ADDTIME(logout,'24:00:00'),logout)>IF(x2_end<'05:00:00',ADDTIME(x2_end,'24:00:00'),x2_end),x2_end,IF(logout>='24:00:00',ADDTIME(logout,'-24:00:00'),logout)),NULL),NULL) as x2_logout
        FROM 
          (
            SELECT Fecha, asesor, `extra1 start` as x1_inicio, `extra1 end` as x1_end, `extra2 start` as x2_login, `extra2 end` as x2_end
            FROM
              `Historial Programacion`
            WHERE 
              Fecha BETWEEN '$inicio' AND '$fin' AND
              `extra1 start` IS NOT NULL AND
              `extra1 start`!=`extra1 end`						
          ) a 
        LEFT JOIN 
          Asesores b 
        ON 
          a.asesor=b.id 
        LEFT JOIN 
          PCRCs c 
        ON 
          b.`id Departamento`=c.id 
        LEFT JOIN 
          log_asesor d
        ON 
          a.Fecha=d.Fecha AND
          a.asesor=d.id ) a
        );

        CREATE TEMPORARY TABLE prenomina (SELECT a.id,
          numcolaborador as CLAVE, NombreAsesor(a.id,2) as Nombre_del_empleado, 
          '' as Ubicacion, '' as Centro_de_costos, '' as Unidad_de_negocio,
          'Operaciones' as Area, Departamento, c.Puesto, 
          '' as Sueldo, '' as Fac,
          Ingreso_ as Ingreso, Egreso_ as Baja,  
          '' as Salario,
          '' as D_Faltas_JUS, SUM(IF(Code_aus='FJ',Ausentismo,0)) as F_Faltas_JUS,
          '' as D_Faltas_IN, SUM(IF(Code_aus='F',Ausentismo,0)) as F_Faltas_IN,
          '' as D_Suspension, SUM(IF(Code_aus='SUS',Ausentismo,0)) as F_Suspension,
          SUM(IF(Code_aus='INC_MT',Ausentismo,0)) as Maternidad, 
          SUM(IF(Code_aus='INC',Ausentismo,0)) as Enfermedad, 
          '' as Accidente, SUM(IF(Code_aus='INC_RT',Ausentismo,0)) as Acc_por_riesgo,
          '' as D_Permiso_sin_g, SUM(IF(Code_aus='PS',Ausentismo,0)) as F_Permiso_sin_g,
          '' as D_Permiso_con_g, SUM(IF(Code_aus='PC',Ausentismo,0)) as F_Permiso_con_g,
          '' as D_Vacaciones, SUM(IF(Code_aus='VAC',Ausentismo,0)) as F_Vacaciones,
          '' as Prima_Vacacional_1_SI, '' as Dias_de_prima_vac,
          SUM(horas_extra) as Horas_extra, '' as Horas_Extra2, '' as Horas_Extra3,
          '' as Dias_pendientes, SUM(IF((Asistencia=1 AND (Descanso=1 AND Code_aus IS NULL)) OR (Code_aus='DT' AND Asistencia=1),1,0)) as Descanso_Trabajado,
          SUM(IF(Code_aus='FES',Ausentismo,0)) as Dia_Festivo,
          '' as Prima_Dominical, '' as Subsidio_por_incapacidad, '' as Compensacion, '' as Comision, '' as Incentivo,
          '' as Bono, '' as Dias_Operados, '' as Ayuda_de_transporte, '' as Ayuda_de_renta,
          '' as Retroactivo, '' as Comedor, '' as Anticipos_de_Sueldo, '' as Descuento_Celular, '' as Otras_Deducciones,
          '' as Curso_ingles, '' as Descuento_empleado, '' as Optica_otras_deducciones,
          '' as Servicio_dental, '' as aportacion_voluntariado, '' as Responsabilidad, '' as Tarjeta_vales, '' as Observaciones
        FROM
          (SELECT 
            Fecha, a.id, dep, puesto, esquema_vacante, numcolaborador, Ingreso_, Egreso_,
            IF(`jornada start`=`jornada end`,1,0) as Descanso,
            CASE
              WHEN login IS NULL THEN 0
              WHEN login IS NOT NULL THEN 1
            END as Asistencia,
            CASE
              WHEN tipo_ausentismo IS NULL THEN 0
              ELSE 1
            END as Ausentismo,
            CASE
              WHEN tipo_ausentismo IS NOT NULL THEN
                CASE
                  WHEN Descansos=0 THEN c.Code
                  WHEN Descansos!=0 THEN
                  CASE
                    WHEN datediff(fin, inicio)<5 THEN IF(fin=Fecha OR inicio=Fecha,'D',c.Code)
                    ELSE CASE
                      WHEN esquema_vacante=10 THEN 
                        IF(WEEKDAY(Fecha)+1 IN (6,7) AND (FLOOR((DAYOFYEAR(Fecha)-DAYOFYEAR(inicio))/7))<Descansos,'D',c.Code)
                      ELSE
                        IF(WEEKDAY(Fecha)+1=7 AND (FLOOR((DAYOFYEAR(Fecha)-DAYOFYEAR(inicio))/7))<Descansos,'D',
                          IF((FLOOR(((DAYOFYEAR(Fecha)-DAYOFYEAR(inicio))-(FLOOR((DAYOFYEAR(Fecha)-DAYOFYEAR(inicio))/7))-FLOOR((DAYOFYEAR(Fecha)-DAYOFYEAR(inicio))-(FLOOR((DAYOFYEAR(Fecha)-DAYOFYEAR(inicio))/7))/5))/5))<Beneficios AND WEEKDAY(Fecha)+1=6,'B',IF(Fecha=fin AND Descansos-((DAYOFYEAR(Fecha)-DAYOFYEAR(inicio))-(FLOOR((DAYOFYEAR(Fecha)-DAYOFYEAR(inicio))/7))-FLOOR((DAYOFYEAR(Fecha)-DAYOFYEAR(inicio))-(FLOOR((DAYOFYEAR(Fecha)-DAYOFYEAR(inicio))/7))/5))>0,'D',c.Code)))
                      
                    END
                  END				
                END
            END as Code_aus,
            total as horas_extra
          FROM 
            log_asesor a
          LEFT JOIN 
            Ausentismos b ON a.id=b.asesor AND Fecha BETWEEN inicio AND fin
          LEFT JOIN
            `Tipos Ausentismos` c ON b.tipo_ausentismo=c.id
          LEFT JOIN
            xtra_time d ON a.id=xtra_asesor AND Fecha=xtra_fecha) a
        LEFT JOIN
          PCRCs b ON a.dep=b.id
        LEFT JOIN
          PCRCs_puestos c ON a.puesto=c.id
        GROUP BY 
          a.id);";

$i=0;
if($connectdb->multi_query($query)){

  do{
    //echo $i."<br>";
    $i++;
  } while (@$connectdb->next_result());
}else{
  echo "ERROR Multi! -> ".$connectdb->error;
}

$query="SELECT * FROM prenomina";
if($result=$connectdb->query($query)){
  $fields=$result->fetch_fields();
  while($fila=$result->fetch_array()){
    for($i=1;$i<$result->field_count;$i++){
      if($fields[$i]->type==246){
        if($fila[$i]==NULL || $fila[$i]==0){
          $value=number_format($fila[$i],0);
        }else{
          if($fila[$i] % intval($fila[$i])==0){
            $value=number_format($fila[$i],0);
          }else{
            $value=number_format($fila[$i],2);
          }
        }
      }else{
        $value=utf8_encode($fila[$i]);
      }
      $data[$fila[0]][$fields[$i]->name]=$value;
      $dataTable[$fila[0]][]=$value;
    }
  }
}else{
  echo "ERROR! -> ".$connectdb->error." ON $query<br>";
}

for($i=1;$i<$result->field_count;$i++){
  $headers[]=array('text'=>utf8_encode($fields[$i]->name)." (".$fields[$i]->type.")");
}

foreach($dataTable as $colab => $info){
  $row[]=$info;
}

$table=array('headers'=>array($headers), 'rows'=>$row);

$connectdb->close();

?>

<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-scroller.js"></script>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-output.js"></script>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-build-table.js"></script>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-editable.js"></script>
<script>
$(function(){
	
	flag=true;
	
	datos=<?php echo json_encode($table, JSON_PRETTY_PRINT); ?>;
	
	function printTable(){
		
		$('#result-table').tablesorter({
		    theme: 'jui',
		    headerTemplate: '{content} {icon}',
        widgets: ['zebra','columns','uitheme','filter', 'output' , 'stickyHeaders', 'editable'],
		    tableClass: 'center',
		    data: datos,
		    widgetOptions: {
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
          
          //Outputs
          output_separator     : ',',         // ',' 'json', 'array' or separator (e.g. ';')
          output_ignoreColumns : [0],          // columns to ignore [0, 1,... ] (zero-based index)
          output_hiddenColumns : false,       // include hidden columns in the output
          output_includeFooter : true,        // include footer rows in the output
          output_dataAttrib    : 'data-name', // data-attribute containing alternate cell text
          output_headerRows    : true,        // output all header rows (multiple rows)
          output_delivery      : 'd',         // (p)opup, (d)ownload
          output_saveRows      : 'a',         // (a)ll, (v)isible, (f)iltered, jQuery filter selector (string only) or filter function
          output_duplicateSpans: true,        // duplicate output data in tbody colspan/rowspan
          output_replaceQuote  : '\u201c;',   // change quote to left double quote
          output_includeHTML   : true,        // output includes all cell HTML (except the header cells)
          output_trimSpaces    : false,       // remove extra white-space characters from beginning & end
          output_wrapQuotes    : false,       // wrap every cell output in quotes
          output_popupStyle    : 'width=580,height=310',
          output_saveFileName  : 'Prenomina_Operaciones_<?php echo $inicio."a".$fin;?>.xls',
          // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
          output_encoding      : 'data:application/octet-stream;charset=utf8,',
          
          //Sticky
          stickyHeaders_attachTo : '#container-cuartiles'
		    }
	  	});
	  	
	  	
	}
	
	printTable();
	
	$('#export').click(function(){
		$('.tablesorter').trigger('outputTable');
	})
	
	
	

	
});
</script>

<div id='buttons' style='width: 90%; margin: auto; overflow: auto;'>
	<button id='export' class='button button_red_w'>Exportar</button> 
</div>
<div style='width: 90%; margin: auto; overflow: auto;'>
<div id='result-table'></div>

</div>