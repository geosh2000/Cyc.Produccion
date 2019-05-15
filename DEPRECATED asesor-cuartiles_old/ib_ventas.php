<?php
include("../connectDB.php");

//GET Variables
$dep=$_GET['pcrc'];
if(isset($_GET['from'])){$from=date('Y-m-d',strtotime($_GET['from']));}else{$from=date('Y-m-d',strtotime('-1 months'));}
if(isset($_GET['to'])){$to=date('Y-m-d',strtotime($_GET['to']));}else{$to=date('Y-m-d',strtotime('-1 days'));}
$perc_defined=0.8;

$query="SELECT * FROM grupos_cuartiles WHERE pcrc=$dep";
    $result=mysql_query($query);
    $num=mysql_numrows($result);
    $numfield_grupos=mysql_num_fields($result);
    $i=0;
    while($i<$numfield_grupos){
        $field_groups[$i]=mysql_field_name($result,$i);
    $i++;
    }
    $i=0;
    while($i<$num){
        $x=5;
        while($x<$numfield_grupos){
            $groups[mysql_result($result,$i,'pcrc')][mysql_result($result,$i,'tipo')][mysql_result($result,$i,'num')][mysql_result($result,$i,'q')][$field_groups[$x]]=mysql_result($result,$i,$field_groups[$x]);
            if($groups[mysql_result($result,$i,'pcrc')][mysql_result($result,$i,'tipo')][mysql_result($result,$i,'num')][mysql_result($result,$i,'q')][$field_groups[$x]]==1){
                $sel_gc[mysql_result($result,$i,'pcrc')][mysql_result($result,$i,'tipo')][mysql_result($result,$i,'num')][mysql_result($result,$i,'q')][$field_groups[$x]]=" checked";
            }
        $x++;
        }
    $i++;
    }

?>
<div id='accordion'>
    <h3>Configuracion de Grupos</h3>
    <div id='config-contain' style='height: 400px; overflow: scroll;padding:0; position: relative'>
    <table id='config' style='font-size:12px; vertical-align: middle'>
    <thead>
        <tr>
            <th>Grupo</th>
            <?php
                $b=5;
                while($b<$numfield_grupos){
                    echo "<td>";
                    echo "$field_groups[$b]";
                    echo "</td>\n";
                $b++;
                }
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
                $x=1;
                while($x<=2){
                    if($x==1){$end=4; $gc_title="Comportamental ";}else{$end=4; $gc_title="Desarrollo ";}
                        $a=1;
                        while($a<=$end){
                            echo "<tr><td>$gc_title $a</td>\n";
                            $b=5;
                            while($b<$numfield_grupos){
                                echo "<td><div class='selector'>\n";
                                $y=1;
                                while($y<=4){
                                    echo "<section class='option$y'>Q$y  <input type='checkbox' name='c_$field_groups[$b]"."_$x$a$y' id='c_$field_groups[$b]"."_$x$a$y' ".$sel_gc[$dep][$x][$a][$y][$field_groups[$b]]."></section>\n";
                                $y++;
                                }
                                echo "</div></td>\n";
                            $b++;
                            }
                            echo "</tr>\n";
                        $a++;
                        }


                $x++;
                }
            ?>

    </tbody>
    </table>
    </div>

