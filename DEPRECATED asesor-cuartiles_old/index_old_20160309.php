<?php

session_start();
$this_page=$_SERVER['PHP_SELF'];
if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
date_default_timezone_set('America/Bogota');
$credential="asesor_cuartiles";
$menu_asesores="class='active'";


?>

<?php
include("../connectDB.php");
header("Content-Type: text/html;charset=utf-8");

//GET Variables
$dep=$_POST['pcrc'];
$month=$_POST['month'];
$year=$_POST['year'];
$perc_defined=0.8;

//SELECT functions
function printDeps($variable){
    $query="SELECT a.id as id, Departamento
            FROM PCRCs_Parent a, PCRCs b
            WHERE a.id=b.id AND Cuartiles=1 ORDER BY Departamento";
    $result=mysql_query($query);
    $num=mysql_numrows($result);
    $i=0;
    while($i<$num){
        if($variable==mysql_result($result,$i,'id')){$selected="selected";}else{$selected="";}
        echo "<option value='".mysql_result($result,$i,'id')."' $selected>";
        echo mysql_result($result,$i,'Departamento');
        echo "</option>\n";
    $i++;
    }

}

function printMonth($variable){
    $i=1;
    while($i<=12){
        if($variable==$i){$selected="selected";}else{$selected="";}
        echo "<option value='$i' $selected>";
        $date="2016-$i-01";
        echo date('F',strtotime($date));
        echo "</option>\n";
    $i++;
    }
}

function printYear($variable){
    $query="SELECT DISTINCT YEAR(Fecha) as year FROM t_Answered_Calls";
    $result=mysql_query($query);
    $num=mysql_numrows($result);
    $i=0;
    while($i<$num){
        if($variable==mysql_result($result,$i,'year')){$selected="selected";}else{$selected="";}
        echo "<option value='".mysql_result($result,$i,'year')."' $selected>";
        echo mysql_result($result,$i,'year');
        echo "</option>\n";
    $i++;
    }
}

include("../common/scripts.php");

?>
<style>
    .selector {
        width: 140px;
        padding:5px;
        border: 0px solid;
        margin: auto;
       }
       .selector .option1{
            display: inline-block;
            font: 12px arial, sans-serif;
            color: green;
            width: 45px;
            padding: 5px;
            border: 1px solid;
            margin-right: 0px;
            margin-left: 10px;
            margin-top:2px;
            margin-bottom:2px;
        }
        .selector .option2{
            display: inline-block;
            font: 12px arial, sans-serif;
            color: #b3b300;
            width: 45px;
            padding: 5px;
            border: 1px solid;
            margin-right: 0px;
            margin-left: 10px;
            margin-top:2px;
            margin-bottom:2px;
        }
        .selector .option3{
            display: inline-block;
            font: 12px arial, sans-serif;
            color: orange;
            width: 45px;
            padding: 5px;
            border: 1px solid;
            margin-right: 0px;
            margin-left: 10px;
            margin-top:2px;
            margin-bottom:2px;
        }
        .selector .option4{
            display: inline-block;
            font: 12px arial, sans-serif;
            color: red;
            width: 45px;
            padding: 5px;
            border: 1px solid;
            margin-right: 0px;
            margin-left: 10px;
            margin-top:2px;
            margin-bottom:2px;
        }
        .qlegend{
            font: 12px arial, sans-serif;
            background-color:  #fffae6;
            color: orange;
            width: 100%;
            padding: 5px;
            border: 1px solid;
            margin-right: 0px;
            margin-left: 0px;
            margin-top:2px;
            margin-bottom:2px;
        }

</style>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-scroller.js"></script>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-output.js"></script>

<?php
include("../common/menu.php");
?>
<table class='t2' width='100%'><form action="<?php $_SERVER['PHP_SELF'] ?>" method="Post">
    <tr class='title'>
        <th colspan=100>Cuartiles por Programa</th>
    </tr>
    <tr class='subtitle'>
        <td width='14%' >Month</td>
        <td width='14%'  class='pair'><select name="month" id="month" required><option value="">Select...</option>><?php printMonth($month); ?></select></td>
        <td width='14%' >Year</td>
        <td width='14%'  class='pair'><select name="year" id="year" required><option value="">Select...</option>><?php printYear($year); ?></select></td>
        <td width='14%' >PCRC</td>
        <td width='14%'  class='pair'><select name="pcrc" id="pcrc" required><option value="">Select...</option>><?php printDeps($dep); ?></select></td>
        <td class='total'><input type="submit" name="consultar"></td>
    </tr>
