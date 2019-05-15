<?php

include_once('../modules/modules.php');

timeAndRegion::setRegion('Cun');

$connectdb=Connection::mysqliDB('CC');

$l=$_POST['l'];
$h=$_POST['h'];
$fecha=$_POST['fecha'];
$skill=$_POST['skill'];

$data['l']=$l;
$data['h']=$h;
$data['fecha']=$fecha;
$data['skill']=$skill;

$query="SELECT 
            *, NombreAsesor(user,1) as gtr
        FROM
            bitacora_base
        WHERE
            skill = $skill AND Fecha = '$fecha'
                AND level = $h
                AND intervalo = $l";
$data['query']=utf8_encode($query);
if($result=$connectdb->query($query)){
    $data['status']=1;
    while($fila=$result->fetch_assoc()){
        $data['act']=$fila['accion'];
        $data['comments']=$fila['comments'];
        $data['asesor']=$fila['gtr'];
    }
}

echo json_encode($data);

$connectdb->close();