</div>
<?php
$query="SELECT
		id, Asesor, FindSuperDay(DAY(MAX(Fecha)),MONTH(MAX(Fecha)),YEAR(MAX(Fecha)),id) as Supervisor, Esquema, Departamento as Dep,
     CONCAT(MIN(Fecha),'<br>a<br>',MAX(Fecha)) as Fechas,
     SUM(Duracion_Sesion)/60 as Duracion_Sesion,
     SUM(PNP)/60 as Pausas_No_Productivas, SUM(PP)/60 as Pausas_Productivas,
     (1-((SUM(PNP)/60)/(SUM(Duracion_Sesion)/60)))*100 as Utilizacion,
     AVG(Adherence)*100 as Adherencia, CAST(IF(AVG(Retardos)<0,0,AVG(Retardos)) as DECIMAL) as Retardos, CAST(AVG(Faltas) as DECIMAL) as Faltas, SUM(Ausentismos_Autorizados) as Ausentismos_Autorizados,
     CAST(AVG(Llamadas) as DECIMAL) as Llamadas, CAST(AVG(CoomevaCalls) as DECIMAL) as Llamadas_Coomeva, CAST(AVG(LlamadasReales) as DECIMAL) as Llamadas_Reales,
	  CAST(AVG(ColgadasAsesor) as DECIMAL) as Llamadas_Colgadas, AVG(ColgadasAsesor)/AVG(Llamadas)*100 as Porcentaje_Colgadas,
	  CAST(AVG(Transferidas) as DECIMAL) as Transferidas, CAST(AVG(TransferidasMin) as DECIMAL) as Transferidas_1min, AVG(AHT) AS AHT,
	  SUM(Locs) as 'Localizadores', SUM(Locs)/AVG(LlamadasReales)*100 as FC, SUM(MontoAsesor) as Monto,
      SUM(LocsMP) as 'LocalizadoresMP', SUM(MontoMPAsesor) as MontoMP,
      SUM(Traslados_items) as Traslados_items,
    SUM(Traslados_pax) as Traslados_pax,
    SUM(Traslados_noches) as Traslados_noches,
    SUM(Tours_items) as Tours_items,
    SUM(Tours_pax) as Tours_pax,
    SUM(Tours_noches) as Tours_noches,
    SUM(Cruceros_items) as Cruceros_items,
    SUM(Cruceros_pax) as Cruceros_pax,
    SUM(Cruceros_noches) as Cruceros_noches,
    SUM(Circuitos_items) as Circuitos_items,
    SUM(Circuitos_paxs) as Circuitos_paxs,
    SUM(Circuitos_noches) as Circuitos_noches
	FROM
		(
			SELECT
				Fecha, Dolar
			FROM
				Fechas
			WHERE
				Fecha BETWEEN '$from' AND '$to'
		) Fechas
	JOIN
		(
			SELECT
				id, `N Corto` as asesor, Esquema, `id Departamento` as Departamento
			FROM
				Asesores
			WHERE
				Activo=1 AND
				`id Departamento`=$dep
		) Asesores
	LEFT JOIN
		(
			SELECT
				Fecha_in as Sesiones_Fecha, asesor as Sesiones_asesor, sum(TIME_TO_SEC(Duracion)) as Duracion_Sesion, Skill as Sesiones_Skill
			FROM
				t_Sesiones
			WHERE
				Fecha_in BETWEEN '$from' AND '$to'
			GROUP BY
			Fecha_in, asesor
		)	Sesiones
	ON
		Fechas.Fecha=Sesiones.Sesiones_Fecha AND
		Asesores.id=Sesiones.Sesiones_asesor
	LEFT JOIN
		(
			SELECT
				asesor as Pausas_asesor, Fecha as Pausas_Fecha, sum(TIME_TO_SEC(if(codigo!=10 AND codigo!=0,Duracion,NULL))) as PNP, sum(TIME_TO_SEC(if(codigo=10 OR codigo=0,Duracion,NULL))) as PP,  Skill as Pausas_Skill
			FROM
				t_pausas
			WHERE
				Fecha BETWEEN '$from' AND '$to'
			GROUP BY
				Fecha, asesor
		) Pausas
	ON
		Fechas.Fecha=Pausas_Fecha AND
		Asesores.id=Pausas_asesor
	LEFT JOIN
		(
			SELECT
				id as Adherencia_id, Fecha as Adherencia_Fecha,
                SUM(time_adh)/SUM(Duracion_jornada) as Adherence,
                SUM(Retardo)-SUM(IF(RJ IS NULL,0,RJ)) as Retardos,
                SUM(if(Duracion_jornada=0,0,if(time_adh=0 && tipo_ausentismo!=12,1,0))) as Faltas,
                COUNT(IF(tipo_ausentismo=1 OR tipo_ausentismo=3 OR tipo_ausentismo=5 OR tipo_ausentismo=6 OR tipo_ausentismo=7 OR tipo_ausentismo=9 OR tipo_ausentismo=10 OR tipo_ausentismo=13,tipo_ausentismo,NULL)) as Ausentismos_Autorizados
			FROM
			(
				SELECT
					a1.id, Fecha, if(adherencia=1,0,time_adh) as time_adh, if(adherencia=1,0,Duracion_jornada) as Duracion_jornada, tipo_ausentismo, adherencia, Retardo
				FROM
					(
						SELECT
							id, Fecha,
							TIME_TO_SEC(
								TIMEDIFF(
									if(Logout IS NULL,
										0,
										if(Logout<=
											if(jornada_start='00:00:00' AND jornada_end='00:00:00',jornada_end,
												if(jornada_end>='00:00:00' AND jornada_end<='05:00:00',
												ADDTIME(jornada_end,'24:00:00'),jornada_end)
											)
											,if(jornada_start='00:00:00' AND jornada_end='00:00:00',Logout,
												if(Logout>='00:00:00' AND Logout<='05:00:00',
												ADDTIME(Logout,'24:00:00'),
												Logout)
											)
										,if(jornada_start='00:00:00' AND jornada_end='00:00:00',
											jornada_end,
											if(jornada_end>='00:00:00' AND jornada_end<='05:00:00',
												ADDTIME(jornada_end,'24:00:00'),
												jornada_end)
										)
										)
									),
								if(Login IS NULL,
									0,
									if(Login>=ADDTIME(jornada_start,'00:01:00'),
										Login,
										jornada_start)
								)
								)
							) as time_adh,
							if(jornada_start='00:00:00' AND jornada_end='00:00:00',0,if(Login IS NULL,
									0,
									if(Login>=ADDTIME(jornada_start,'00:01:00'),
										1,
										0)
								)) as Retardo,
							TIME_TO_SEC(TIMEDIFF(if(jornada_start='00:00:00' AND jornada_end='00:00:00',jornada_end,if(jornada_end>='00:00:00' AND jornada_end<='05:00:00',ADDTIME(jornada_end,'24:00:00'),jornada_end)),jornada_start)) as Duracion_jornada
							FROM
								(
									SELECT
										`Historial Programacion`.asesor as id, Fechas.Fecha,
										if(`jornada start`='00:00:00' AND `jornada end`='00:00:00',`jornada start`,if(`jornada start`<'01:00:00',ADDTIME(`jornada end`,if(Fechas.Verano=0,'23:00:00','24:00:00')),ADDTIME(`jornada start`,if(Fechas.Verano=0,'-01:00:00','00:00:00')))) as jornada_start,
										if(`jornada start`='00:00:00' AND `jornada end`='00:00:00',`jornada end`,if(`jornada end`<'01:00:00',ADDTIME(`jornada end`,if(Fechas.Verano=0,'23:00:00','24:00:00')),ADDTIME(`jornada end`,if(Fechas.Verano=0,'-01:00:00','00:00:00')))) as jornada_end,
										if(`jornada start`='00:00:00' AND `jornada end`='00:00:00',`jornada start`,LogAsesor(Fechas.Fecha,t_Sesiones.asesor,'in')) as Login,
										if(`jornada start`='00:00:00' AND `jornada end`='00:00:00',`jornada end`,LogAsesor(Fechas.Fecha,t_Sesiones.asesor,'out')) as Logout
									FROM
										Fechas
									LEFT JOIN
										`Historial Programacion`
									ON
										Fechas.Fecha=`Historial Programacion`.Fecha
									LEFT JOIN
										t_Sesiones
									ON
										`Historial Programacion`.asesor=t_Sesiones.asesor AND
										Fechas.Fecha=t_Sesiones.Fecha_in
									LEFT JOIN
										Asesores c
									ON
										`Historial Programacion`.asesor=c.id
									WHERE
										`id Departamento`=$dep AND
										Fechas.Fecha BETWEEN '$from' AND '$to'
									GROUP BY
										`Historial Programacion`.asesor, Fechas.Fecha
								) as Jornadas
						GROUP BY
								id, Fecha
					) as a1
			LEFT JOIN

			(
				SELECT
					asesor,tipo_ausentismo, Inicio, Fin, adherencia
				FROM
					Ausentismos a,
					`Tipos Ausentismos` c
				WHERE
					a.tipo_ausentismo=c.id AND
					(
						Inicio BETWEEN '$from' AND '$to' OR
						Fin BETWEEN '$from' AND '$to'
					)
			) as b1
			ON
				a1.id=b1.asesor AND
				a1.Fecha BETWEEN Inicio AND Fin
		) as Adherencia
			LEFT JOIN
				(
					SELECT
						Fecha as RJS_Fecha, a.asesor, COUNT(if(tipo=3 OR tipo=8,tipo,NULL)) as RJ
					FROM
						PyA_Exceptions a,
						`Historial Programacion` c
					WHERE
						a.horario_id=c.id AND
						Fecha BETWEEN '$from' AND '$to'
					GROUP BY
						a.asesor,Fecha
				) RJS
			ON
				Adherencia.Fecha=RJS_Fecha AND
				Adherencia.id=RJS.asesor
			GROUP BY
		   	id
		) Adherencia
	ON
		Fechas.Fecha=Adherencia_Fecha AND
		Asesores.id=Adherencia_id
	LEFT JOIN
		(
			SELECT
				Fecha as Telefonia_Fecha, asesor as Telefonia_asesor, count(ac_id) as Llamadas, COUNT(if(Desconexion='Agente',ac_id,NULL)) as ColgadasAsesor,
            COUNT(if(Desconexion='Transferida',ac_id,NULL)) as Transferidas,
            COUNT(if(Desconexion='Transferida' AND Duracion_Real<'00:01:00',ac_id,NULL)) as TransferidasMin,
            AVG(TIME_TO_SEC(Duracion_Real)) as AHT,
            COUNT(if(MONTH(Fecha)=2,if(DNIS='4879',ac_id,NULL),NULL)) as CoomevaCalls,
            count(ac_id)-COUNT(if(Desconexion='Transferida' AND Duracion_Real<'00:01:00',ac_id,NULL))-if(Fecha BETWEEN '2016-02-01' AND '2016-02-29',COUNT(if(MONTH(Fecha)=2,if(DNIS='4879',ac_id,NULL),NULL)),0) as LlamadasReales
			FROM
				t_Answered_Calls a,
				Cola_Skill b
			WHERE
				a.Cola=b.Cola AND
                                b.Skill=$dep AND
				Fecha BETWEEN '$from' AND '$to'
			GROUP BY
				asesor
		) Telefonia
	ON
		Fechas.Fecha=Telefonia_Fecha AND
		Asesores.id=Telefonia_asesor
	LEFT JOIN
		(
			SELECT
				asesor as Localizadores_asesor, Fecha as Localizadores_Fecha, COUNT(*) as Locs, COUNT(IF(Afiliado LIKE '%pricetravel.com%',Localizador,NULL)) as LocsMP, SUM(Monto) as MontoPorDia
			FROM
				(
					SELECT
						asesor, Fechas.Fecha, Localizador, Afiliado, SUM(Venta+OtrosIngresos+Egresos)*Dolar as Monto
					FROM
						Fechas
					LEFT JOIN
						t_Locs
					ON
						Fechas.Fecha=t_Locs.Fecha
					WHERE
						Venta!=0 AND
						Fechas.Fecha BETWEEN '$from' AND '$to'
					GROUP BY
						asesor, Fechas.Fecha, Localizador
					HAVING
						Monto!=0
				) Locs1
			GROUP BY
				asesor,Fecha
		) Localizadores
	ON
		Fechas.Fecha=Localizadores_Fecha AND
		Asesores.id=Localizadores_asesor
	LEFT JOIN
		(
			SELECT
				asesor as LocsMonto_asesor, Fecha as LocsMonto_Fecha, SUM(Monto) as MontoAsesor, SUM(MontoMP) as MontoMPAsesor
			FROM
				(
					SELECT
						asesor, Fechas.Fecha, Localizador, SUM(Venta+OtrosIngresos+Egresos)*Dolar as Monto, SUM(IF(Afiliado LIKE '%pricetravel.com%',Venta+OtrosIngresos+Egresos,0))*Dolar as MontoMP
					FROM
						Fechas
					LEFT JOIN
						t_Locs
					ON
						Fechas.Fecha=t_Locs.Fecha
					LEFT JOIN
						Asesores c
					ON
						t_Locs.asesor=c.id
					WHERE
						`id Departamento`=$dep AND
						Fechas.Fecha BETWEEN '$from' AND '$to'
					GROUP BY
						asesor, Fechas.Fecha, Localizador

				) Locs2
			GROUP BY
				asesor,Fecha
		) LocsMonto
	ON
		Fechas.Fecha=LocsMonto_Fecha AND
		Asesores.id=LocsMonto_asesor
    LEFT JOIN
        (
        SELECT
            	Fecha as serv_fecha, locs_asesor as serv_asesor,   COUNT(IF(servicio='Traslado',locs_locs,NULL)) as Traslados_items,
    SUM(IF(servicio='Traslado',pax,0)) as Traslados_pax,
    SUM(IF(servicio='Traslado',noches,0)) as Traslados_noches,
    COUNT(IF(servicio='Tour',locs_locs,NULL)) as Tours_items,
    SUM(IF(servicio='Tour',pax,0)) as Tours_pax,
    SUM(IF(servicio='Tour',noches,0)) as Tours_noches,
    COUNT(IF(servicio='Crucero',locs_locs,NULL)) as Cruceros_items,
    SUM(IF(servicio='Crucero',pax,0)) as Cruceros_pax,
    SUM(IF(servicio='Crucero',noches,0)) as Cruceros_noches,
    COUNT(IF(servicio='Circuito',locs_locs,NULL)) as Circuitos_items,
    SUM(IF(servicio='Circuito',pax,0)) as Circuitos_paxs,
    SUM(IF(servicio='Circuito',noches,0)) as Circuitos_noches
            FROM
            	(
            		SELECT
            			Fecha, locs_locs, locs_asesor, servicio, pax, noches
            		FROM
            			(
            				SELECT
            					Fecha
            				FROM
            					Fechas
            				WHERE
            					Fecha BETWEEN '$from' AND '$to'
            			) Fechas
            		LEFT JOIN
            			(
            				SELECT
            					asesor as locs_asesor, Fecha as Locs_fecha, Localizador as locs_locs, SUM(Venta+OtrosIngresos+Egresos) as Monto
            				FROM
            					t_Locs
            				LEFT JOIN
            					Asesores c
            				ON
            					asesor=c.id
            				WHERE
            					`id Departamento`=$dep AND
            					Venta!=0 AND
            					Fecha BETWEEN '$from' AND '$to' AND
                                Afiliado LIKE '%pricetravel.com.mx%'
            				GROUP BY
            					asesor, Fecha, Localizador
            				HAVING
            					Monto!=0
            			) Locs
            		ON
            			Fecha=Locs_fecha
            		RIGHT JOIN
            			(
            				SELECT
            					localizador as ter_loc, fecha_transaccion as ter_fecha, b.servicio, pax, noches
            				FROM
            					t_terrestres a
            				LEFT JOIN
            					tipos_servicios_terrestres b
            				ON
            					a.servicio=b.id
            				WHERE
            					a.fecha_transaccion BETWEEN '$from' AND '$to' AND
            					venta>0 AND
            					pax!=0
            			) terrestres
            		ON
            			locs_locs=ter_loc AND
            			Fecha=ter_fecha
                    HAVING
            			locs_locs IS NOT NULL
            	) a
            WHERE
            	servicio IS NOT NULL
            GROUP BY
            	serv_fecha, serv_asesor
        ) servicios
    ON
        Fechas.Fecha=serv_fecha AND
		Asesores.id=serv_asesor
	GROUP BY
		Asesor
	ORDER BY
      Asesores.Asesor";
