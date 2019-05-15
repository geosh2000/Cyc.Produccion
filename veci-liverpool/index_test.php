<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];


if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="tablas_f";

include("../connectDB.php");
include("../common/list_asesores.php");


//default timezone
date_default_timezone_set('America/Bogota');

//Get Variables
$dept=$_POST['depto'];
$from=$_POST['from'];
if($from==NULL){$from=date('Y-m-d', strtotime('-5 days'));}else{$from=date('Y-m-d', strtotime($_POST['from']));  }
$to=$_POST['to'];
if($to==NULL){$to=date('Y-m-d', strtotime('-1 days'));}else{$to=date('Y-m-d', strtotime($_POST['to']));  }
$classid=1;


//Function PrintRows
function printRows($variable,$title,$group,$format,$type='td'){
    global    $data,$TotalFechas,$class, $classid;
    if($classid % 2 == 0){$class="pair";}else{$class="odd";}
    if($classid==1){$class="title";}
    echo "\t<tr>\n";
    echo "\t\t<$type style='text-align:left'>$title</$type>\n<$type>$group</$type>\n";
    $x=0;
    while($x<=$TotalFechas){
        echo "\t\t<$type>";
        switch($format){
            case "num":
                echo number_format($data[$variable][$x]);
                break;
            case "%":
                echo number_format(($data[$variable][$x]*100),2)."%";
                break;
            case "$":
                echo "$".number_format($data[$variable][$x],2);
                break;
            case "na":
                echo $data[$variable][$x];
                break;
            case "fecha":
                echo date('l',strtotime($data[$variable][$x]))."<br>".date('d-M-y',strtotime($data[$variable][$x]));
                break;
            default:
                echo $data[$variable][$x];
                break;
        }

        echo "</$type>\n";
    $x++;
    }
    echo "\t</tr>\n";
    $classid++;
}

//Function PrintRowsAcumulado
function printRowsAc($title,$format,$var){
    global    $data_ac,$class, $classid;
    $var1=$var.'mp';
    $var2=$var.'it';
    $var3=$var.'copa';
    $var4=$var.'ltb';
    if($classid % 2 == 0){$class="pair";}else{$class="odd";}
    if($classid==1){$class="title";}
    echo "\t<tr class='$class'>\n";
    echo "\t\t<td class='title'>$title</td>\n";

        switch($format){
            case "num":
                echo "\t\t<td>";
                echo number_format($data_ac[$var]);
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo number_format($data_ac[$var1]);
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo number_format($data_ac[$var2]);
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo number_format($data_ac[$var3]);
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo number_format($data_ac[$var4]);
                echo "\t\t</td>\n";
                break;
            case "%":
                echo "\t\t<td>";
                echo number_format(($data_ac[$var]*100),2)."%";
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo number_format(($data_ac[$var1]*100),2)."%";
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo number_format(($data_ac[$var2]*100),2)."%";
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo number_format(($data_ac[$var3]*100),2)."%";
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo number_format(($data_ac[$var4]*100),2)."%";
                echo "\t\t</td>\n";
                break;
            case "$":
                echo "\t\t<td>";
                echo "$".number_format($data_ac[$var],2);
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo "$".number_format($data_ac[$var1],2);
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo "$".number_format($data_ac[$var2],2);
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo "$".number_format($data_ac[$var3],2);
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo "$".number_format($data_ac[$var4],2);
                echo "\t\t</td>\n";
                break;
            case "na":
                 echo "\t\t<td>";
                echo $data_ac[$var];
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo $data_ac[$var1];
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo $data_ac[$var2];
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo $data_ac[$var3];
                echo "\t\t</td>\n";
                echo "\t\t<td>";
                echo $data_ac[$var4];
                echo "\t\t</td>\n";
                break;
            default:
                echo $data[$variable][$x];
                break;
        }


    echo "\t</tr>\n";
    $classid++;
}

function createVariable($nombre,$variable1,$variable2,$operacion){
    global $data, $TotalFechas;
        $x=0;
        while($x<=$TotalFechas){
            switch($operacion){
                    case "+":
                        $data[$nombre][$x]=$data[$variable1][$x] + $data[$variable2][$x];
                        break;
                    case "*":
                        $data[$nombre][$x]=$data[$variable1][$x] * $data[$variable2][$x];
                        break;
                    case "/":
                        $data[$nombre][$x]=$data[$variable1][$x] / $data[$variable2][$x];
                        break;
                    case "-":
                        $data[$nombre][$x]=$data[$variable1][$x] - $data[$variable2][$x];
                        break;
            }
        $x++;
        }
}

function createVariableAc($nombre,$variable1,$variable2,$operacion){
    global $data_ac;
    $nombre1=$nombre.'mp';
    $nombre2=$nombre.'it';
    $nombre3=$nombre.'copa';
    $nombre4=$nombre.'ltb';
    $variable11=$variable1.'mp';
    $variable12=$variable1.'it';
    $variable13=$variable1.'copa';
    $variable14=$variable1.'ltb';
    $variable21=$variable2.'mp';
    $variable22=$variable2.'it';
    $variable23=$variable2.'copa';
    $variable24=$variable2.'ltb';
        $x=0;
        switch($operacion){
                    case "+":
                        $data_ac[$nombre]=$data_ac[$variable1] + $data_ac[$variable2];
                        $data_ac[$nombre1]=$data_ac[$variable11] + $data_ac[$variable21];
                        $data_ac[$nombre2]=$data_ac[$variable12] + $data_ac[$variable22];
                        $data_ac[$nombre3]=$data_ac[$variable13] + $data_ac[$variable23];
                        $data_ac[$nombre4]=$data_ac[$variable14] + $data_ac[$variable24];
                        break;
                    case "*":
                        $data_ac[$nombre]=$data_ac[$variable1] * $data_ac[$variable2];
                        $data_ac[$nombre1]=$data_ac[$variable11] * $data_ac[$variable21];
                        $data_ac[$nombre2]=$data_ac[$variable12] * $data_ac[$variable22];
                        $data_ac[$nombre3]=$data_ac[$variable13] * $data_ac[$variable23];
                        $data_ac[$nombre4]=$data_ac[$variable14] * $data_ac[$variable24];
                        break;
                    case "/":
                        $data_ac[$nombre]=$data_ac[$variable1] / $data_ac[$variable2];
                        $data_ac[$nombre1]=$data_ac[$variable11] / $data_ac[$variable21];
                        $data_ac[$nombre2]=$data_ac[$variable12] / $data_ac[$variable22];
                        $data_ac[$nombre3]=$data_ac[$variable13] / $data_ac[$variable23];
                        $data_ac[$nombre4]=$data_ac[$variable14] / $data_ac[$variable24];
                        break;
                    case "-":
                        $data_ac[$nombre]=$data_ac[$variable1] - $data_ac[$variable2];
                        $data_ac[$nombre1]=$data_ac[$variable11] - $data_ac[$variable21];
                        $data_ac[$nombre2]=$data_ac[$variable12] - $data_ac[$variable22];
                        $data_ac[$nombre3]=$data_ac[$variable13] - $data_ac[$variable23];
                        $data_ac[$nombre4]=$data_ac[$variable14] - $data_ac[$variable24];
                        break;
            }
        $x++;



}

