<?php
include_once("../modules/modules.php");

timeAndRegion::setRegion('Cun');

header("content-type: application/json");
setlocale(LC_TIME,'es_ES');

//GET Info
$asesor=$_GET['asesor'];
$fecha_i=$_GET['fechai'];
$fecha_f=$_GET['fechaf'];
$type=$_GET['type'];
$md=$_GET['mdt'];
$mt=$_GET['mt'];
$dep=$_GET['dep'];

$connectdb=Connection::mysqliDB('CC');

$query="SELECT
          a.Fecha, asesor, NombreAsesor(asesor,1) as `N Corto`, 
          rsvas as Localizadores, monto as Monto, llamadas, ausentismo as Aus, diashabiles as DiasHabiles, fc_meta
        FROM 
          Fechas a 
        LEFT JOIN 
          (SELECT * FROM module_grafVentas WHERE asesor=$asesor) b ON a.Fecha=b.fecha 
        JOIN 
          (
            SELECT meta*100 as fc_meta FROM metas_kpi	WHERE mes=MONTH(CURDATE()) AND anio=YEAR(CURDATE()) AND skill=".$dep." AND tipo='fc'
          ) metafc
        WHERE 
          a.Fecha BETWEEN CAST(CONCAT(YEAR(CURDATE()),'-',MONTH(CURDATE()),'-01') as DATE) AND ADDDATE(CAST(CONCAT(IF(MONTH(CURDATE())=12,YEAR(CURDATE())+1,YEAR(CURDATE())),'-',MONTH(CURDATE())+1,'-01') as DATE),-1)";

//echo $query."<br>";
if($result=$connectdb->query($query)){
	$row_cnt = $result->num_rows;
	$rows=$result->field_count;
	$fields=$result->fetch_fields();
	$x=0;
	while($fila=$result->fetch_row()){
		for($i=0;$i<$rows;$i++){
			$data[$fields[$i]->name][$x]=$fila[$i];
		}
		$x++;
	}
}else{
	echo "Error: ".$connectdb->error;
}

//Calculated Fields
for($i=0;$i<$row_cnt;$i++){
	if(date("I", strtotime($data['Fecha'][$i]))==1){$minus="4";}else{$minus="5";} //Convert Date for HighCharts
	//$datetmp_dt=new DateTime($data['Fecha'][$i]);
	//$datetmp=strftime("%s", strtotime($data['Fecha'][$i].' -'.$minus.' hours'))*1000;
	$datetmp="Date.UTC(".date('Y',strtotime($data['Fecha'][$i])).",".(date('m',strtotime($data['Fecha'][$i]))-1).",".date('d',strtotime($data['Fecha'][$i])).")";
	//echo $data['Fecha'][$i].' -'.$minus.' hours -> '.$datetmp.'<br>';
	$xdata[$i]=$datetmp;
	
	$data['locs'][$i]=intval($data['Localizadores'][$i]);
	@$data['metadiaria'][$i]=intval($mt/intval($data['DiasHabiles'][$i]));
	
	$acumulado['Monto']+=$data['Monto'][$i];
    $acumulado['Localizadores']+=$data['Localizadores'][$i];
    $acumulado['Llamadas']+=$data['llamadas'][$i];
    
    @$data['acumfc'][$i]=$acumulado['Localizadores']/$acumulado['Llamadas']*100;
    @$data['acummonto'][$i]=intval($acumulado['Monto']);
    @$data['arfc'][$i]=floatval($data['Localizadores'][$i]/$data['llamadas'][$i]*100);
    @$data['armonto'][$i]=intval($data['Monto'][$i]);
	$data['metafc'][$i]=intval($data['fc_meta'][$i]);
    
    if($data['Aus'][$i]==1){
        $acumulado['md']+=intval($mt/intval($data['DiasHabiles'][$i]));
    }
    @$data['metadiaria'][$i]=intval($mt/intval($data['DiasHabiles'][$i]));
    @$data['mdtacum'][$i]=intval($acumulado['md']);
}

//print_r($data);

$xData=array();
$datasets=array();
$inf=array();
$datamonto=array();
$datalocs=array();


$xData[]=array("categories"=>$data['Fecha']);

switch($type){
    case "montos":
        $datasets[]=array("name"=>"Meta Diaria","data"=>$data['metadiaria'],"valueDecimals"=>2,"yAxis"=>1,"type"=>"line","color"=>"#606060","marker"=>array("enabled"=>false));
        $datasets[]=array("name"=>"Meta Acumulada por Dia","data"=>$data['mdtacum'],"valueDecimals"=>2,"yAxis"=>0,"type"=>"line","color"=>"#89CC6F","marker"=>array("enabled"=>false));
        $datasets[]=array("name"=>"Por dia","data"=>$data['armonto'],"valueDecimals"=>2,"yAxis"=>1,"type"=>"column","color"=>"#85ACDB","marker"=>array("enabled"=>true));
        $datasets[]=array("name"=>"Acumulado","data"=>$data['acummonto'],"valueDecimals"=>2,"yAxis"=>0,"type"=>"spline","color"=>"#C7000D","marker"=>array("enabled"=>true));
        //$datasets[]=array("name"=>"Por dia","data"=>$monto,"xData"=>$xdata,"valueDecimals"=>2,"yAxis"=>1,"type"=>"column");
        //$datasets[]=array("name"=>"Acumulado","data"=>$acum,"xData"=>$xdata,"valueDecimals"=>2,"yAxis"=>0,"type"=>"spline");
        break;
    case "montot":
        $info=$data['acummonto'];
        echo  json_encode($info, JSON_PRETTY_PRINT);
        exit;
        break;
    case "montod":
        $info=$data['armonto'];
        echo  json_encode($info, JSON_PRETTY_PRINT);
        exit;
        break;
    case "fc":
        $datasets[]=array("name"=>"Meta","data"=>$data['metafc'],"valueDecimals"=>2,"yAxis"=>0);
		$datasets[]=array("name"=>"Por Dia","data"=>$data['arfc'],"valueDecimals"=>2,"yAxis"=>0,"type"=>"column","color"=>"#85ACDB");
        $datasets[]=array("name"=>"Acumulado","data"=>$data['acumfc'],"valueDecimals"=>2,"yAxis"=>0,"type"=>"line","color"=>"##C7000D");
        break;
}
    //$datasets[]=array("name"=>"FC","data"=>$fc,"valueDecimals"=>0,"yAxis"=>1,"type"=>"spline");

$connectdb->close();
    
$info=array("xData"=>$xData,"datasets"=>$datasets);
print json_encode($info,JSON_PRETTY_PRINT);

//print_r($fechas);
?>