//echo "$query<br>";

$result=mysql_query($query);
if(mysql_error()){echo "<br>".mysql_error()."<br>";}
$num=mysql_numrows($result);
$fields=mysql_num_fields($result);

$fi=0;
$i=0;
while($i<$num){
     $x=0;
     while($x<$fields){
        $field[$x]=mysql_field_name($result,$x);
        $data_asesores[$i][$field[$x]]=mysql_result($result,$i,$x);
        $data_values[$field[$x]][$i]=mysql_result($result,$i,$x);
     $x++;
     }
$i++;
}



$avg_calls=array_sum($data_values['Llamadas'])/$num*0.8;
foreach($data_values['Esquema'] as $tipo => $value){
    switch($value){
        case 4:
            $ses_4=$ses_4+$data_values['Duracion_Sesion'][$tipo];
            $ses_4_count++;
            break;
        case 6:
            $ses_6=$ses_6+$data_values['Duracion_Sesion'][$tipo];
            $ses_6_count++;
            break;
        case 8:
            $ses_8=$ses_8+$data_values['Duracion_Sesion'][$tipo];
            $ses_8_count++;
            break;
        case 10:
            $ses_10=$ses_10+$data_values['Duracion_Sesion'][$tipo];
            $ses_10_count++;
            break;

    }
}
unset($tipo);
unset($value);
if($ses_4_count==0){
    $avg_ses4=0;
}else{
    $avg_ses4=$ses_4/$ses_4_count*$perc_defined;
}