//Query
$query="SELECT
	*
FROM
	PCRCs b,
	(
SELECT
	Telefonia.Fecha, Telefonia.Skill,
    VolumenTotal, VolumenMP, VolumenIT, VolumenCOPA, VolumenLTB,
    AnsweredTotal, AnsweredMP, AnsweredIT, AnsweredCOPA, AnsweredLTB,
    AnsweredRealTotal, AnsweredRealMP, AnsweredRealIT, AnsweredRealCOPA, AnsweredRealLTB,
    AbandonedTotal, AbandonedMP, AbandonedIT, AbandonedCOPA, AbandonedLTB,
    TransferedTotal, TransferedMP, TransferedIT, TransferedCOPA, TransferedLTB,
    TransferedMinTotal, TransferedMinMP, TransferedMinIT, TransferedMinCOPA, TransferedMinLTB,
    SLA20Total, SLA20MP, SLA20IT, SLA20COPA, SLA20LTB,
    SLA30Total, SLA30MP, SLA30IT, SLA30COPA, SLA30LTB,
    AHT_RealTotal, AHT_RealMP, AHT_RealIT, AHT_RealCOPA, AHT_RealLTB,
    ASATotal, ASAMP, ASAIT, ASACOPA, ASALTB,
    Talking_TimeTotal, Talking_TimeMP, Talking_TimeIT, Talking_TimeCOPA, Talking_TimeLTB,
    Asesores_Conectados, NivelPrincipal, NivelApoyo,
    Total_Sesiones, Total_Tiempo_Pausas, Total_Pausas, MXN, USD, COP, Total,
    Localizadores.Localizadores as LocalizadoresTotal,
    Localizadores.LocalizadoresMP,
    Localizadores.LocalizadoresIT,
    Localizadores.LocalizadoresLTB,
    Localizadores.LocalizadoresCOPA,
    Localizadores.MontoTotal,
    Localizadores.MontoMP,
    Localizadores.MontoIT,
    Localizadores.MontoLTB,
    Localizadores.MontoCOPA,
    Dolar
	FROM
		# Telefonia
		(
				SELECT
						a.Fecha, b.Skill as Skill, COUNT(ac_id) as VolumenTotal,
						COUNT(IF(c.Canal LIKE '%MP MX%',ac_id,NULL)) as VolumenMP,
						COUNT(IF(c.Canal LIKE '%intertours%',ac_id,NULL)) as VolumenIT,
						COUNT(IF(c.Canal LIKE '%copa%',ac_id,NULL)) as VolumenCOPA,
						COUNT(IF(c.Canal LIKE '%ltb%',ac_id,NULL)) as VolumenLTB,
						COUNT(IF(Answered=1,1,NULL)) as AnsweredTotal,
						COUNT(IF(Answered=1 AND c.Canal LIKE '%MP MX%',1,NULL)) as AnsweredMP,
						COUNT(IF(Answered=1 AND c.Canal LIKE '%intertours%',1,NULL)) as AnsweredIT,
						COUNT(IF(Answered=1 AND c.Canal LIKE '%copa%',1,NULL)) as AnsweredCOPA,
						COUNT(IF(Answered=1 AND c.Canal LIKE '%ltb%',1,NULL)) as AnsweredLTB,
						COUNT(IF(Answered=0,1,NULL)) as AbandonedTotal,
						COUNT(IF(Answered=0 AND c.Canal LIKE '%MP MX%',1,NULL)) as AbandonedMP,
						COUNT(IF(Answered=0 AND c.Canal LIKE '%intertours%',1,NULL)) as AbandonedIT,
						COUNT(IF(Answered=0 AND c.Canal LIKE '%copa%',1,NULL)) as AbandonedCOPA,
						COUNT(IF(Answered=0 AND c.Canal LIKE '%ltb%',1,NULL)) as AbandonedLTB,
						COUNT(IF(Answered=1 AND ((Desconexion='Transferida' AND Duracion_Real>='00:01:00') OR Desconexion!='Transferida'),1,NULL)) as AnsweredRealTotal,
						COUNT(IF(Answered=1 AND ((Desconexion='Transferida' AND Duracion_Real>='00:01:00') OR Desconexion!='Transferida') AND c.Canal LIKE '%MP MX%',1,NULL)) as AnsweredRealMP,
						COUNT(IF(Answered=1 AND ((Desconexion='Transferida' AND Duracion_Real>='00:01:00') OR Desconexion!='Transferida') AND c.Canal LIKE '%intertours%',1,NULL)) as AnsweredRealIT,
						COUNT(IF(Answered=1 AND ((Desconexion='Transferida' AND Duracion_Real>='00:01:00') OR Desconexion!='Transferida') AND c.Canal LIKE '%copa%',1,NULL)) as AnsweredRealCOPA,
						COUNT(IF(Answered=1 AND ((Desconexion='Transferida' AND Duracion_Real>='00:01:00') OR Desconexion!='Transferida') AND c.Canal LIKE '%ltb%',1,NULL)) as AnsweredRealLTB,
						COUNT(IF(Desconexion='Transferida',1,NULL)) as TransferedTotal,
						COUNT(IF(Desconexion='Transferida' AND c.Canal LIKE '%MP MX%',1,NULL)) as TransferedMP,
						COUNT(IF(Desconexion='Transferida' AND c.Canal LIKE '%intertours%',1,NULL)) as TransferedIT,
						COUNT(IF(Desconexion='Transferida' AND c.Canal LIKE '%copa%',1,NULL)) as TransferedCOPA,
						COUNT(IF(Desconexion='Transferida' AND c.Canal LIKE '%ltb%',1,NULL)) as TransferedLTB,
						COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:01:00',1,NULL)) as TransferedMinTotal,
						COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:01:00' AND c.Canal LIKE '%MP MX%',1,NULL)) as TransferedMinMP,
						COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:01:00' AND c.Canal LIKE '%intertours%',1,NULL)) as TransferedMinIT,
						COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:01:00' AND c.Canal LIKE '%copa%',1,NULL)) as TransferedMinCOPA,
						COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:01:00' AND c.Canal LIKE '%ltb%',1,NULL)) as TransferedMinLTB,
						COUNT(IF(Espera<'00:00:20',1,NULL)) as SLA20Total,
						COUNT(IF(Espera<'00:00:20' AND c.Canal LIKE '%MP MX%',1,NULL)) as SLA20MP,
						COUNT(IF(Espera<'00:00:20' AND c.Canal LIKE '%intertours%',1,NULL)) as SLA20IT,
						COUNT(IF(Espera<'00:00:20' AND c.Canal LIKE '%copa%',1,NULL)) as SLA20COPA,
						COUNT(IF(Espera<'00:00:20' AND c.Canal LIKE '%ltb%',1,NULL)) as SLA20LTB,
						COUNT(IF(Espera<'00:00:30',1,NULL)) as SLA30Total,
						COUNT(IF(Espera<'00:00:30' AND c.Canal LIKE '%MP MX%',1,NULL)) as SLA30MP,
						COUNT(IF(Espera<'00:00:30' AND c.Canal LIKE '%intertours%',1,NULL)) as SLA30IT,
						COUNT(IF(Espera<'00:00:30' AND c.Canal LIKE '%copa%',1,NULL)) as SLA30COPA,
						COUNT(IF(Espera<'00:00:30' AND c.Canal LIKE '%ltb%',1,NULL)) as SLA30LTB,
						round(AVG(TIME_TO_SEC(Duracion)),2) as AHTTotal,
						round(AVG(IF(c.Canal LIKE '%MP MX%',TIME_TO_SEC(Duracion),NULL)),2) as AHTMP,
						round(AVG(IF(c.Canal LIKE '%intertours%',TIME_TO_SEC(Duracion),NULL)),2) as AHTIT,
						round(AVG(IF(c.Canal LIKE '%copa%',TIME_TO_SEC(Duracion),NULL)),2) as AHTCOPA,
						round(AVG(IF(c.Canal LIKE '%ltb%',TIME_TO_SEC(Duracion),NULL)),2) as AHTLTB,
						round(AVG(TIME_TO_SEC(Duracion_Real)),2) as AHT_RealTotal,
						round(AVG(IF(c.Canal LIKE '%MP MX%',TIME_TO_SEC(Duracion_Real),NULL)),2) as AHT_RealMP,
						round(AVG(IF(c.Canal LIKE '%intertours%',TIME_TO_SEC(Duracion_Real),NULL)),2) as AHT_RealIT,
						round(AVG(IF(c.Canal LIKE '%copa%',TIME_TO_SEC(Duracion_Real),NULL)),2) as AHT_RealCOPA,
						round(AVG(IF(c.Canal LIKE '%ltb%',TIME_TO_SEC(Duracion_Real),NULL)),2) as AHT_RealLTB,
						round(AVG(IF(Answered=1,TIME_TO_SEC(Espera),NULL)),2) as ASATotal,
						round(AVG(IF(Answered=1 AND c.Canal LIKE '%MP MX%',TIME_TO_SEC(Espera),NULL)),2) as ASAMP,
						round(AVG(IF(Answered=1 AND c.Canal LIKE '%intertours%',TIME_TO_SEC(Espera),NULL)),2) as ASAIT,
						round(AVG(IF(Answered=1 AND c.Canal LIKE '%copa%',TIME_TO_SEC(Espera),NULL)),2) as ASACOPA,
						round(AVG(IF(Answered=1 AND c.Canal LIKE '%ltb%',TIME_TO_SEC(Espera),NULL)),2) as ASALTB,
						sum(if(Answered=1,TIME_TO_SEC(Duracion),NULL)) as Talking_TimeTotal,
						sum(if(Answered=1 AND c.Canal LIKE '%MP MX%',TIME_TO_SEC(Duracion),NULL)) as Talking_TimeMP,
						sum(if(Answered=1 AND c.Canal LIKE '%intertours%',TIME_TO_SEC(Duracion),NULL)) as Talking_TimeIT,
						sum(if(Answered=1 AND c.Canal LIKE '%copa%',TIME_TO_SEC(Duracion),NULL)) as Talking_TimeCOPA,
						sum(if(Answered=1 AND c.Canal LIKE '%ltb%',TIME_TO_SEC(Duracion),NULL)) as Talking_TimeLTB
					FROM
						t_Answered_Calls a
					LEFT JOIN
						Cola_Skill b
					ON
						a.Cola=b.Cola
					LEFT JOIN
						Dids c
					ON
						a.DNIS=c.DID AND
						a.Fecha>=c.Fecha
					WHERE
						 Skill IS NOT NULL AND
						 a.Fecha >= '2016-01-01'

					GROUP BY
						a.Fecha, Skill
		) as Telefonia

		LEFT JOIN
		# Sesiones y Pausas
		(
			SELECT
	Sesiones.Fecha as Fecha, Sesiones.Skill as Skill, Sesiones.Asesores_Conectados, Sesiones.Principal as NivelPrincipal, Sesiones.Apoyo as NivelApoyo, Sesiones.Total_Sesiones, Pausas.Total_Tiempo_Pausas, Pausas.Total_Pausas
	FROM
	(
		SELECT
				a.Fecha_in as Fecha, a.Skill as Skill, count(DISTINCT asesor) as Asesores_Conectados, SUM(TIME_TO_SEC(a.Duracion)) as Total_Sesiones,
				COUNT(DISTINCT IF(b.`id Departamento`=a.Skill,asesor,NULL)) as Principal, COUNT(DISTINCT IF(b.`id Departamento`!=a.Skill,asesor,NULL)) as Apoyo

				FROM
					t_Sesiones a,
					Asesores b

				WHERE
					a.asesor=b.id AND
					a.Hora_in >= '05:00:00'

				GROUP BY
					Fecha, a.Skill
			) as Sesiones
			LEFT JOIN

			(
				SELECT
					a.Fecha as Fecha, a.Skill as Skill, SUM(TIME_TO_SEC(Duracion)) as Total_Tiempo_Pausas, COUNT(pausas_id) as Total_Pausas
					FROM
						t_pausas a,
						Asesores b
					WHERE
						a.asesor=b.id
					GROUP BY
						Fecha, a.Skill

			) as Pausas

			ON
			Sesiones.Fecha=Pausas.Fecha AND
			Sesiones.Skill=Pausas.Skill
		) as Sesiones
		ON
		Telefonia.Fecha=Sesiones.Fecha AND
		Telefonia.Skill=Sesiones.Skill
		LEFT JOIN
		#Montos
		(
				SELECT
					a.Fecha, b.`id Departamento` as Skill, round(SUM(a.mxn_total),2) as MXN, round(SUM(a.usd_total),2) as USD, round(SUM(a.cop_total),2) as COP, round(SUM(a.mxn_total)+SUM(a.usd_total)*17.5+SUM(a.cop_total)*.0066,2) as Total
					FROM
						t_Montos_Diarios a,
						Asesores b
					WHERE
						a.asesor=b.id
					GROUP BY
						Fecha, b.`id Departamento`
		) as Montos
		ON
		Sesiones.Fecha=Montos.Fecha AND
		Sesiones.Skill=Montos.Skill
		LEFT JOIN
		#Localizadores
		(
				SELECT
					a.Fecha, b.`id Departamento` as Skill, COUNT(DISTINCT IF(Venta=0,NULL,Localizador)) as Localizadores,
					COUNT(DISTINCT IF(Venta!=0 AND Afiliado LIKE '%pricetravel.com%',Localizador,NULL)) as LocalizadoresMP,
					COUNT(DISTINCT IF(Venta!=0 AND Afiliado LIKE '%intertours%',Localizador,NULL)) as LocalizadoresIT,
					COUNT(DISTINCT IF(Venta!=0 AND Afiliado LIKE '%tiquetes%',Localizador,NULL)) as LocalizadoresLTB,
					COUNT(DISTINCT IF(Venta!=0 AND Afiliado LIKE '%copa%',Localizador,NULL)) as LocalizadoresCOPA,
					SUM(IF(Afiliado LIKE '%pricetravel.com%',Venta,0))+SUM(IF(Afiliado LIKE '%pricetravel.com%',OtrosIngresos,0))+SUM(IF(Afiliado LIKE '%pricetravel.com%',Egresos,0)) as MontoMP,
					SUM(IF(Afiliado LIKE '%intertours%',Venta,0))+SUM(IF(Afiliado LIKE '%intertours%',OtrosIngresos,0))+SUM(IF(Afiliado LIKE '%intertours%',Egresos,0)) as MontoIT,
					SUM(IF(Afiliado LIKE '%tiquetes%',Venta,0))+SUM(IF(Afiliado LIKE '%tiquetes%',OtrosIngresos,0))+SUM(IF(Afiliado LIKE '%tiquetes%',Egresos,0)) as MontoLTB,
                    SUM(IF(Afiliado LIKE '%copa%',Venta,0))+SUM(IF(Afiliado LIKE '%tiquetes%',OtrosIngresos,0))+SUM(IF(Afiliado LIKE '%tiquetes%',Egresos,0)) as MontoCOPA,
					SUM(Venta)+SUM(OtrosIngresos)+SUM(Egresos) as MontoTotal
					FROM
						t_Locs a,
						Asesores b
					WHERE
						a.asesor=b.id
					GROUP BY
						Fecha, b.`id Departamento`
		) as Localizadores
		ON
		Montos.Fecha=Localizadores.Fecha AND
		Montos.Skill=Localizadores.Skill
        LEFT JOIN
		#Dolar
		(
			SELECT
				Fecha, Dolar
				FROM
					Fechas

		) as Dolar
		ON Montos.Fecha=Dolar.Fecha
	) a
	WHERE
		a.Skill=b.id AND
        a.Skill=$dept AND
        Fecha >= '$from' AND
        Fecha <= '$to'
    ORDER BY
        Fecha";

