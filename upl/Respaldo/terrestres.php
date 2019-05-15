<?php ?>
<head>
   
</head>
<?php
include("../connectDB.php");
header("Content-Type: tesxt/html;charset=utf-8");

$i=0;
while($i<=100){
	$x=1;
	while($x<500){
	    switch($x){
	        case 1:
                $campo="localizador";
                break;
            case 2:
                $campo="item";
                break;
            case 3:
                $campo="fechatx";
                break;
            case 4:
                $campo="fechasrv";
                break;
            case 5:
                $campo="venta";
                break;
            case 6:
                $campo="pax";
                break;
            case 7:
                $campo="noches";
                break;
            case 8:
                $campo="destino";
                break;
            case 9:
                $campo="nombre";
                break;
            case 10:
                $campo="proveedor";
                break;
	    }
        if(!isset($_POST['a'.$i.'b'.$x])){$x=1000;}else{
            if($_POST['a'.$i.'b'.$x]==""){
                $data[$i][$campo]="NULL";
            }else{
                $data[$i][$campo]="'".utf8_decode($_POST['a'.$i.'b'.$x])."'";
            }
        }
	$x++;
	}
$i++;
}

$tipo=$_POST['tipo'];


foreach($data as $key => $info){
    $query="INSERT INTO t_terrestres
                (localizador,item,fecha_transaccion,fecha_servicio,venta,servicio,pax,noches,destino,nombre_servicio,proveedor)
            VALUES
                ($info[localizador],$info[item],$info[fechatx],$info[fechasrv],$info[venta],$tipo,$info[pax],$info[noches],$info[destino],$info[nombre],$info[proveedor])";
    mysql_query($query);
    if(mysql_error()){
        echo "$info[nombre]<br>$key: Error<br><br>";
    }else{
        echo "$info[nombre]<br>$key: OK<br><br>";
    }
}









?>