if($ses_6_count==0){
    $avg_ses6=0;
}else{
    $avg_ses6=$ses_6/$ses_6_count*$perc_defined;
}

if($ses_8_count==0){
    $avg_ses8=0;
}else{
    $avg_ses8=$ses_8/$ses_8_count*$perc_defined;
}

if($ses_10_count==0){
    $avg_ses10=0;
}else{
    $avg_ses10=$ses_10/$ses_10_count*$perc_defined;
}


foreach($data_values as $key => $valores){
    foreach($valores as $key2 => $info){
        switch($data_values['Esquema'][$key2]){
                case 4:
                    if($data_values['Duracion_Sesion'][$key2]>=$avg_ses4){$sort[$key][]=$info;}
                    break;
                case 6:
                    if($data_values['Duracion_Sesion'][$key2]>=$avg_ses6){$sort[$key][]=$info;}
                    break;
                case 8:
                    if($data_values['Duracion_Sesion'][$key2]>=$avg_ses8){$sort[$key][]=$info;}
                    break;
                case 10:
                    if($data_values['Duracion_Sesion'][$key2]>=$avg_ses10){$sort[$key][]=$info;}
                    break;
        }

    }
    unset($key2, $info);
    switch($key){
        case 'Colgadas_porcentaje':
        case 'AHT':
        case 'Retardos':
        case 'Faltas':
            sort($sort[$key]);
            break;
        default:
            rsort($sort[$key]);
            break;

    }

}
unset($key, $valores);