$query_ac="SELECT
    id,Departamento,Skill, SUM(Llamadas) as Llamadas, SUM(Answered) as Answered, SUM(Abandoned) as Abandoned, SUM(Transfered) as Transfered, SUM(TransferedMin) as TransferedMin,
	SUM(AnsweredMP) as AnsweredMP, SUM(AnsweredIT) as AnsweredIT, SUM(AnsweredLTB) as AnsweredLTB, SUM(AnsweredCOPA) as AnsweredCOPA,
    SUM(AllCallsMP) as AllCallsMP, SUM(AllCallsIT) as AllCallsIT, SUM(AllCallsCOPA) as AllCallsCOPA,
	SUM(SLA20) as SLA20, SUM(SLA30) as SLA30, AVG(AHT_Real) as AHT_Real, AVG(ASA) as ASA, SUM(Talking_Time) as Talking_Time,
	SUM(Asesores_Conectados) as Asesores_Conectados, AVG(NivelPrincipal) as NivelPrincipal, AVG(NivelApoyo) as NivelApoyo,
	SUM(Total_Sesiones) as Total_Sesiones, SUM(Total_Tiempo_Pausas) as Total_Tiempo_Pausas, SUM(Total_Pausas) as Total_Pausas,
	SUM(MXN) as MXN, SUM(USD) as USD, SUM(COP) as COP, SUM(Total) as Total,
	SUM(Localizadores) as Localizadores, SUM(LocalizadoresMP) as LocalizadoresMP, SUM(LocalizadoresIT) as LocalizadoresIT, SUM(LocalizadoresLTB) as LocalizadoresLTB, SUM(LocalizadoresCOPA) as LocalizadoresCOPA,
	SUM(MontoTotal*Dolar) as MontoTotal, SUM(MontoMP*Dolar) as MontoMP, SUM(MontoIT*Dolar) as MontoIT, SUM(MontoLTB*Dolar) as MontoLTB, SUM(MontoCOPA*Dolar) as MontoCOPA
