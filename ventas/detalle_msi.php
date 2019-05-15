<?php
include_once('../modules/modules.php');

$connectdb=Connection::mysqliDB('CC');

$connectdb->query("SET timezone = '-10:00'");

//Build Info

$from=date('Y-m-d',strtotime($_GET['from']));
$to=date('Y-m-d',strtotime($_GET['to']));

$query="SELECT 
            a.id,
            NOMBREASESOR(a.asesor, 1) AS Asesor,
            Departamento,
            date_created AS Fecha,
            b.text AS Pidio_MSI,
            c.text AS Venta_Concretada,
            d.text AS TC_Otro_Banco,
            monto AS Monto_Reservacion,
            a.Last_Update
        FROM
            ventas_msiBanamex a
                LEFT JOIN
            formulario_ventas_MSIBanamex b ON a.pidio_msi = b.id
                LEFT JOIN
            formulario_ventas_MSIBanamex c ON a.concretada = c.id
                LEFT JOIN
            formulario_ventas_MSIBanamex d ON a.otra_tc = d.id
                LEFT JOIN
            dep_asesores e ON a.asesor = e.asesor
                AND CAST(a.Last_Update AS DATE) = e.Fecha
                LEFT JOIN
            PCRCs f ON e.dep = f.id
        WHERE
                CAST(date_created as DATE) BETWEEN '$from' AND '$to'";

if ($result=$connectdb->query($query)) {
	$info_field=$result->fetch_fields();
   while ($fila = $result->fetch_row()) {
		for($i=0;$i<$result->field_count;$i++){
			switch($info_field[$i]->type){
				case 246:
					$data[$fila[0]][]=number_format($fila[$i],2);
					break;
				default:
					if($info_field[$i]->name=='Fecha_recibido'){
						if($_GET['editable']==1){
							$data[$fila[0]][]=utf8_encode("<input type='text' class='f_recep' value='$fila[$i]' reg='".$fila[0]."'>");
						}else{
							$data[$fila[0]][]=utf8_encode($fila[$i]);
						}
					}else{
						$data[$fila[0]][]=utf8_encode($fila[$i]);
					}
					break;
			}
		}
	}
}else{
	echo $connectdb->error."<br> ON <br>$query<br>";
}

for($i=0;$i<$result->field_count;$i++){
	$dataheaders[]=ucwords(str_replace("_"," ",$info_field[$i]->name));
}

unset($result);

//Create Headers
foreach($dataheaders as $index => $info){
	$headers[]=array("text"=>$info);
}

//Create Rows
foreach($data as $id =>$info){
	$row[]=$info;
}

$connectdb->close();

//Build JSON
$table=array();
$table = array("rows" => $row,"headers"=>array($headers));

//Print JSON
print json_encode($table,JSON_UNESCAPED_UNICODE);
//print json_encode($table,JSON_PRETTY_PRINT);

?>