$q_1=intval(count($sort['Utilizacion'])/4);
$q_2=intval(count($sort['Utilizacion'])/4)*2;
$q_3=intval(count($sort['Utilizacion'])/4)*3;
switch(count($sort['Utilizacion']) % 4){
    case 1:
        $q_1+1;
        break;
    case 2:
        $q_1+1;
        $q_2+1;
        break;
    case 3:
        $q_1+1;
        $q_2+1;
        $q_3+1;
        break;
    default:
        break;
}


?>

<br>
<div style='text-align:left; width:120px; float:left; display: inline-block;'>
<button type="button" tipo="" number='' class='group_select button button_white_r'>Reset</button>
</div>
<div style='text-align:left; width:35%; float:left; display: inline-block;'>
<button type="button" tipo="1" number='1' class='group_select buttonlarge button_redpastel_w'>Comportamental 1</button>
<button type="button" tipo="1" number='2' class='group_select buttonlarge button_redpastel_w'>Comportamental 2</button>
<button type="button" tipo="1" number='3' class='group_select buttonlarge button_redpastel_w'>Comportamental 3</button>
<button type="button" tipo="1" number='4' class='group_select buttonlarge button_redpastel_w'>Comportamental 4</button>
</div>
<div style='text-align:left; width:35%; float:left; display: inline-block;'>
<button type="button" tipo="2" number='1' class='group_select buttonlarge button_orange_w'>Desarrollable 1</button>
<button type="button" tipo="2" number='2' class='group_select buttonlarge button_orange_w'>Desarrollable 2</button>
<button type="button" tipo="2" number='3' class='group_select buttonlarge button_orange_w'>Desarrollable 3</button>
<button type="button" tipo="2" number='4' class='group_select buttonlarge button_orange_w'>Desarrollable 4</button>
</div>
<div style='text-align:right; width:100px; float:right; display: inline-block;'>
<input type="button" id='export' value="Export" class='button button_blue_w'/>  </div>
<br>