FROM
	PCRCs b,
	(
SELECT
	Telefonia.Fecha, Telefonia.Skill, Llamadas, Answered, Abandoned, Transfered, TransferedMin,
    AnsweredMP, AnsweredIT, AnsweredLTB, AnsweredCOPA, AllCallsMP,AllCallsIT,AllCallsCOPA,
    SLA20, SLA30, AHT_Real, ASA, Talking_Time, Asesores_Conectados, NivelPrincipal, NivelApoyo,
    Total_Sesiones, Total_Tiempo_Pausas, Total_Pausas, MXN, USD, COP, Total,
    Localizadores.Localizadores,Localizadores.LocalizadoresMP,Localizadores.LocalizadoresIT,Localizadores.LocalizadoresLTB,Localizadores.LocalizadoresCOPA,
    Localizadores.MontoTotal, Localizadores.MontoMP,Localizadores.MontoIT,Localizadores.MontoLTB,Localizadores.MontoCOPA,
    Dolar
	FROM
		# Telefonia
		(
				SELECT
						Fecha, b.Skill as Skill, COUNT(ac_id) as Llamadas, COUNT(IF(Answered=1,1,NULL)) as Answered, COUNT(IF(Answered=0,1,NULL)) as Abandoned,
						COUNT(IF(Answered=1 AND ((Desconexion='Transferida' AND Duracion_Real>='00:01:00') OR Desconexion='Agente' OR Desconexion='Llamante') AND
						(DNIS='0920' OR DNIS='3081' OR DNIS='3110' OR DNIS='3159' OR DNIS='6836' OR DNIS='2236' OR DNIS='2244' OR DNIS='2246' OR DNIS='2255' OR
						 DNIS='8830' OR DNIS='9146' OR DNIS='3352' OR DNIS='3389' OR DNIS='3396' OR DNIS='3509' OR DNIS='3553' OR DNIS='3566' OR DNIS='3584'),1,NULL)) as AnsweredIT,
						 COUNT(IF(Answered=1 AND ((Desconexion='Transferida' AND Duracion_Real>='00:01:00') OR Desconexion='Agente' OR Desconexion='Llamante') AND
						(DNIS='0600' OR DNIS='2296' OR DNIS='2297' OR DNIS='2485' OR DNIS='2482' OR DNIS='2486' OR DNIS='3111' OR DNIS='3167' OR DNIS='3195' OR
						 DNIS='3196' OR DNIS='3226' OR DNIS='8879' OR DNIS='9455' OR DNIS='2460'),1,NULL)) as AnsweredMP,
                         COUNT(IF((DNIS='0600' OR DNIS='2296' OR DNIS='2297' OR DNIS='2485' OR DNIS='2482' OR DNIS='2486' OR DNIS='3111' OR DNIS='3167' OR DNIS='3195' OR
						 DNIS='3196' OR DNIS='3226' OR DNIS='8879' OR DNIS='9455' OR DNIS='2460'),1,NULL)) as AllCallsMP,
                         COUNT(IF((DNIS='2488' OR DNIS='2963' OR DNIS='3238' OR DNIS='8825' OR DNIS='3526' OR DNIS='3527' OR DNIS='3557'),1,NULL)) as AllCallsCOPA,
                         COUNT(IF((DNIS='0920' OR DNIS='3081' OR DNIS='3110' OR DNIS='3159' OR DNIS='6836' OR DNIS='2236' OR DNIS='2244' OR DNIS='2246' OR DNIS='2255' OR
						 DNIS='8830' OR DNIS='9146' OR DNIS='3352' OR DNIS='3389' OR DNIS='3396' OR DNIS='3509' OR DNIS='3553' OR DNIS='3566' OR DNIS='3584'),1,NULL)) as AllCallsIT,
						 COUNT(IF(Answered=1 AND ((Desconexion='Transferida' AND Duracion_Real>='00:01:00') OR Desconexion='Agente' OR Desconexion='Llamante') AND
						(DNIS='4851' OR DNIS='3570' OR DNIS='3572' OR DNIS='6620'),1,NULL)) as AnsweredLTB,
						COUNT(IF(Answered=1 AND ((Desconexion='Transferida' AND Duracion_Real>='00:01:00') OR Desconexion='Agente' OR Desconexion='Llamante') AND
						(DNIS='2488' OR DNIS='2963' OR DNIS='3238' OR DNIS='8825' OR DNIS='3526' OR DNIS='3527'),1,NULL)) as AnsweredCOPA,
						COUNT(IF(Desconexion='Transferida',1,NULL)) as Transfered, COUNT(IF(Desconexion='Transferida' AND Duracion_Real<'00:01:00',1,NULL)) as TransferedMin,
						COUNT(IF(Espera<'00:00:20',1,NULL)) as SLA20,  COUNT(IF(Espera<'00:00:30',1,NULL)) as SLA30,
						round(AVG(TIME_TO_SEC(Duracion)),2) as AHT,  round(AVG(TIME_TO_SEC(Duracion_Real)),2) as AHT_Real,
						round(AVG(IF(Answered=1,TIME_TO_SEC(Espera),NULL)),2) as ASA,
						sum(if(Answered=1,TIME_TO_SEC(Duracion),NULL)) as Talking_Time
					FROM
						t_Answered_Calls a,
						Cola_Skill b
					WHERE
						a.Cola=b.Cola AND
						 Fecha >= '2016-01-01'

					GROUP BY
						Fecha, Skill
		) as Telefonia

		LEFT JOIN
		# Sesiones y Pausas
		(
			SELECT
	Sesiones.Fecha as Fecha, Sesiones.Skill as Skill, Sesiones.Asesores_Conectados, Sesiones.Principal as NivelPrincipal, Sesiones.Apoyo as NivelApoyo, Sesiones.Total_Sesiones, Pausas.Total_Tiempo_Pausas, Pausas.Total_Pausas
	FROM
	(
		SELECT
				a.Fecha_in as Fecha, a.Skill as Skill, count(DISTINCT asesor) as Asesores_Conectados, SUM(TIME_TO_SEC(a.Duracion)) as Total_Sesiones,
				COUNT(DISTINCT IF(b.`id Departamento`=a.Skill,asesor,NULL)) as Principal, COUNT(DISTINCT IF(b.`id Departamento`!=a.Skill,asesor,NULL)) as Apoyo

				FROM
					t_Sesiones a,
					Asesores b

				WHERE
					a.asesor=b.id AND
					a.Hora_in >= '05:00:00'

				GROUP BY
					Fecha, a.Skill
			) as Sesiones
			LEFT JOIN

			(
				SELECT
					a.Fecha as Fecha, a.Skill as Skill, SUM(TIME_TO_SEC(Duracion)) as Total_Tiempo_Pausas, COUNT(pausas_id) as Total_Pausas
					FROM
						t_pausas a,
						Asesores b
					WHERE
						a.asesor=b.id
					GROUP BY
						Fecha, a.Skill

			) as Pausas

			ON
			Sesiones.Fecha=Pausas.Fecha AND
			Sesiones.Skill=Pausas.Skill
		) as Sesiones
		ON
		Telefonia.Fecha=Sesiones.Fecha AND
		Telefonia.Skill=Sesiones.Skill
		LEFT JOIN
		#Montos
		(
				SELECT
					a.Fecha, b.`id Departamento` as Skill, round(SUM(a.mxn_total),2) as MXN, round(SUM(a.usd_total),2) as USD, round(SUM(a.cop_total),2) as COP, round(SUM(a.mxn_total)+SUM(a.usd_total)*17.5+SUM(a.cop_total)*.0066,2) as Total
					FROM
						t_Montos_Diarios a,
						Asesores b
					WHERE
						a.asesor=b.id
					GROUP BY
						Fecha, b.`id Departamento`
		) as Montos
		ON
		Sesiones.Fecha=Montos.Fecha AND
		Sesiones.Skill=Montos.Skill
		LEFT JOIN
		#Localizadores
		(
				SELECT
					a.Fecha, b.`id Departamento` as Skill, COUNT(DISTINCT IF(Venta=0,NULL,Localizador)) as Localizadores,
					COUNT(DISTINCT IF(Venta!=0 AND Afiliado LIKE '%pricetravel.com%',Localizador,NULL)) as LocalizadoresMP,
					COUNT(DISTINCT IF(Venta!=0 AND Afiliado LIKE '%intertours%',Localizador,NULL)) as LocalizadoresIT,
					COUNT(DISTINCT IF(Venta!=0 AND Afiliado LIKE '%tiquetes%',Localizador,NULL)) as LocalizadoresLTB,
					COUNT(DISTINCT IF(Venta!=0 AND Afiliado LIKE '%copa%',Localizador,NULL)) as LocalizadoresCOPA,
					SUM(IF(Afiliado LIKE '%pricetravel.com%',Venta,0))+SUM(IF(Afiliado LIKE '%pricetravel.com%',OtrosIngresos,0))+SUM(IF(Afiliado LIKE '%pricetravel.com%',Egresos,0)) as MontoMP,
					SUM(IF(Afiliado LIKE '%intertours%',Venta,0))+SUM(IF(Afiliado LIKE '%intertours%',OtrosIngresos,0))+SUM(IF(Afiliado LIKE '%intertours%',Egresos,0)) as MontoIT,
					SUM(IF(Afiliado LIKE '%tiquetes%',Venta,0))+SUM(IF(Afiliado LIKE '%tiquetes%',OtrosIngresos,0))+SUM(IF(Afiliado LIKE '%tiquetes%',Egresos,0)) as MontoLTB,
                    SUM(IF(Afiliado LIKE '%copa%',Venta,0))+SUM(IF(Afiliado LIKE '%tiquetes%',OtrosIngresos,0))+SUM(IF(Afiliado LIKE '%tiquetes%',Egresos,0)) as MontoCOPA,
					SUM(Venta)+SUM(OtrosIngresos)+SUM(Egresos) as MontoTotal
					FROM
						t_Locs a,
						Asesores b
					WHERE
						a.asesor=b.id
					GROUP BY
						Fecha, b.`id Departamento`
		) as Localizadores
		ON
		Montos.Fecha=Localizadores.Fecha AND
		Montos.Skill=Localizadores.Skill
        LEFT JOIN
		#Dolar
		(
			SELECT
				Fecha, Dolar
				FROM
					Fechas

		) as Dolar
		ON Montos.Fecha=Dolar.Fecha
	) a
	WHERE
		a.Skill=b.id AND
        a.Skill=$dept AND
        Fecha >= '$from' AND
        Fecha <= '$to'
    ORDER BY
        Fecha";

