<?php



//Query

$query="SELECT
		Fecha,
		SUM(Volumen) as Volumen,
		SUM(Answered) as Answered,
		SUM(AnsweredDept) as AnsweredDept,
		SUM(AnsweredPDV) as AnsweredPDV,
		SUM(AnsweredCYW) as AnsweredCYW,
		SUM(Abandoned) as Abandoned,
		SUM(Transferidas) as Transferidas,
		SUM(TransferidasMin) as TransferidasMin,
		SUM(SLA20Calls)/SUM(Volumen) as SLA20,
		SUM(SLA30Calls)/SUM(Volumen) as SLA30,
		SUM(SLA20Calls) as SLA20Calls,
		SUM(SLA30Calls) as SLA30Calls,
		SUM(Abandoned)/SUM(Volumen) as Abandon,
		SUM(TalkingTime)/SUM(Answered) as AHT,
		SUM(WaitingTime)/SUM(Answered) as ASA,
		SUM(TalkingTime) as TalkingTime,
		SUM(TalkingTimeDept) as TalkingTimeDept,
		SUM(WaitingTime) as WaitingTime,
		SUM(Localizadores)/(SUM(Answered)-SUM(TransferidasMin)) as FC,
		SUM(LocalizadoresDept)/(SUM(AnsweredDept)-SUM(TransferidasMinDept)) as FC_Departamento,
		SUM(LocalizadoresPDV)/(SUM(AnsweredPDV)-SUM(TransferidasMinPDV)) as FC_PDV,
		SUM(LocalizadoresCYW)/(SUM(AnsweredCYW)-SUM(TransferidasMinCYW)) as FC_CYW,
		SUM(Localizadores) as Localizadores,
		SUM(LocalizadoresDept) as LocalizadoresDept,
		SUM(LocalizadoresPDV) as LocalizadoresPDV,
		SUM(LocalizadoresCYW) as LocalizadoresCYW,
		SUM(Monto) as Monto,
		SUM(MontoDept) as MontoDept,
		SUM(MontoPDV) as MontoPDV,
		SUM(MontoCYW) as MontoCYW,
		1-(SUM(PNP)/SUM(DuracionSesiones)) as Utilizacion,
		(SUM(TalkingTimeDept)+SUM(PP))/(SUM(DuracionSesiones)-SUM(PNP)) as Ocupacion,
		SUM(PNP) as PNP,
		SUM(DuracionSesiones) as DuracionSesiones,
		SUM(PP) as PP,
		SUM(Adherencia) as Adherencia,
		SUM(AsesoresPrimerNivel) as AsesoresPrimerNivel,
		SUM(AsesoresApoyo) as AsesoresApoyo
	FROM
		(
			SELECT
				Fecha, Dolar
			FROM
				Fechas
			WHERE
				Fecha BETWEEN '$from' AND '$to'
			GROUP BY
				Fecha
		) D_Fechas
	LEFT JOIN
		(
			SELECT
				a.Fecha as Telefonia_Fecha, Skill,
				COUNT(ac_id) as Volumen,
				COUNT(IF(Answered=1,ac_id,NULL)) as Answered,
				COUNT(IF(Answered=1 AND `id Departamento`=$dept,ac_id,NULL)) as AnsweredDept,
				COUNT(IF(Answered=1 AND `id Departamento`=29,ac_id,NULL)) as AnsweredPDV,
				COUNT(IF(Answered=1 AND `id Departamento`=43,ac_id,NULL)) as AnsweredCYW,
				COUNT(IF(Answered=0,ac_id,NULL)) as Abandoned,
				COUNT(IF(Desconexion='Transferida',ac_id,NULL)) as Transferidas,
				COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:01:00',ac_id,NULL)) as TransferidasMin,
				COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:01:00' AND `id Departamento`=$dept,ac_id,NULL)) as TransferidasMinDept,
				COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:01:00' AND `id Departamento`=29,ac_id,NULL)) as TransferidasMinPDV,
				COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:01:00' AND `id Departamento`=43,ac_id,NULL)) as TransferidasMinCYW,
				COUNT(IF(Answered=1 AND Espera<='00:00:20',ac_id,NULL)) as SLA20Calls,
				COUNT(IF(Answered=1 AND Espera<='00:00:30',ac_id,NULL)) as SLA30Calls,
				SUM(IF(Answered=1, TIME_TO_SEC(Duracion_Real),0)) as TalkingTime,
				SUM(IF(Answered=1 AND `id Departamento`=$dept, TIME_TO_SEC(Duracion_Real),0)) as TalkingTimeDept,
				SUM(IF(Answered=1, TIME_TO_SEC(Espera),0)) as WaitingTime,
				COUNT(DISTINCT IF(`id Departamento`=$dept,asesor,NULL)) as AsesoresPrimerNivel,
				COUNT(DISTINCT IF(`id Departamento`!=$dept or `id Departamento` IS NULL,asesor,NULL)) as AsesoresApoyo
			FROM
				t_Answered_Calls a
			LEFT JOIN
				Cola_Skill b
			ON
				a.Cola=b.Cola
			LEFT JOIN
				Dids d
			ON
				a.DNIS=d.DID AND
				a.Fecha>=d.Fecha
			LEFT JOIN
				Asesores c
			ON
				a.asesor=c.id
			WHERE
				Skill=$dept AND
				a.Fecha BETWEEN '$from' AND '$to'
			GROUP BY
				a.Fecha
		) Telefonia
	ON
		Fecha=Telefonia_Fecha
	LEFT JOIN
		(
			SELECT
				Fecha as Localizadores_Fecha,
				CASE 
					WHEN $dept=3 THEN COUNT(IF(`id Departamento` IN (3,4,6,9,10,12,35) AND NOT Afiliado LIKE '%pricetravel%' AND NOT ((`id Departamento` IS NULL AND Afiliado LIKE '%agentes.pricetravel.com.mx%') OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND `id Departamento`!=5)),localizador,NULL))
					WHEN $dept=35 THEN COUNT(IF((`id Departamento` IN (3,4,6,9,10,12,35) AND Afiliado LIKE '%pricetravel%') OR ((`id Departamento` IN (3,4,6,9,10,12,35,29) OR (`id Departamento` IS NULL) OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND `id Departamento`!=5)) AND Afiliado LIKE '%agentes.pricetravel.com.mx%'),localizador,NULL))
					ELSE COUNT(IF(`id Departamento` IN ($dept) AND NOT Afiliado LIKE '%pricetravel%' AND NOT ((`id Departamento` IS NULL AND Afiliado LIKE '%agentes.pricetravel.com.mx%') OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND `id Departamento`!=5)),localizador,NULL))
				END  as Localizadores,
				CASE 
					WHEN $dept=3 THEN COUNT(IF(`id Departamento` IN (3) AND NOT Afiliado LIKE '%pricetravel%' AND NOT ((`id Departamento` IS NULL AND Afiliado LIKE '%agentes.pricetravel.com.mx%') OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND `id Departamento`!=5)),localizador,NULL))
					WHEN $dept=35 THEN COUNT(IF((`id Departamento` IN (35) AND Afiliado LIKE '%pricetravel%') OR ((`id Departamento` IN (35)) AND Afiliado LIKE '%agentes.pricetravel.com.mx%'),localizador,NULL))
					ELSE COUNT(IF(`id Departamento` IN ($dept) AND NOT Afiliado LIKE '%pricetravel%' AND NOT ((`id Departamento` IS NULL AND Afiliado LIKE '%agentes.pricetravel.com.mx%') OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND `id Departamento`!=5)),localizador,NULL))
				END  as LocalizadoresDept,
				CASE 
					WHEN $dept=3 THEN COUNT(IF(`id Departamento` IN (29) AND NOT Afiliado LIKE '%pricetravel%' AND NOT ((`id Departamento` IS NULL AND Afiliado LIKE '%agentes.pricetravel.com.mx%') OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND `id Departamento`!=5)),localizador,NULL))
					WHEN $dept=35 THEN COUNT(IF(((`id Departamento` IN (29) OR (`id Departamento` IS NULL)) AND Afiliado LIKE '%agentes.pricetravel.com.mx%'),localizador,NULL))
				END  as LocalizadoresPDV,
				CASE 
					WHEN $dept=3 THEN COUNT(IF(`id Departamento` IN (43) AND NOT Afiliado LIKE '%pricetravel%' AND NOT ((`id Departamento` IS NULL AND Afiliado LIKE '%agentes.pricetravel.com.mx%') OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND `id Departamento`!=5)),localizador,NULL))
					WHEN $dept=35 THEN COUNT(IF(((`id Departamento` IN (43)) AND Afiliado LIKE '%agentes.pricetravel.com.mx%'),localizador,NULL))
				END  as LocalizadoresCYW,
				CASE 
					WHEN $dept=3 THEN SUM(IF(`id Departamento` IN (3,4,6,9,10,12,35) AND NOT Afiliado LIKE '%pricetravel%' AND NOT ((`id Departamento` IS NULL AND Afiliado LIKE '%agentes.pricetravel.com.mx%') OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND `id Departamento`!=5)),Monto,NULL))
					WHEN $dept=35 THEN SUM(IF((`id Departamento` IN (3,4,6,9,10,12,35) AND Afiliado LIKE '%pricetravel%') OR ((`id Departamento` IN (3,4,6,9,10,12,35,29) OR (`id Departamento` IS NULL) OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND `id Departamento`!=5)) AND Afiliado LIKE '%agentes.pricetravel.com.mx%'),Monto,NULL))
				END  as Monto,
				CASE 
					WHEN $dept=3 THEN SUM(IF(`id Departamento` IN (3) AND NOT Afiliado LIKE '%pricetravel%' AND NOT ((`id Departamento` IS NULL AND Afiliado LIKE '%agentes.pricetravel.com.mx%') OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND `id Departamento`!=5)),Monto,NULL))
					WHEN $dept=35 THEN SUM(IF((`id Departamento` IN (35) AND Afiliado LIKE '%pricetravel%') OR ((`id Departamento` IN (35)) AND Afiliado LIKE '%agentes.pricetravel.com.mx%'),Monto,NULL))
				END  as MontoDept,
				CASE 
					WHEN $dept=3 THEN SUM(IF(`id Departamento` IN (29) AND NOT Afiliado LIKE '%pricetravel%' AND NOT ((`id Departamento` IS NULL AND Afiliado LIKE '%agentes.pricetravel.com.mx%') OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND `id Departamento`!=5)),Monto,NULL))
					WHEN $dept=35 THEN SUM(IF(((`id Departamento` IN (29) OR (`id Departamento` IS NULL)) AND Afiliado LIKE '%agentes.pricetravel.com.mx%'),Monto,NULL))
				END  as MontoPDV,
				CASE 
					WHEN $dept=3 THEN SUM(IF(`id Departamento` IN (43) AND NOT Afiliado LIKE '%pricetravel%' AND NOT ((`id Departamento` IS NULL AND Afiliado LIKE '%agentes.pricetravel.com.mx%') OR (Afiliado LIKE'%agentes.pricetravel.com.mx%' AND `id Departamento`!=5)),Monto,NULL))
					WHEN $dept=35 THEN SUM(IF(((`id Departamento` IN (43)) AND Afiliado LIKE '%agentes.pricetravel.com.mx%'),Monto,NULL))
				END  as MontoCYW
			FROM
				(
					SELECT
						Fecha, Localizador, asesor, `id Departamento`, Afiliado, SUM(VentaMXN+OtrosIngresosMXN+EgresosMXN) as Monto, SUM(Venta) as Venta
					FROM
							t_Locs a
					LEFT JOIN
						Asesores b
					ON
						a.asesor=b.id
					WHERE
						a.Fecha BETWEEN '$from' AND '$to'  AND
                        Afiliado NOT LIKE '%outlet%' AND
                        Venta!=0
					GROUP BY
						a.Fecha, localizador
					HAVING
						Monto!=0
				) Locs
			GROUP BY
				Fecha
		) Localizadores
	ON
		Fecha=Localizadores_Fecha
	LEFT JOIN
		(
			SELECT
				Fecha as Pausas_Fecha,
				SUM(IF(Skill=$dept AND Productiva=0,TIME_TO_SEC(Duracion),0)) as PNP,
				SUM(IF(Skill=$dept AND Productiva=1,TIME_TO_SEC(Duracion),0)) as PP
			FROM
				t_pausas a
			LEFT JOIN
				Tipos_pausas b
			ON
				a.codigo=b.pausa_id
			LEFT JOIN
				Asesores c
			ON a.asesor=c.id
			WHERE
				Fecha BETWEEN '$from' AND '$to' AND
				`id Departamento`=$dept
			GROUP BY
				Fecha
		) Pausas
	ON
		Fecha=Pausas_Fecha
	LEFT JOIN
		(
			SELECT
				Fecha_in as Sesiones_Fecha,
				SUM(IF(Skill=$dept,TIME_TO_SEC(Duracion),0)) as DuracionSesiones
			FROM
				t_Sesiones a
			LEFT JOIN
				Asesores c
			ON a.asesor=c.id
			WHERE
				Fecha_in BETWEEN '$from' AND '$to' AND
				`id Departamento`=$dept
			GROUP BY
				Fecha_in
		) Sesiones
	ON
		Fecha=Sesiones_Fecha
	LEFT JOIN
		(
			SELECT
				adh_s_fecha, IF(TotalSesiones/TiempoSesion>1,1,TotalSesiones/TiempoSesion) as Adherencia
			FROM
				(
					SELECT
						Fecha_in as adh_s_fecha, sum(TIME_TO_SEC(Duracion)) as TotalSesiones
					FROM
						t_Sesiones a
					LEFT JOIN
						Asesores b
					ON
						a.asesor=b.id
					WHERE
						Skill=$dept AND
						`id Departamento`=$dept AND
						Fecha_in BETWEEN '$from' AND '$to'
					GROUP BY
						adh_s_fecha
				) adh_s
			LEFT JOIN
				(
					SELECT
						a.Fecha as adh_p_fecha, SUM(TIME_TO_SEC(IF(`jornada end`<'09:00:00','24:00:00',`jornada end`))-TIME_TO_SEC(`jornada start`)) as TiempoSesion
					FROM
						`Historial Programacion` a
					LEFT JOIN
						Asesores b
					ON
						a.asesor=b.id
					LEFT JOIN
						Ausentismos c
					ON
						a.asesor=c.asesor AND
						a.Fecha BETWEEN c.Inicio AND c.Fin
					WHERE
						`id Departamento`=$dept AND
						a.Fecha < Egreso AND
						c.tipo_ausentismo IS NULL AND
						`jornada start`!=`jornada end` AND
						a.Fecha BETWEEN '$from' AND '$to'
					GROUP BY
						adh_p_fecha
				) adh_p
			ON
				adh_s_fecha=adh_p_fecha
		) adh_total
	ON
		Fecha=adh_s_fecha
	GROUP BY
		Fecha
    ORDER BY
        Fecha";