</form></table>
<br><br>


<?php if(!isset($_POST['consultar'])){exit;}

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
    <div style='height: 400px; overflow: scroll;'>
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
	Sesiones.asesor as id, ADH.Nombre, Sesiones.Esquema, Sesiones.Mes, Sesiones.Skill,FindSuper(Sesiones.Mes,Sesiones.Anio,Sesiones.asesor) as Supervisor,  TIME_TO_SEC(Sesiones.Duracion)/60 as 'Sesiones',
	TIME_TO_SEC(PNP)/60 as Pausas_no_Productivas,TIME_TO_SEC(PP)/60 as Pausas_Productivas,
	1-TIME_TO_SEC(PNP)/TIME_TO_SEC(Duracion) as Utilizacion, Adherence as Adherencia,
	Llamadas, CoomevaCalls as Llamadas_Coomeva, Llamadas-TransferidasMin-(IF(Sesiones.Mes=2,CoomevaCalls,0)) as Llamadas_Reales, ColgadasAsesor, ColgadasAsesor/Llamadas as Colgadas_porcentaje, Transferidas, TransferidasMin as Transferidas_1min, AHT,
	Retardos-RJ as Retardos, Faltas,
	Localizadores,
    Localizadores/(Llamadas-TransferidasMin-(IF(Sesiones.Mes=2,CoomevaCalls,0))) as FC,
	mxn_total+usd_total/USD+cop_total*COP as Monto
	FROM
        #Fechas
    		(
    			SELECT
    				MONTH(Fecha) as Mes
    					FROM
    						Fechas
    					WHERE
    						MONTH(Fecha)=$month AND
    						YEAR(Fecha)=$year
                        GROUP BY
                            MONTH(Fecha)
    		) as Fechas
    LEFT JOIN
		#Sesiones
		(
			SELECT
			Esquema, asesor, YEAR(Fecha_in) as Anio,MONTH(Fecha_in) as Mes, SEC_TO_TIME(sum(TIME_TO_SEC(Duracion))) as Duracion, Skill
			FROM
				t_Sesiones a,
				Asesores b
			WHERE
				a.asesor=b.id AND
				b.`id Departamento`=$dep AND
				Activo=1 AND
				Skill=$dep
			GROUP BY
				MONTH(Fecha_in), asesor
		) as Sesiones
    ON
		Fechas.Mes=Sesiones.Mes
	LEFT JOIN
		#Pausas
		(
			SELECT
			asesor, MONTH(Fecha) as Mes, SEC_TO_TIME(sum(TIME_TO_SEC(if(codigo!=10 AND codigo!=0,Duracion,NULL)))) as PNP, SEC_TO_TIME(sum(TIME_TO_SEC(if(codigo=10 OR codigo=0,Duracion,NULL)))) as PP,  Skill
			FROM
				t_pausas a,
				Asesores b
			WHERE
				a.asesor=b.id AND
				b.`id Departamento`=$dep AND
				Activo=1 AND
				Skill=$dep
			GROUP BY
				MONTH(Fecha), asesor
		) as Pausas
	ON
		Sesiones.asesor=Pausas.asesor AND
		Sesiones.Mes=Pausas.Mes
	LEFT JOIN
	#Aderencia
	(
		SELECT
		id, Nombre, MONTH(Fecha) as Mes, SUM(time_adh)/SUM(Duracion_jornada) as Adherence, SUM(Retardo) as Retardos, SUM(if(Duracion_jornada=0,0,if(time_adh=0,1,0))) as Faltas
		FROM
		(
			SELECT a1.id, a1.Nombre, Fecha, if(adherencia=1,0,time_adh) as time_adh, if(adherencia=1,0,Duracion_jornada) as Duracion_jornada, tipo_ausentismo, adherencia, Retardo FROM
	(
	SELECT
			id, `N Corto` as Nombre, Fecha,
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
					if(Login>=jornada_start,
						1,
						0)
				)) as Retardo,
			TIME_TO_SEC(TIMEDIFF(if(jornada_start='00:00:00' AND jornada_end='00:00:00',jornada_end,if(jornada_end>='00:00:00' AND jornada_end<='05:00:00',ADDTIME(jornada_end,'24:00:00'),jornada_end)),jornada_start)) as Duracion_jornada
			FROM
			(
				SELECT
				Asesores.id, `N Corto`, Fechas.Fecha,
				if(`jornada start`='00:00:00' AND `jornada end`='00:00:00',`jornada start`,if(`jornada start`<'01:00:00',ADDTIME(`jornada end`,if(Fechas.Verano=0,'23:00:00','24:00:00')),ADDTIME(`jornada start`,if(Fechas.Verano=0,'-01:00:00','00:00:00')))) as jornada_start,
				if(`jornada start`='00:00:00' AND `jornada end`='00:00:00',`jornada end`,if(`jornada end`<'01:00:00',ADDTIME(`jornada end`,if(Fechas.Verano=0,'23:00:00','24:00:00')),ADDTIME(`jornada end`,if(Fechas.Verano=0,'-01:00:00','00:00:00')))) as jornada_end,
				if(`jornada start`='00:00:00' AND `jornada end`='00:00:00',`jornada start`,LogAsesor(Fechas.Fecha,t_Sesiones.asesor,'in')) as Login,
				if(`jornada start`='00:00:00' AND `jornada end`='00:00:00',`jornada end`,LogAsesor(Fechas.Fecha,t_Sesiones.asesor,'out')) as Logout
				FROM
					Asesores
				JOIN
					Fechas
				LEFT JOIN
					`Historial Programacion`
				ON
					Asesores.id=`Historial Programacion`.asesor AND
					Fechas.Fecha=`Historial Programacion`.Fecha
				LEFT JOIN
					t_Sesiones
				ON
					`Historial Programacion`.asesor=t_Sesiones.asesor AND
					`Historial Programacion`.Fecha=t_Sesiones.Fecha_in
				WHERE
					Activo=1 AND
					MONTH(Fechas.Fecha)=$month AND
					YEAR(Fechas.Fecha)=$year AND
					`id Departamento`=$dep
				GROUP BY
					Asesores.id, Fechas.Fecha) as Jornadas
		GROUP BY
				id, Fecha
	) as a1
	LEFT JOIN

	(SELECT
	asesor,tipo_ausentismo, Inicio, Fin, adherencia
	FROM

		Ausentismos a,
		Asesores b,
		`Tipos Ausentismos` c
	WHERE
		a.asesor=b.id AND
		a.tipo_ausentismo=c.id AND
		b.`id Departamento`=$dep AND
		MONTH(Inicio)=$month AND
		YEAR(Inicio)=$year
		) as b1
	ON
	a1.id=b1.asesor AND
	a1.Fecha BETWEEN Inicio AND Fin
	) as Adherencia
		GROUP BY
	   	MONTH(Fecha),
	   	id) as ADH
		ON
			Sesiones.asesor=ADH.id
	LEFT JOIN
		#Telefonia
		(
			SELECT
			asesor, count(ac_id) as Llamadas, COUNT(if(Desconexion='Agente',ac_id,NULL)) as ColgadasAsesor,
            COUNT(if(Desconexion='Transferida',ac_id,NULL)) as Transferidas,
            COUNT(if(Desconexion='Transferida' AND Duracion_Real<'00:01:00',ac_id,NULL)) as TransferidasMin,
            AVG(TIME_TO_SEC(Duracion_Real)) as AHT,
            COUNT(if(MONTH(Fecha)=2,if(DNIS='4879',ac_id,NULL),NULL)) as CoomevaCalls
			FROM
				t_Answered_Calls a,
				Cola_Skill b,
				Asesores c
			WHERE
				a.asesor=c.id AND
				a.Cola=b.Cola AND
				b.Skill=c.`id Departamento` AND
				c.`id Departamento`=$dep AND
				MONTH(Fecha)=$month AND
				YEAR(Fecha)=$year
			GROUP BY
				asesor
		) as Telefonia
	ON
		Telefonia.asesor=Sesiones.asesor
	LEFT JOIN
	#Localizadores
		(
			SELECT
		asesor, MONTH(Fecha) as Mes, count(DISTINCT Localizador)-CheckLocsCancelados(MONTH(Fecha), asesor) as Localizadores
		FROM
			t_Locs a,
			Asesores b
		WHERE
			a.asesor=b.id AND
			b.`id Departamento`=$dep AND
			Venta!=0 AND
			MONTH(Fecha)=$month AND
			YEAR(Fecha)=$year
		GROUP BY
			Asesor, MONTH(Fecha)
		) as Locs
	ON
		Sesiones.asesor=Locs.asesor
	LEFT JOIN
	#Montos
		(
			SELECT
			asesor, mxn_total, usd_total, cop_total
			FROM
				t_Montos_Diarios_Acumulado a,
				Asesores b
			WHERE
				a.asesor=b.id AND
				b.`id Departamento`=$dep AND
				MONTH(Fecha)=$month AND
				YEAR(Fecha)=$year
			GROUP BY
				asesor, MONTH(Fecha)
		) as Montos
	ON
		Sesiones.asesor=Montos.asesor
	LEFT JOIN
	#Tipo de cambio
		(
			SELECT
				MONTH(Fecha) as Mes, AVG(Dolar) as USD, AVG(COP) as COP
				FROM
					Fechas
				WHERE
					MONTH(Fecha)=$month
		) as TipoCambio
	ON
		Sesiones.Mes=TipoCambio.Mes
    LEFT JOIN
	#Retardos Justificados
		(
			SELECT
	MONTH(Fecha) as Mes, a.asesor, COUNT(if(tipo=3 OR tipo=8,tipo,NULL)) as RJ
	FROM
		PyA_Exceptions a,
		Asesores b,
		`Historial Programacion` c
	WHERE
		a.asesor=b.id AND
		b.`id Departamento`=$dep AND
		a.horario_id=c.id AND
		MONTH(Fecha)=$month AND
		YEAR(Fecha)=$year
	GROUP BY
		asesor, MONTH(Fecha)
		) as Rets
	ON
		Sesiones.Mes=Rets.Mes AND
		Sesiones.asesor=Rets.asesor
    ORDER BY
        ADH.Nombre";