$result=mysql_query($query);
$result_ac=mysql_query($query_ac);
$num=mysql_numrows($result);
$numfield=mysql_num_fields($result);
$i=0;
while($i<$numfield){
    $field[$i]=mysql_field_name($result,$i);
$i++;
}
$i=0;
while($i<$num){
    if($i==0){
        $data_ac['departamento']=mysql_result($result_ac,$i,'Departamento');
         $data_ac['fecha']=mysql_result($result_ac,$i,'Fecha');
         $data_ac['llamadas']=mysql_result($result_ac,$i,'Llamadas');
         $data_ac['answered']=mysql_result($result_ac,$i,'Answered');
         $data_ac['answeredmp']=mysql_result($result_ac,$i,'AnsweredMP');
         $data_ac['llamadasmp']=mysql_result($result_ac,$i,'AllCallsMP');
         $data_ac['llamadasit']=mysql_result($result_ac,$i,'AllCallsIT');
         $data_ac['llamadascopa']=mysql_result($result_ac,$i,'AllCallsCOPA');
         $data_ac['answeredit']=mysql_result($result_ac,$i,'AnsweredIT');
         $data_ac['answeredltb']=mysql_result($result_ac,$i,'AnsweredLTB');
         $data_ac['answeredcopa']=mysql_result($result_ac,$i,'AnsweredCOPA');
         $data_ac['abandoned']=mysql_result($result_ac,$i,'Abandoned');
         $data_ac['transfered']=mysql_result($result_ac,$i,'Transfered');
         $data_ac['transfered_min']=mysql_result($result_ac,$i,'TransferedMin');
         $data_ac['sla20']=mysql_result($result_ac,$i,'SLA20');
         $data_ac['sla30']=mysql_result($result_ac,$i,'SLA30');
         $data_ac['aht']=mysql_result($result_ac,$i,'AHT_Real');
         $data_ac['asa']=mysql_result($result_ac,$i,'ASA');
         $data_ac['talking_time']=mysql_result($result_ac,$i,'Talking_Time');
         $data_ac['asesores_conectados']=mysql_result($result_ac,$i,'Asesores_Conectados');
         $data_ac['nivel_principal']=mysql_result($result_ac,$i,'NivelPrincipal');
         $data_ac['nivel_apoyo']=mysql_result($result_ac,$i,'NivelApoyo');
         $data_ac['total_sesiones']=mysql_result($result_ac,$i,'Total_Sesiones');
         $data_ac['total_tiempo_pausas']=mysql_result($result_ac,$i,'Total_Tiempo_Pausas');
         $data_ac['total_pausas']=mysql_result($result_ac,$i,'Total_Pausas');
         $data_ac['mxn']=mysql_result($result_ac,$i,'MXN');
         $data_ac['usd']=mysql_result($result_ac,$i,'USD');
         $data_ac['cop']=mysql_result($result_ac,$i,'COP');
         $data_ac['total_monto']=mysql_result($result_ac,$i,'Total');
         $data_ac['dolar']=mysql_result($result_ac,$i,'Dolar');
         $data_ac['total_monto']=mysql_result($result_ac,$i,'MontoTotal');
         $data_ac['total_montomp']=mysql_result($result_ac,$i,'MontoMP');
         $data_ac['total_montoit']=mysql_result($result_ac,$i,'MontoIT');
         $data_ac['total_montoltb']=mysql_result($result_ac,$i,'MontoLTB');
         $data_ac['total_montocopa']=mysql_result($result_ac,$i,'MontoCOPA');
         $data_ac['locs']=mysql_result($result_ac,$i,'Localizadores');
         $data_ac['locsmp']=mysql_result($result_ac,$i,'LocalizadoresMP');
         $data_ac['locsit']=mysql_result($result_ac,$i,'LocalizadoresIT');
         $data_ac['locsltb']=mysql_result($result_ac,$i,'LocalizadoresLTB');
         $data_ac['locscopa']=mysql_result($result_ac,$i,'LocalizadoresCOPA');
    }
     $x=0;
     while($x<$numfield){
        $data[$field[$x]][$i]=mysql_result($result,$i,$field[$x]);
     $x++;
     }
     $TotalFechas=$i;
$i++;
}
include("../common/scripts.php");