$result=mysql_query($query);
if(mysql_error()){
	echo mysql_error()." ON <br>$query<br>";
}
$num=mysql_numrows($result);
$numfield=mysql_num_fields($result);
$i=0;
while($i<$numfield){
    $field[$i]=mysql_field_name($result,$i);
$i++;
}
$i=0;
while($i<$num){
    $x=0;
     while($x<$numfield){
        $data[$field[$x]][$i]=mysql_result($result,$i,$field[$x]);
     $x++;
     }
     $TotalFechas=$i;
$i++;
}


?>



<div id="accordion">
  <h3>Informacion por dia</h3>
    <div>
    <table width='100%' class='tablesorter' id='tablesorter' style='text-align:center'>
    <thead>


<?php
    printRows('Fecha','M&eacutetrica','Canal','na','th');
?>
    </thead>
    <tbody>
<?php
    printRows('Volumen','Volumen','Total','num');
    printRows('Answered','Contestadas','Total','num');
    printRows('AnsweredDept','Contestadas (Departamento)','Total','num');
	printRows('AnsweredPDV','Contestadas (PDV)','Total','num');
	printRows('AnsweredCYW','Contestadas (Celaya)','Total','num');
    printRows('Abandoned','Abandonadas','Total','num');
    printRows('Transferidas','Transferidas','Total','num');
    printRows('TransferidasMin','Transferidas <1 min','Total','num');
    printRows('SLA20','SLA 20 seg','Total','%');
    printRows('SLA30','SLA 30 seg','Total','%');
    printRows('Abandon','Abandon %','Total','%');
    printRows('AHT','AHT','Total','num');
    printRows('ASA','ASA','Total','num');
    printRows('TalkingTime','Talking Time','Total','num');
	printRows('Localizadores','Localizadores','Total','num');
	printRows('LocalizadoresDept','Localizadores (Departamento)','Total','num');
	printRows('LocalizadoresPDV','Localizadores (PDV)','Total','num');
	printRows('LocalizadoresCYW','Localizadores (Celaya)','Total','num');
    printRows('FC','FC %','Total','%');
	printRows('FC_Departamento','FC % (Departamento)','Total','%');
	printRows('FC_PDV','FC % (PDV)','Total','%');
	printRows('FC_CYW','FC % (Celaya)','Total','%');
    printRows('Monto','Monto $','Total','$');
	printRows('MontoDept','Monto $ (Departamento)','Total','$');
	printRows('MontoPDV','Monto $ (PDV)','Total','$');
    printRows('MontoCYW','Monto $ (Celaya)','Total','$');
    printRows('Utilizacion','Utilizaci&oacuten','Total','%');
    printRows('Ocupacion','Ocupaci&oacuten','Total','%');
    printRows('Adherencia','Adherencia','Total','%');
    printRows('AsesoresPrimerNivel','Asesores conectados<br>First Level','Total','na');
    printRows('AsesoresApoyo','Asesores conectados<br>Apoyo','Total','na');
    