//echo "$query<br>";
$result=mysql_query($query);
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
            $ses_4=$ses_4+$data_values['Sesiones'][$tipo];
            $ses_4_count++;
            break;
        case 6:
            $ses_6=$ses_6+$data_values['Sesiones'][$tipo];
            $ses_6_count++;
            break;
        case 8:
            $ses_8=$ses_8+$data_values['Sesiones'][$tipo];
            $ses_8_count++;
            break;
        case 10:
            $ses_10=$ses_10+$data_values['Sesiones'][$tipo];
            $ses_10_count++;
            break;

    }
}
unset($tipo);
unset($value);
$avg_ses4=$ses_4/$ses_4_count*$perc_defined;
$avg_ses6=$ses_6/$ses_6_count*$perc_defined;
$avg_ses8=$ses_8/$ses_8_count*$perc_defined;
$avg_ses10=$ses_10/$ses_10_count*$perc_defined;

foreach($data_values as $key => $valores){
    foreach($valores as $key2 => $info){
        switch($data_values['Esquema'][$key2]){
                case 4:
                    if($data_values['Sesiones'][$key2]>=$avg_ses4){$sort[$key][]=$info;}
                    break;
                case 6:
                    if($data_values['Sesiones'][$key2]>=$avg_ses6){$sort[$key][]=$info;}
                    break;
                case 8:
                    if($data_values['Sesiones'][$key2]>=$avg_ses8){$sort[$key][]=$info;}
                    break;
                case 10:
                    if($data_values['Sesiones'][$key2]>=$avg_ses10){$sort[$key][]=$info;}
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

$q_1=intval(count($sort['AHT'])/4);
$q_2=intval(count($sort['AHT'])/4)*2;
$q_3=intval(count($sort['AHT'])/4)*3;
switch(count($sort['AHT']) % 4){
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
<div style='max-height:800px; width: 100%; overflow: scroll'>
<table id='info' style='font-size:12px'>
    <thead>
    <tr class='title'>
    <?php
         $csv_hdr="Info,";
        foreach($field as $key => $campo){
            switch($campo){
                case 'Monto':
                case 'Utilizacion':
                case 'Adherencia':
                case 'Colgadas_porcentaje':
                case 'AHT':
                case 'Retardos':
                case 'Faltas':
                case 'Localizadores':
                case 'FC':
                    $colspan=" colspan=1";
                    $q_title="<th>Cuartil</th>";
                    break;
                case 'Nombre':
                    $colspan=" colspan=2";
                    $q_title="";
                    break;
                default:
                    $colspan=" colspan=1";
                    $q_title="";
                    break;
            }
            $campo_ok=str_replace('_','<br>',$campo);
            echo "\t<th$colspan>$campo_ok</th>$q_title\n";
            $csv_hdr.=$campo.",";
        }
        unset($key);
        unset($campo);
        $csv_hdr=substr($csv_hdr,0,-1);
    ?>
    </tr>
    </thead>
    <tbody>
    <?php
        foreach($data_asesores as $key => $info){
            $class="";
            switch($info['Esquema']){
                case 4:
                    if($info['Sesiones']<$avg_ses4){$class="class='not'";}
                    break;
                case 6:
                    if($info['Sesiones']<$avg_ses6){$class="class='not'";}
                    break;
                case 8:
                    if($info['Sesiones']<$avg_ses8){$class="class='not'";}
                    break;
                case 10:
                    if($info['Sesiones']<$avg_ses10){$class="class='not'";}
                    break;
            }

            echo "<tr style='text-align: center'>\n";
            $csv1_output="";
            $csv2_output="";
            foreach($field as $key2=> $parameter){

                switch($parameter){
                    case 'Utilizacion':
                    case 'Adherencia':
                    case 'Localizadores':
                    case 'FC':
                    case 'Monto':
                        if($class!="class='not'"){
                            if($info[$parameter]>=$sort[$parameter][$q_3]){$p_class="</td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td>"; $q=3;}else{$p_class="</td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td>"; $q=4;}
                            if($info[$parameter]>=$sort[$parameter][$q_2]){$p_class="</td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td>"; $q=2;}
                            if($info[$parameter]>=$sort[$parameter][$q_1]){$p_class="</td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td>"; $q=1;}
                        }else{$p_class="</td><td></td>";}
                        break;
                    case 'Colgadas_porcentaje':
                    case 'AHT':
                    case 'Retardos':
                    case 'Faltas':
                        if($class!="class='not'"){
                            if($info[$parameter]>$sort[$parameter][$q_1]){$p_class="</td><td>2 <img src='/images/yellowflag.png' alt='Q2' height='20' width='20'></td>"; $q=2;}else{$p_class="</td><td>1 <img src='/images/greenflag.png' alt='Q1' height='20' width='20'></td>"; $q=1;}
                            if($info[$parameter]>$sort[$parameter][$q_2]){$p_class="</td><td>3 <img src='/images/orangeflag.png' alt='Q3' height='20' width='20'></td>"; $q=3;}
                            if($info[$parameter]>$sort[$parameter][$q_3]){$p_class="</td><td>4 <img src='/images/redflag.png' alt='Q4' height='20' width='20'></td>"; $q=4;}
                        }else{$p_class="</td><td></td>";}
                        break;
                    case 'Nombre':
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
                    case 'Colgadas_porcentaje':
                    case 'FC':
                        $result=number_format($info[$parameter]*100,2)." %";
                        break;
                    case 'Monto':
                        $result="$".number_format($info[$parameter],2);
                        break;
                    case 'AHT':
                    case 'Sesiones':
                    case 'Pausas_no_Productivas':
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
                $csv1_output.=$info[$parameter].",";
                $csv2_output.=$q.",";
            }
            echo "</tr>\n";
            $csv_output.="Datos,".substr($csv1_output,0,-1)."\n";
            $csv_output.="Cuartil,".substr($csv2_output,0,-1)."\n";
        }
    ?>
    </tbody>
</table>
</div>

<br>
<div class='qlegend' id='qlegend'><p id='qtlegend'></p></div>
<script>

$(document).ready(function()
    {
        $('#qlegend').hide();

        $("#info").tablesorter({
            theme: 'blue',
            headerTemplate: '{content}',
            stickyHeaders: "tablesorter-stickyHeader",
            // fix the column widths
            widthFixed: false,
            widgets: [ 'zebra','filter', 'output' ],
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
                output_encoding      : 'data:application/octet-stream;charset=utf8,'

            }
        });

        $("#config").tablesorter({
            theme: 'blue',
            headerTemplate: '{content}',
            stickyHeaders: "tablesorter-stickyHeader",
            // fix the column widths
            widthFixed: true,
            widgets: [ 'zebra'],
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
                stickyHeaders: "tablesorter-stickyHeader"

            }
        });

        $( "#accordion" ).accordion({
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
            filters['19'] = Colgadas_filter;
            filters['23'] = AHT_filter;
            filters['25'] = Retardos_filter;
            filters['27'] = Faltas_filter;
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
    }
);

</script>