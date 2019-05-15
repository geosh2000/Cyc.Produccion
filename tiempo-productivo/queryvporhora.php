<?php
include("../connectDB.php");
date_default_timezone_set('America/Mexico_city');
header("content-type: application/json");

$asesor=$_GET['asesor'];
$fecha_i=$_GET['fechai'];
$fecha_f=$_GET['fechaf'];
$hoy=date('Y-m-d');
$type=$_GET['type'];
$md=$_GET['mdt'];
$mt=$_GET['mt'];
$depart=$_GET['skill'];

$query="SELECT
	a.Fecha, Hora, Localizador, SUM(Venta+OtrosIngresos+Egresos)*Dolar as Monto,
	IF(afiliado LIKE '%pricetravel.com.mx%','MP MX',IF(afiliado LIKE '%intertours%','Intertours','MT MX')) as Canal
FROM
	d_Locs a
LEFT JOIN
	Asesores b
ON
	a.asesor=b.id
LEFT JOIN
	Fechas c
ON
	a.Fecha=c.Fecha
WHERE
	a.Fecha='$hoy' AND
	`id Departamento`=$depart AND
	Activo=1
GROUP BY a.locs_id ORDER BY Hora";
$result=mysql_query($query);
$num=mysql_num_rows($result);

//echo "ERROR: ".mysql_error()."<br>$query<br>Rows: $num<br>";
$i=0;
$mdacumulado=0;
$locsacumulado=0;
while($i<$num){
    $fecha[$i]=mysql_result($result,$i,'Fecha');
    $hora[$i]=mysql_result($result,$i,'Hora');
    //echo mysql_result($result,$i,'Fecha')."<br>";
    if(date("I", strtotime($fecha[$i]))==1){$minus="4";}else{$minus="5";}
    $datetmp=strftime("%s", strtotime($fecha[$i].' '.$hora[$i].' -'.$minus.' hours'))*1000;
    $xdata[$i]=$datetmp;

    $acumulado+=mysql_result($result,$i,'Monto');
    if(mysql_result($result,$i,'Venta')==0){$locsacumulado++;}
    $acum[$i]=array($datetmp,intval($acumulado));
    $locsacum[$i]=array($datetmp,$locsacumulado);

    if(mysql_result($result,$i,'Canal')=='MP MX'){$acumuladomp+=mysql_result($result,$i,'Monto');}
    if(mysql_result($result,$i,'Canal')=='MP MX' && mysql_result($result,$i,'Venta')==0){$locsacumuladomp++;}
    $acummp[$i]=array($datetmp,intval($acumuladomp));
    $locsacummp[$i]=array($datetmp,$locsacumuladomp);

    if(mysql_result($result,$i,'Canal')=='Intertours'){$acumuladoit+=mysql_result($result,$i,'Monto');}
    if(mysql_result($result,$i,'Canal')=='Intertours' && mysql_result($result,$i,'Venta')==0){$locsacumuladoit++;}
    $acumit[$i]=array($datetmp,intval($acumuladoit));
    $locsacumit[$i]=array($datetmp,$locsacumuladoit);

    if(mysql_result($result,$i,'Canal')=='MT MX'){$acumuladomt+=mysql_result($result,$i,'Monto');}
    if(mysql_result($result,$i,'Canal')=='MT MX' && mysql_result($result,$i,'Venta')==0){$locsacumuladomt++;}
    $acummt[$i]=array($datetmp,intval($acumuladomt));
    $locsacummt[$i]=array($datetmp,$locsacumuladomt);


$i++;
}
    $xData=array();
    $datasets=array();
    $inf=array();
    $datamonto=array();
    $datalocs=array();


    $xData[]=array("categories"=>$fecha);

switch($type){
    case "montos":
        $datasets[]=array("name"=>"Monto ALL","data"=>$acum,"valueDecimals"=>2,"yAxis"=>0,"type"=>"spline","color"=>"#8BCC52","marker"=>array("enabled"=>false));
        $datasets[]=array("name"=>"Monto MP","data"=>$acummp,"valueDecimals"=>2,"yAxis"=>0,"type"=>"spline","color"=>"#EC6FA7","marker"=>array("enabled"=>false));
        $datasets[]=array("name"=>"Monto IT","data"=>$acumit,"valueDecimals"=>2,"yAxis"=>0,"type"=>"spline","color"=>"#4B5CDB","marker"=>array("enabled"=>false));
        $datasets[]=array("name"=>"Monto MT","data"=>$acummt,"valueDecimals"=>2,"yAxis"=>0,"type"=>"spline","color"=>"#DED354","marker"=>array("enabled"=>false));
        //$datasets[]=array("name"=>"Por dia","data"=>$monto,"xData"=>$xdata,"valueDecimals"=>2,"yAxis"=>1,"type"=>"column");
        //$datasets[]=array("name"=>"Acumulado","data"=>$acum,"xData"=>$xdata,"valueDecimals"=>2,"yAxis"=>0,"type"=>"spline");
        break;
    case "locs":
        $datasets[]=array("name"=>"Localizadores ALL","data"=>$locsacum,"valueDecimals"=>2,"yAxis"=>0,"spline"=>"column","color"=>"#8BCC52","marker"=>array("enabled"=>false));
        $datasets[]=array("name"=>"Localizadores MP","data"=>$locsacummp,"valueDecimals"=>2,"yAxis"=>0,"spline"=>"column","color"=>"#EC6FA7","marker"=>array("enabled"=>false));
        $datasets[]=array("name"=>"Localizadores IT","data"=>$locsacumit,"valueDecimals"=>2,"yAxis"=>0,"spline"=>"column","color"=>"#4B5CDB","marker"=>array("enabled"=>false));
        $datasets[]=array("name"=>"Localizadores MT","data"=>$locsacummt,"valueDecimals"=>2,"yAxis"=>0,"spline"=>"column","color"=>"#DED354","marker"=>array("enabled"=>false));
        break;
    case "montod":
        $info=$monto;
        echo  json_encode($info, JSON_PRETTY_PRINT);
        exit;
        break;
    case "fc":
        $datasets[]=array("name"=>"Por Dia","data"=>$fc,"valueDecimals"=>2,"yAxis"=>0,"type"=>"column","color"=>"#85ACDB");
        $datasets[]=array("name"=>"Acumulado","data"=>$acumfc,"valueDecimals"=>2,"yAxis"=>0,"type"=>"line","color"=>"##C7000D");
        break;
}
    //$datasets[]=array("name"=>"FC","data"=>$fc,"valueDecimals"=>0,"yAxis"=>1,"type"=>"spline");


    $info=array("xData"=>$xData,"datasets"=>$datasets);
echo  json_encode($info, JSON_PRETTY_PRINT);

//print_r($fechas);
?>

