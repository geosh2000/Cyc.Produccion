<?php

include_once('../modules/modules.php');

$connectDB=Connection::mysqliDB('CC');

$idSol=$_POST['idSol'];
$f_aplicacion=$_POST['f_aplicacion'];

$query="SELECT * FROM rrhh_solicitudesCambioBaja WHERE id=$idSol";
if($result=$connectDB->query($query)){
    $fields=$result->fetch_fields();
    $fila=$result->fetch_array();
    for($i=0;$i<$result->field_count;$i++){
        $sol[$fields[$i]->name]=$fila[$i];
    }
    
    $query="SELECT 
                *
            FROM
                asesores_movimiento_vacantes a
                    LEFT JOIN
                asesores_plazas b ON a.vacante = b.id
            WHERE
                vacante = ".$sol['vacante']." AND fecha_in IS NULL
                    AND fecha_out <= '$f_aplicacion'
                    AND fin > '$f_aplicacion'";
    if($result=$connectDB->query($query)){
        $fila=$result->fetch_assoc();
        if($result->num_rows>0){
            $td['status']=1;
            $td['avail']=1;
            $td['movimiento']=$fila['id'];
        }else{
            $td['status']=1;
            $td['avail']=0;
        }
    }else{
        $td['status']=0;
        $td['msg']=utf8_encode("ERROR en DB Movimientos -> ".$connectDB->error." ON $query");
    }
    
}else{
    $td['status']=0;
    $td['msg']=utf8_encode("ERROR en DB Solicitud -> ".$connectDB->error." ON $query");
}


echo json_encode($td);



$connectDB->close();