<div id='container-cuartiles' style='max-height:800px; width: 100%; overflow: scroll; position: relative'>
<table id='info' style='font-size:12px'>
    <thead>
    <tr class='title'>
    <?php
        foreach($field as $key => $campo){
            switch($campo){
                case 'Monto':
                case 'MontoMP':
                case 'LocalizadoresMP':
                case 'Utilizacion':
                case 'Adherencia':
                case 'Porcentaje_Colgadas':
                case 'AHT':
                case 'Retardos':
                case 'Faltas':
                case 'Localizadores':
                case 'FC':
                case 'Traslados_items':
                case 'Tours_items':
                case 'Circuitos_items':
                case 'Cruceros_items':
                    $colspan=" colspan=1";
                    $q_title="<th>Cuartil<br>$campo</th>";
                    break;
                case 'Asesor':
                    $colspan=" colspan=2";
                    $q_title="";
                    break;
                default:
                    $colspan=" colspan=1";
                    $q_title="";
                    break;
            }
            $campo_ok=str_replace('_','<br>',$campo);
            echo "\t<th$colspan class='drag-enable'>$campo_ok</th>$q_title\n";

        }
        unset($key);
        unset($campo);

    ?>
    </tr>
    </thead>
    <tbody>
    <?php
        foreach($data_asesores as $key => $info){
            $class="";
            switch($info['Esquema']){
                case 4:
                    if($info['Duracion_Sesion']<$avg_ses4){$class="class='not'";}
                    break;
                case 6:
                    if($info['Duracion_Sesion']<$avg_ses6){$class="class='not'";}
                    break;
                case 8:
                    if($info['Duracion_Sesion']<$avg_ses8){$class="class='not'";}
                    break;
                case 10:
                    if($info['Duracion_Sesion']<$avg_ses10){$class="class='not'";}
                    break;
            }

            echo "<tr style='text-align: center'>\n";
            foreach($field as $key2=> $parameter){

                switch($parameter){
                    case 'Utilizacion':
                    case 'Adherencia':
                    case 'Localizadores':
                    case 'FC':
                    case 'Monto':
                    case 'LocalizadoresMP':
                    case 'MontoMP':
                    case 'Traslados_items':
                    case 'Tours_items':
                    case 'Circuitos_items':
                    case 'Cruceros_items':
                        if($class!="class='not'"){
                            if($info[$parameter]>=$sort[$parameter][$q_3]){$p_class="</td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td>"; $q=3;}else{$p_class="</td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td>"; $q=4;}
                            if($info[$parameter]>=$sort[$parameter][$q_2]){$p_class="</td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td>"; $q=2;}
                            if($info[$parameter]>=$sort[$parameter][$q_1]){$p_class="</td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td>"; $q=1;}
                        }else{$p_class="</td><td></td>";}
                        break;
                    case 'Porcentaje_Colgadas':
                    case 'AHT':
                    case 'Retardos':
                    case 'Faltas':
                        if($class!="class='not'"){
                            if($info[$parameter]>$sort[$parameter][$q_1]){$p_class="</td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td>"; $q=2;}else{$p_class="</td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td>"; $q=1;}
                            if($info[$parameter]>$sort[$parameter][$q_2]){$p_class="</td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td>"; $q=3;}
                            if($info[$parameter]>$sort[$parameter][$q_3]){$p_class="</td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td>"; $q=4;}
                        }else{$p_class="</td><td></td>";}
                        break;
                    case 'Asesor':
                    if($class=="class='not'"){
                        $p_class="</td><td><img src='/images/not.png' alt='No Cuartilizable' height='20' width='20'></td>";
                    }else{$p_class="</td><td></td>";}
                    break;
                    default:
                        $p_class="";
                         $q="";
                        break;

                }
                switch($parameter){
                    case 'Utilizacion':
                    case 'Adherencia':
                    case 'Porcentaje_Colgadas':
                    case 'FC':
                        $result=number_format($info[$parameter],2)." %";
                        break;
                    case 'Monto':
                    case 'MontoMP':
                        $result="$".number_format($info[$parameter],2);
                        break;
                    case 'AHT':
                    case 'Duracion_Sesion':
                    case 'Pausas_No_Productivas':
                    case 'Pausas_Productivas':
                        $result=number_format($info[$parameter],2);
                        break;
                    case 'id':
                    case 'Nombre':
                    case 'Esquema':
                    case 'Mes':
                    case 'Skill':
                        $result=$info[$parameter];
                        $q=$info[$parameter];
                        break;
                    default:
                        $result=$info[$parameter];
                        break;
                }

                echo "\t<td>$result $p_class</td>\n";
            }
            echo "</tr>\n";
        }
    ?>
    </tbody>