?>
<script type="text/javascript" src="/js/tablesorter/js/widgets/widget-output.js"></script>
<script>
  $(function() {
    $( "#from" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3,
      onClose: function( selectedDate ) {
        $( "#to" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#to" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3,
      onClose: function( selectedDate ) {
        $( "#from" ).datepicker( "option", "maxDate", selectedDate );
      }
    });

    $('.tablesorter-childRow td').toggle();

        $('#tablesorter').tablesorter({
            theme: 'blue',
            sortList: [[1,1]],
            headerTemplate: '{content}',
            stickyHeaders: "tablesorter-stickyHeader",
            cssChildRow : "tablesorter-childRow",
            // fix the column widths
            widthFixed: false,
            widgets: [ 'zebra','filter','output'],
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
                  output_saveFileName  : 'mytable.csv',
                  // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
                  output_encoding      : 'data:application/octet-stream;charset=utf8,'

            }
        });


    // clicking the download button; all you really need is to
    // trigger an "output" event on the table
    $('#export').click(function(){
        $('#tablesorter').trigger('outputTable');

    });


    $( "#accordion" ).accordion({
      collapsible: true,
      heightStyle: "content",
      active: false
    });


    }
);
  </script>

<?php
include("../common/menu.php");

?>

<table width='100%' class='t2'><form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
    <tr class='title'>
         <th colspan="100">Tabla F <?php echo $data[departamento][0]; ?></th>
    </tr>
    <tr class='title'>
         <td>Departamento:</td>
         <td>Fecha inicial</td>
         <td>Fecha final</td>
         <td class='total' rowspan=2><input type="submit" name='consulta' value='consulta' /></td>
    </tr>
    <tr class='pair'>
         <td class='pair'><select name="depto" required><?php list_departamentos($dept); ?></select></td>
         <td><input type="text" id="from" name="from" value='<?php echo $from; ?>' required></td>
         <td><input type="text" id="to" name="to" value='<?php echo $to; ?>' required></td>
    </tr>

