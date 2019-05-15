<?
include("../DBCallsHoraV.php");
include("../connectDB.php");
include("../common/erlangC.php");
date_default_timezone_set('America/Mexico_City');

if($_GET['date']==NULL){$date=date('Y/m/d');}else{$date=date('Y/m/d',strtotime($_GET['date']));}
$skill=$_GET['skill'];

switch($skill){
    case "Ventas":
		$s=3;
        $aht=500;
        $tat=20;
        $slr=0.8;
		break;
	case "Servicio a Cliente":
		$s=4;
		$aht=600;
        $tat=30;
        $slr=0.7;
		break;
	case "Trafico MP":
		$s=9;
		$aht=600;
        $tat=30;
        $slr=0.7;
		break;
	case "Trafico MT":
		$s=8;
		$aht=600;
        $tat=30;
        $slr=0.7;
		break;
	case "Soporte Agencias":
		$s=7;
		$aht=0600;
        $tat=30;
        $slr=0.7;
		break;
    case "Corporativo":
		$s=13;
		$aht=600;
        $tat=30;
        $slr=0.7;
		break;
}

//get ids
$queryT="SELECT * FROM `Historial Llamadas` WHERE (Fecha='$date' AND Skill='$skill')";
;

$queryF="SELECT
            *
            FROM
                Fechas
            LEFT JOIN
	            (SELECT
                    *
                FROM
                    `Historial Llamadas`
                WHERE
                    Skill='$skill'
                ) as Llamadas
        	ON
        	    WEEK(Fechas.Fecha- INTERVAL 365 day,1)=WEEK(Llamadas.Fecha- INTERVAL 365 day,1)-IF(Fechas.Fecha BETWEEN '2016-03-14' AND '2016-04-03',1,0)  AND
        	    WEEKDAY(Fechas.Fecha- INTERVAL 365 day)+1=WEEKDAY(Llamadas.Fecha- INTERVAL 365 day)+1
        	WHERE
                Fechas.Fecha='$date' AND
                YEAR(Llamadas.Fecha)=YEAR(Fechas.Fecha)-1";

$resultT=mysql_query($queryT);
$resultF=mysql_query($queryF);
//echo "$queryT<br>$queryF<br><br>";

$i=1;
while($i<=48){
    $c_td[$i]=mysql_result($resultT,0,$i+5);
    $c_f[$i]=intval(mysql_result($resultF,0,$i+15)*mysql_result($resultF,0,'forecast_'.$s));
    if($c_f[$i]==0){
        $prec[$i]=1;
    }else{
        $prec[$i]=$c_td[$i]/$c_f[$i];
    }

$i++;
}
//echo print_r($c_td)."<br>".print_r($c_f)."<br>";

$td=mysql_result($resultT,0,"id");
$fd=$td;


//JSON
$a = array();
       $cols = array();
       $rows = array();
       $cols[] = array("id"=>"","label"=>"Hora","pattern"=>"","type"=>"number");
       $cols[] = array("id"=>"","label"=>"Today","pattern"=>"","type"=>"number"); 

       $cols[] = array("id"=>"","label"=>"Forecast","pattern"=>"","type"=>"number");
       $cols[] = array("id"=>"","label"=>"Precision %","pattern"=>"","type"=>"number");
       $cols[] = array("id"=>"","label"=>"Top H Prec.","pattern"=>"","type"=>"number");
       $cols[] = array("id"=>"","label"=>"Top L Prec.","pattern"=>"","type"=>"number");

       foreach($c_f as $key => $forecast){
           if($key<19 || $key>=45){$precision=NULL;}else{$precision=$prec[$key];}

           $rows[] = array("c"=>array(array("v"=>$CVHora[$key-1],"f"=>null),array("v"=>$c_td[$key],"f"=>null),array("v"=>$forecast,"f"=>null),array("v"=>$precision,"f"=>null),array("v"=>1.15,"f"=>null),array("v"=>0.85,"f"=>null)));
       }

       $a = array("cols"=>$cols,"rows"=>$rows);
      


       



echo  json_encode($a);






?>