?>
            </tbody>
            </table>
        </div>
    <h3>Acumulado de fechas Seleccionadas</h3>
        <div>
            <table width='100%' id='acumulado' style='text-align: center'>
                <thead>
                <tr class='title' style='text-align: left'>
                    <th>Concepto</th>
                    <th>Total</th>
                </tr>
                </thead>
                <tbody>
                <?php
                function printRowAc($name,$text){
                	echo "<tr><td>$name</td><td>$text</td></tr>\n\t";
                }
				
				printRowAc('Volumen',number_format(array_sum($data['Volumen']),0));
				printRowAc('Answered',number_format(array_sum($data['Answered']),0));
				printRowAc('Answered (Departamento)',number_format(array_sum($data['AnsweredDept']),0));
				printRowAc('Answered (PDV)',number_format(array_sum($data['AnsweredPDV']),0));
				printRowAc('Answered (Celaya)',number_format(array_sum($data['AnsweredCYW']),0));
				printRowAc('Abandoned',number_format(array_sum($data['Abandoned']),0));
				printRowAc('Transferidas',number_format(array_sum($data['Transferidas']),0));
				printRowAc('TransferidasMin',number_format(array_sum($data['TransferidasMin']),0));
				printRowAc('SLA20',number_format(array_sum($data['SLA20Calls'])/array_sum($data['Answered'])*100,2).'%');
				printRowAc('SLA30',number_format(array_sum($data['SLA30Calls'])/array_sum($data['Answered'])*100,2).'%');
				printRowAc('Abandon',number_format(array_sum($data['Abandoned'])/array_sum($data['Volumen'])*100,2).'%');
				printRowAc('AHT',number_format(array_sum($data['TalkingTime'])/array_sum($data['Answered']),2).' seg');
				printRowAc('ASA',number_format(array_sum($data['WaitingTime'])/array_sum($data['Volumen']),2).' seg');
				printRowAc('Localizadores',number_format(array_sum($data['Localizadores']),0));
				printRowAc('Localizadores (Departamento)',number_format(array_sum($data['LocalizadoresDept']),0));
				printRowAc('Localizadores (PDV)',number_format(array_sum($data['LocalizadoresPDV']),0));
				printRowAc('Localizadores (Celaya)',number_format(array_sum($data['LocalizadoresCYW']),0));
				printRowAc('FC',number_format(array_sum($data['Localizadores'])/(array_sum($data['Answered'])-array_sum($data['TransferidasMin']))*100,2).'%');
				printRowAc('FC (Departamento)',number_format(array_sum($data['LocalizadoresDept'])/(array_sum($data['AnsweredDept'])-array_sum($data['TransferidasMinDept']))*100,2).'%');
				printRowAc('FC (PDV)',number_format(array_sum($data['LocalizadoresPDV'])/(array_sum($data['AnsweredPDV'])-array_sum($data['TransferidasMinPDV']))*100,2).'%');
				printRowAc('FC (Celaya)',number_format(array_sum($data['LocalizadoresCYW'])/(array_sum($data['AnsweredCYW'])-array_sum($data['TransferidasMinCYW']))*100,2).'%');
				printRowAc('Monto','$'.number_format(array_sum($data['Monto']),2));
				printRowAc('Monto (Departamento)','$'.number_format(array_sum($data['MontoDept']),2));
				printRowAc('Monto (PDV)','$'.number_format(array_sum($data['MontoPDV']),2));
				printRowAc('Monto (Celaya)','$'.number_format(array_sum($data['MontoCYW']),2));
				printRowAc('Utilizacion',number_format(100-array_sum($data['PNP'])/array_sum($data['DuracionSesiones'])*100,2).'%');
				printRowAc('Ocupacion',number_format((array_sum($data['TalkingTimeDept'])+array_sum($data['PP']))/(array_sum($data['DuracionSesiones'])-array_sum($data['PNP']))*100,2).'%');
				printRowAc('AsesoresPrimerNivel',number_format(array_sum($data['AsesoresPrimerNivel'])/count($data['AsesoresPrimerNivel'],0)));
				printRowAc('AsesoresApoyo',number_format(array_sum($data['AsesoresApoyo'])/count($data['AsesoresApoyo']),0));
                  /*
				   * 
				   * 1-SUM(PNP)/SUM(DuracionSesiones) as Utilizacion,
		(SUM(TalkingTime)+SUM(PP))/(SUM(DuracionSesiones)-SUM(PNP)) as Ocupacion,
				   * 
				   * printRows_ac('Volumen','Volumen','Total','num');
                  printRows_ac('Answered','Contestadas','Total','num');
                  printRows_ac('Abandoned','Abandonadas','Total','num');
                  printRows_ac('Transferidas','Transferidas','Total','num');
                  printRows_ac('TransferidasMin','Transferidas <1 min','Total','num');
                  printRows_ac('SLA20','SLA 20 seg','Total','%');
                  printRows_ac('SLA30','SLA 30 seg','Total','%');
                  printRows_ac('Abandon','Abandon %','Total','%');
                  printRows_ac('AHT','AHT','Total','num');
                  printRows_ac('ASA','ASA','Total','num');
                  printRows_ac('TalkingTime','Talking Time','Total','num');
                  printRows_ac('Localizadores','Localizadores','Total','num');
                  printRows_ac('FC','FC %','Total','%');
                  printRows_ac('Monto','Monto $','Total','$');
                  printRows_ac('Utilizacion','Utilizaci&oacuten','Total','%');
                  printRows_ac('Ocupacion','Ocupaci&oacuten','Total','%');
                  printRows_ac('AsesoresPrimerNivel','Asesores conectados<br>First Level','Total','na');
                   printRows_ac('AsesoresApoyo','Asesores conectados<br>Apoyo','Total','na');
*/



                ?>

            </tbody>

            </table>



        </div>



</div>