</form></table>
<br><br>
<?php

    if(!isset($_POST['consulta'])){exit;}
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
    printRows('VolumenTotal','Volumen','Total','num');
    printRows('VolumenMP','Volumen','MP','num');
    printRows('VolumenIT','Volumen','IT','num');
    printRows('VolumenCOPA','Volumen','COPA','num');
    printRows('VolumenLTB','Volumen','LTB','num');
    printRows('AnsweredTotal','Contestadas','Total','num');
    printRows('AnsweredMP','Contestadas','MP','num');
    printRows('AnsweredIT','Contestadas','IT','num');
    printRows('AnsweredCOPA','Contestadas','COPA','num');
    printRows('AnsweredLTB','Contestadas','LTB','num');
    printRows('AbandonedTotal','Abandonadas','Total','num');
    printRows('AbandonedMP','Abandonadas','MP','num');
    printRows('AbandonedIT','Abandonadas','IT','num');
    printRows('AbandonedCOPA','Abandonadas','COPA','num');
    printRows('AbandonedLTB','Abandonadas','LTB','num');
    printRows('TransferedTotal','Transferidas','Total','num');
    printRows('TransferedMP','Transferidas','MP','num');
    printRows('TransferedIT','Transferidas','IT','num');
    printRows('TransferedCOPA','Transferidas','COPA','num');
    printRows('TransferedLTB','Transferidas','LTB','num');
    printRows('TransferedMinTotal','Transferidas <1 min','Total','num');
    printRows('TransferedMinMP','Transferidas <1 min','MP','num');
    printRows('TransferedMinIT','Transferidas <1 min','IT','num');
    printRows('TransferedMinCOPA','Transferidas <1 min','COPA','num');
    printRows('TransferedMinLTB','Transferidas <1 min','LTB','num');
    createVariable('slaOffered20Total','SLA20Total','VolumenTotal','/'); printRows('slaOffered20Total','SLA 20 seg','Total','%');
    createVariable('slaOffered20MP','SLA20MP','VolumenMP','/'); printRows('slaOffered20MP','SLA 20 seg','MP','%');
    createVariable('slaOffered20IT','SLA20IT','VolumenIT','/'); printRows('slaOffered20IT','SLA 20 seg','IT','%');
    createVariable('slaOffered20COPA','SLA20COPA','VolumenCOPA','/'); printRows('slaOffered20COPA','SLA 20 seg','COPA','%');
    createVariable('slaOffered20LTB','SLA20LTB','VolumenLTB','/'); printRows('slaOffered20LTB','SLA 20 seg','LTB','%');
    createVariable('slaOffered30Total','SLA30Total','VolumenTotal','/'); printRows('slaOffered30Total','SLA 30 seg','Total','%');
    createVariable('slaOffered30MP','SLA30MP','VolumenMP','/'); printRows('slaOffered30MP','SLA 30 seg','MP','%');
    createVariable('slaOffered30IT','SLA30IT','VolumenIT','/'); printRows('slaOffered30IT','SLA 30 seg','IT','%');
    createVariable('slaOffered30COPA','SLA30COPA','VolumenCOPA','/'); printRows('slaOffered30COPA','SLA 30 seg','COPA','%');
    createVariable('slaOffered30LTB','SLA30LTB','VolumenLTB','/'); printRows('slaOffered30LTB','SLA 30 seg','LTB','%');
    createVariable('AbandonTotal','AbandonedTotal','VolumenTotal','/'); printRows('AbandonTotal','Abandon %','Total','%');
    createVariable('AbandonMP','AbandonedMP','VolumenMP','/'); printRows('AbandonMP','Abandon %','MP','%');
    createVariable('AbandonIT','AbandonedIT','VolumenIT','/'); printRows('AbandonIT','Abandon %','IT','%');
    createVariable('AbandonCOPA','AbandonedCOPA','VolumenCOPA','/'); printRows('AbandonCOPA','Abandon %','COPA','%');
    createVariable('AbandonLTB','AbandonedLTB','VolumenLTB','/'); printRows('AbandonLTB','Abandon %','LTB','%');
    printRows('AHT_RealTotal','AHT','Total','num');
    printRows('AHT_RealMP','AHT','MP','num');
    printRows('AHT_RealIT','AHT','IT','num');
    printRows('AHT_RealCOPA','AHT','COPA','num');
    printRows('AHT_RealLTB','AHT','LTB','num');
    printRows('ASATotal','ASA','Total','num');
    printRows('ASAMP','ASA','MP','num');
    printRows('ASAIT','ASA','IT','num');
    printRows('ASACOPA','ASA','COPA','num');
    printRows('ASALTB','ASA','LTB','num');
    printRows('Talking_TimeTotal','Talking Time','Total','num');
    printRows('Talking_TimeMP','Talking Time','MP','num');
    printRows('Talking_TimeIT','Talking Time','IT','num');
    printRows('Talking_TimeCOPA','Talking Time','COPA','num');
    printRows('Talking_TimeLTB','Talking Time','LTB','num');
    printRows('LocalizadoresTotal','Localizadores','Total','num');
    printRows('LocalizadoresMP','Localizadores','MP','num');
    printRows('LocalizadoresIT','Localizadores','IT','num');
    printRows('LocalizadoresCOPA','Localizadores','COPA','num');
    printRows('LocalizadoresLTB','Localizadores','LTB','num');
    createVariable('FCTotal','LocalizadoresTotal','AnsweredRealTotal','/'); printRows('FCTotal','FC %','Total','%');
    createVariable('FCMP','LocalizadoresMP','AnsweredRealMP','/'); printRows('FCMP','FC %','MP','%');
    createVariable('FCIT','LocalizadoresIT','AnsweredRealIT','/'); printRows('FCIT','FC %','IT','%');
    createVariable('FCCOPA','LocalizadoresCOPA','AnsweredRealCOPA','/'); printRows('FCCOPA','FC %','COPA','%');
    createVariable('FCLTB','LocalizadoresLTB','AnsweredRealLTB','/'); printRows('FCLTB','FC %','LTB','%');
    createVariable('Monto_MXNTotal','MontoTotal','Dolar','*'); printRows('Monto_MXNTotal','Monto %','Total','%');
    createVariable('Monto_MXNMP','MontoMP','Dolar','*'); printRows('Monto_MXNMP','Monto %','MP','%');
    createVariable('Monto_MXNIT','MontoIT','Dolar','*'); printRows('Monto_MXNIT','Monto %','IT','%');
    createVariable('Monto_MXNCOPA','MontoCOPA','Dolar','*'); printRows('Monto_MXNCOPA','Monto %','COPA','%');
    createVariable('Monto_MXNLTB','MontoLTB','Dolar','*'); printRows('Monto_MXNLTB','Monto %','LTB','%');
    createVariable('utilizacion_tiempo','Total_Sesiones','Total_Tiempo_Pausas','-');
    createVariable('utilizacion_porcentaje','utilizacion_tiempo','Total_Sesiones','/'); printRows('utilizacion_porcentaje','Utilizaci&oacuten','Total','%');
    createVariable('ocupacion','Talking_TimeTotal','utilizacion_tiempo','/'); printRows('ocupacion','Ocupaci&oacuten','Total','%');
    printRows('NivelPrincipal','Asesores conectados<br>First Level','Total','na');
    printRows('NivelApoyo','Asesores conectados<br>Apoyo','Total','na');
    printRows('Dolar','Tipo de Cambio','Total','$');