</table>
</div>
<br>
<table id='info_aht' style='font-size:12px; vertical-align: middle; margin:auto; width:400px;'>
    <thead>
        <tr>
            <th>Supervisor</th>
            <th>AHT</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $query="SELECT
                    	asesor, FindSuperDay(DAY(Fecha),MONTH(Fecha),YEAR(Fecha),asesor) as Supervisor, AVG(TIME_TO_SEC(Duracion_Real)) as AHT
                    FROM
                    	t_Answered_Calls a
                    LEFT JOIN
                    	Cola_Skill b
                    ON
                    	a.Cola=b.Cola
                    LEFT JOIN
                        Asesores c
                    ON
                        asesor=c.id
                    WHERE
                    	Skill=$dep AND
                    	Answered=1 AND
                        `id Departamento`=$dep AND
                    	Fecha BETWEEN '$from' AND '$to'
                    GROUP BY
                    	Supervisor";
            $result=mysql_query($query);
            $num=mysql_numrows($result);
            for($i=0;$i<$num;$i++){
                $data_aht[mysql_result($result,$i,'Supervisor')]=mysql_result($result,$i,'AHT');
            }

            foreach($data_aht as $super_aht => $info_aht){
                echo "<tr><td>$super_aht</td><td style='text-align:center'>".number_format($info_aht,2)."</td></tr>\n\t";
            }

        ?>

    </tbody>
    </table>
