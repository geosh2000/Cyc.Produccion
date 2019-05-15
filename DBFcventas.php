<?php
include("connectDB.php");
date_default_timezone_set('America/Bogota');


$json_display=$_GET['disp'];


$query="SELECT
		`N Corto` as Asesor, Calls, TIME_TO_SEC(Avg_Time_Calls) as AHT,
        d_PorCola.Fecha,
		InboundAllLocs/Calls as FC,
		InboundAllLocs, InboundMPLocs,InboundITLocs,InboundCOPALocs,InboundCOOMEVALocs,
		InboundAllMonto, InboundMPMonto,InboundITMonto,InboundCOPAMonto,InboundCOOMEVAMonto
	FROM Asesores LEFT JOIN d_PorCola ON Asesores.id=d_PorCola.asesor LEFT JOIN
	(
		SELECT
        		`N Corto` as Asesor, Fecha,
				COUNT(IF((PCRC=3 OR PCRC=4 OR PCRC=6),Localizador,NULL)) as InboundAllLocs,
        		COUNT(IF(Afiliado LIKE'%pricetravel.com.mx%' AND (PCRC=3 OR PCRC=4 OR PCRC=6),Localizador,NULL)) as InboundMPLocs,
        		COUNT(IF((PCRC=5),Localizador,NULL)) as OutboundLocs,
        		COUNT(IF(Afiliado LIKE'%intertours%' AND (PCRC=3 OR PCRC=4 OR PCRC=6),Localizador,NULL)) as InboundITLocs,
        		COUNT(IF(Afiliado LIKE'%copa%' AND (PCRC=3 OR PCRC=4 OR PCRC=6),Localizador,NULL)) as InboundCOPALocs,
        		COUNT(IF(Afiliado LIKE'%coomeva%' AND (PCRC=3 OR PCRC=4 OR PCRC=6),Localizador,NULL)) as InboundCOOMEVALocs,
        		SUM(IF((PCRC=3 OR PCRC=4 OR PCRC=6),Monto,NULL)) * Dolar as InboundAllMonto,
        		SUM(IF(Afiliado LIKE'%pricetravel.com.mx%' AND (PCRC=3 OR PCRC=4 OR PCRC=6),Monto,NULL)) * Dolar as InboundMPMonto,
        		SUM(IF((PCRC=5),Monto,NULL)) * Dolar as OutboundMonto,
        		SUM(IF(Afiliado LIKE'%intertours%' AND (PCRC=3 OR PCRC=4 OR PCRC=6),Monto,NULL)) * Dolar as InboundITMonto,
        		SUM(IF(Afiliado LIKE'%copa%' AND (PCRC=3 OR PCRC=4 OR PCRC=6),Monto,NULL)) * Dolar as InboundCOPAMonto,
        		SUM(IF(Afiliado LIKE'%coomeva%' AND (PCRC=3 OR PCRC=4 OR PCRC=6),Monto,NULL)) * Dolar as InboundCOOMEVAMonto
         FROM
        	(SELECT
        		a.Fecha, Afiliado, Localizador, SUM(Venta+OtrosIngresos+Egresos) as Monto, SUM(Venta) as Venta,
        		`id Departamento` as PCRC, `N Corto`, Dolar
        	FROM
        		d_Locs a,
        		Asesores b,
        		Fechas c
        	WHERE
        		a.asesor=b.id AND
        		a.Fecha=c.Fecha
        	GROUP BY
        		Localizador
        	HAVING
        		Venta>0 AND Monto>0) locs

        	GROUP BY
        		`N Corto`, Fecha
	) c
	ON Asesores.`N Corto`=c.Asesor AND d_PorCola.Fecha=c.Fecha
	WHERE d_PorCola.Skill=3 AND `id Departamento`=3  AND d_PorCola.Fecha='".date('Y-m-d')."'
	ORDER BY Asesor
	" ;
$result=mysql_query($query);

$numFC=mysql_numrows($result);

mysql_close();



$i=0;
while ($i < $numFC) {

$ncorto[$i]=mysql_result($result,$i,"Asesor");
if(mysql_result($result,$i,"InboundAllMonto")==NULL){$monto[$i]=0;}else{$monto[$i]=mysql_result($result,$i,"InboundAllMonto");}
if(mysql_result($result,$i,"Calls")==NULL){$calls[$i]=0;}else{$calls[$i]=mysql_result($result,$i,"Calls");}
if(mysql_result($result,$i,"InboundMPMonto")==NULL){$mmp[$i]=0;}else{$mmp[$i]=mysql_result($result,$i,"InboundMPMonto");}
if(mysql_result($result,$i,"InboundAllLocs")==NULL){$locs[$i]=0;}else{$locs[$i]=mysql_result($result,$i,"InboundAllLocs"); }
if(mysql_result($result,$i,"FC")==NULL){$fc[$i]=0;}else{$fc[$i]=number_format(mysql_result($result,$i,"FC")*100,2);}
if(mysql_result($result,$i,"Fecha")==NULL){$fecha[$i]=0;}else{$fecha[$i]=mysql_result($result,$i,"Fecha");}
if(mysql_result($result,$i,"AHT")==NULL){$aht[$i]=0;}else{$aht[$i]=mysql_result($result,$i,"AHT");}

$i++;
}

if(isset($_GET['disp'])){

switch($json_display){
    case 'calls':

}

//JSON
$a = array();
       $cols = array();
       $rows = array();
       $cols[] = array("id"=>"","label"=>"Asesor","pattern"=>"","type"=>"string");
       switch($json_display){
            case 'calls':
                $cols[] = array("id"=>"","label"=>"Llamadas","pattern"=>"","type"=>"number");
                $cols[] = array("id"=>"","label"=>"Localizadores","pattern"=>"","type"=>"number");
                break;
            case 'montos':
                $cols[] = array("id"=>"","label"=>"Total","pattern"=>"","type"=>"number");
                $cols[] = array("id"=>"","label"=>"MP","pattern"=>"","type"=>"number");
                break;
            case 'aht':
                $av_aht=array_sum($aht)/array_count_values($aht);
                $cols[] = array("id"=>"","label"=>"AHT","pattern"=>"","type"=>"number");
                default;
            case 'fc':
                $cols[] = array("id"=>"","label"=>"FC","pattern"=>"","type"=>"number");
                default;
        }

       foreach($ncorto as $key => $asesor){
            switch($json_display){
                case 'calls':
                    $rows[] = array("c"=>array(array("v"=>$asesor,"f"=>null),array("v"=>$calls[$key],"f"=>null),array("v"=>$locs[$key],"f"=>null)));
                    break;
                case 'montos':
                    $rows[] = array("c"=>array(array("v"=>$asesor,"f"=>null),array("v"=>$monto[$key],"f"=>null),array("v"=>$mmp[$key],"f"=>null)));
                    break;
                case 'aht':
                    $rows[] = array("c"=>array(array("v"=>$asesor,"f"=>null),array("v"=>$aht[$key],"f"=>null),));
                    default;
                case 'fc':
                    $rows[] = array("c"=>array(array("v"=>$asesor,"f"=>null),array("v"=>$fc[$key],"f"=>null),));
                    default;
            }

       }
       unset($key,$asesor);
       $a = array("cols"=>$cols,"rows"=>$rows);







echo  json_encode($a);
}
?>