?>
            </tbody>
            </table>
        </div>
    <h3>Acumulado de fechas Seleccionadas</h3>
        <div>
            <table width='100%' class='t2'>
                <tr class='title'>
                    <th>Concepto</th>
                    <th>Total</th>
                    <th>MP</th>
                    <th>IT</th>
                    <th>COPA</th>
                    <th>LTB</th>
                </tr>
                <?php
                  printRowsAc('Volumen','num','llamadas');
                  printRowsAc('Contestadas','num','answered');
                  createVariableAc('c_reales','answered','transfered_min','-');
                  printRowsAc('Contestadas Reales','num','c_reales');
                  createVariableAc('c_abandoned','llamadas','answered','-');
                  printRowsAc('Abandonadas','num','c_abandoned');
                  printRowsAc('Transferidas','num','transfered');
                  printRowsAc('Abandonadas','num','abandoned');
                  createVariableAc('c_sla20','sla20','llamadas','/');
                  createVariableAc('c_sla30','sla30','llamadas','/');
                  createVariableAc('c_abandon','abandoned','llamadas','/');
                  printRowsAc('SLA 20 seg','%','c_sla20');
                  printRowsAc('SLA 30 seg','%','c_sla30');
                  printRowsAc('Abandon %','%','c_abandon');
                  printRowsAc('AHT','num','aht');
                  printRowsAc('ASA','num','asa');
                  printRowsAc('Reservas','num','locs');
                  createVariableAc('c_fc','locs','c_reales','/');
                  printRowsAc('FC %','%','c_fc');
                  createVariableAc('c_utilizacion_tiempo','total_sesiones','total_tiempo_pausas','-');
                  createVariableAc('c_utilizacion_porcentaje','c_utilizacion_tiempo','total_sesiones','/');
                  printRowsAc('Utilizaci&oacuten','%','c_utilizacion_porcentaje');
                  createVariableAc('c_ocupacion','talking_time','c_utilizacion_tiempo','/');
                  printRowsAc('Ocupaci&oacuten','%','c_ocupacion');
                  printRowsAc('Monto','$','total_monto');
                  printRowsAc('Asesores (avg)<br>First Level','num','nivel_principal');
                  printRowsAc('Asesores (avg)<br>Apoyo','num','nivel_principal');

                ?>
            </table>

        </div>

</div>