<br>
<div class='qlegend' id='qlegend'><p id='qtlegend'></p></div>
<style>
/* optional styling */
caption {
  /* override bootstrap adding 8px to the top & bottom of the caption */
  padding: 0;
}
.ui-sortable-placeholder {
  /* change placeholder (seen while dragging) background color */
  background: #ddd;
}
div.table-handle-disabled {
  /* optional red background color indicating a disabled drag handle */
  background-color: rgba(255,128,128,0.5);
  /* opacity set to zero for disabled handles in the dragtable.mod.css file */
  opacity: 0.7;
}
/* fix cursor */
.tablesorter-blue .tablesorter-header {
  cursor: default;
}
.sorter {
  cursor: pointer;
}
</style>

<script type="text/javascript" src="/js/tablesorter/js/extras/jquery.dragtable.mod.js"></script>
<script>

$(document).ready(function()
    {
        $('#qlegend').hide();

        $('#info, #info_aht').tablesorter({
            theme: 'blue',
            headerTemplate: '{content}',
            widthFixed: false,
            widgets: [ 'zebra','filter', 'output' , 'stickyHeaders',  'resizable'],
            widgetOptions: {

               resizable_addLastColumn : true,
               resizable_widths : [ ,,,,,,'65px' ],
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
                output_includeHTML   : false,        // output includes all cell HTML (except the header cells)
                output_trimSpaces    : false,       // remove extra white-space characters from beginning & end
                output_wrapQuotes    : false,       // wrap every cell output in quotes
                output_popupStyle    : 'width=580,height=310',
                output_saveFileName  : 'cuartiles_<?php echo "$year"."_$month"."_$dep";?>.csv',
                // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
                output_encoding      : 'data:application/octet-stream;charset=utf8,',
                stickyHeaders_attachTo : '#container-cuartiles'


            }
        });

        $("#config").tablesorter({
            theme: 'blue',
            headerTemplate: '{content}',
            widthFixed: false,
            widgets: [ 'uitheme','zebra', 'stickyHeaders'],
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
                stickyHeaders_attachTo : '#config-contain'


            }
        });

        $( "#accordion, #accordion_sups" ).accordion({
      collapsible: true,
      heightStyle: "content",
      active: false
    });

        $('#export').click(function(){
            $('#info').trigger('outputTable');

        });




         $('.group_select').click(function(){
            var tipo;
            var numero;
            var grupo;


            tipo=$(this).attr('tipo');
            numero=$(this).attr('number');

            if(tipo==1){grupo="Comportamental "+numero+": ";}else{grupo="Desarrollable "+numero+": ";}

            var tmp;
            var legend="";
            <?php
                $i=5;
                    while($i<$numfield_grupos){
                        echo "var ".$field_groups[$i]."_filter='';\n var ".$field_groups[$i]."_flag=0;\n var separator_$field_groups[$i]='';\n ";

                    $i++;
                    }
            ?>
            var i=1;
            while(i<=4){
                <?php
                    $i=5;
                    while($i<$numfield_grupos){

                        echo "if($('#c_".$field_groups[$i]."_'+tipo+numero+i).is(':checked')){\n
                                    tmp=1;\n
                                }else{\n
                                    tmp=0;\n
                                }\n
                                if(".$field_groups[$i]."_flag!=0){separator_$field_groups[$i]='|';}\n
                                if(tmp==1){".$field_groups[$i]."_filter=".$field_groups[$i]."_filter+separator_$field_groups[$i]+i;
                                ".$field_groups[$i]."_flag=".$field_groups[$i]."_flag+1;
                                legend=legend+' $field_groups[$i] Q'+i+' // ';
                                }\n\n";

                    $i++;
                    }
                ?>

            i=i+1;
            }



           var filters = [],
              col = '31', // zero-based index
              txt = FC_filter; // text to add to filter

            filters['11'] = Utilizacion_filter;
            filters['13'] = Adherencia_filter;
            filters['23'] = Colgadas_filter;
            filters['27'] = AHT_filter;
            filters['15'] = Retardos_filter;
            filters['17'] = Faltas_filter;
            filters['29'] = Localizadores_filter;
            filters['31'] = FC_filter;
            filters['33'] = Monto_filter;

            // using "table.hasFilters" here to make sure we aren't targeting a sticky header
            $.tablesorter.setFilters( $('#info'), filters, true ); // new v2.9

            if(tipo!=""){
                document.getElementById('qtlegend').innerText=grupo+legend;
                $('#qlegend').show();
            }else{
                document.getElementById('qtlegend').innerText="";
                $('#qlegend').hide();
            }
            return false;
          });

          $('#info').trigger('refreshColumnSelector', [ [2,3,4] ]);
    }
);

</script>