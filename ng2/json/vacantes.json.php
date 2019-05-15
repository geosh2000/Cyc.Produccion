<?php
header("Access-Control-Allow-Origin: *");

include_once("../../modules/modules.php");
include_once("../../common/JWT.php");
include_once("../validateToken.php");

timeAndRegion::setRegion('Cun');


validateTk(function(){
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);

    if($request->future == 'true'){
        $dateView="ADDDATE(CURDATE(),15)";
    }else{
        $dateView="CURDATE()";
    }

    $id=$request->id;
    $hc_area=$request->area;

    if($hc_area != "" && $hc_area!=7){
        $restrict= "AND c.id = $hc_area";
    }else{
        $restrict= "";
    }

    $connectdb=Connection::mysqliDB('CC');

    $query="SELECT
                a.id,
                libre,
                f.id as id_PuestoCode,
                f.Unidad_de_Negocio,
                f.Area,
                f.Departamento,
                f.Puesto,
                f.Codigo,
                b.Departamento AS Departamento_Alias,
                c.Puesto AS puesto_Alias,
                d.Ciudad AS ciudad,
                e.PDV AS oficina,
                a.inicio,
                a.fin,
                a.comentarios,
                NOMBREASESOR(GETVACANTE(a.id, $dateView), 2) AS Asesor_Actual,
                if( NOMBREASESOR(GETVACANTE(a.id, $dateView), 2) IS NULL, if( a.Activo=0 AND a.Status!=0, '2Inactivas', '0Vacantes') ,'1Cubiertas') as
                type,
                GETVACANTE(a.id, $dateView) as asesorID,
                a.Status,
                a.Activo,
                a.esquema,
                a.departamento AS dep_id,
                a.puesto AS puesto_id,
                a.oficina AS oficina_id,
                a.ciudad AS ciudad_id,
                NOMBREASESOR(approbed_by, 1) AS Aprobada_por,
                date_approbed AS Fecha_Aprobacion,
                date_deactivated AS Fecha_Desactivacion,
                NOMBREASESOR(deactivated_by,1) AS Desactivada_por,
                deactivation_comments AS Comentarios_desactivacion
            FROM
                asesores_plazas a
                    LEFT JOIN
                PCRCs b ON a.departamento = b.id
                    LEFT JOIN
                PCRCs_puestos c ON a.puesto = c.id
                    LEFT JOIN
                cat_zones d ON a.ciudad = d.id
                    LEFT JOIN
                PDVs e ON a.oficina = e.id
                    LEFT JOIN
                (SELECT
                        a.id,
                        d.nombre as Unidad_de_Negocio,
                        c.nombre as Area,
                        b.nombre as Departamento,
                        a.nombre as Puesto,
                        CONCAT(d.clave,'-',c.clave,'-',b.clave,'-',a.clave) as Codigo
                    FROM
                        hc_codigos_Puesto a
                            LEFT JOIN
                        hc_codigos_Departamento b ON a.departamento = b.id
                            LEFT JOIN
                        hc_codigos_Areas c ON b.area = c.id
                            LEFT JOIN
                        hc_codigos_UnidadDeNegocio d ON c.unidadDeNegocio = d.id
                    WHERE
                        1=1
                        $restrict
                        ) f ON hc_puesto=f.id
                            LEFT JOIN
                (SELECT
                    vacante, MAX(fecha_out) AS libre
                FROM
                    asesores_movimiento_vacantes
                GROUP BY vacante) g ON a.id = g.vacante
            HAVING f.Unidad_de_Negocio IS NOT NULL
            ORDER BY Unidad_de_Negocio, Area, Departamento, Puesto, type, f.id, d.Ciudad, e.PDV, Asesor_Actual, a.id";
    if($result=$connectdb->query($query)){
        $fields=$result->fetch_fields();
        $columns=$result->field_count;
        $x=0;
        while($fila=$result->fetch_array(MYSQLI_BOTH)){
            for($i=0;$i<$columns;$i++){
                if($fila['Status']==1 AND $fila['Activo']==1){
                    if($fila['Asesor_Actual']==NULL){
                        $data['CodigoPuesto']['Partes'][$fila['Unidad_de_Negocio']]['Partes'][$fila['Area']]['Partes'][$fila['Departamento']]['Partes'][$fila['Puesto']]['Partes'][utf8_encode($fila['puesto_Alias'])]['Vacantes'][$x][$fields[$i]->name]=utf8_encode($fila[$i]);
                    }else{
                        $data['CodigoPuesto']['Partes'][$fila['Unidad_de_Negocio']]['Partes'][$fila['Area']]['Partes'][$fila['Departamento']]['Partes'][$fila['Puesto']]['Partes'][utf8_encode($fila['puesto_Alias'])]['Cubiertas'][$x][$fields[$i]->name]=utf8_encode($fila[$i]);
                    }
                }

                if($fila['Status']==0){
                    //Pendientes
                    $data['CodigoPuesto']['Partes'][$fila['Unidad_de_Negocio']]['Partes'][$fila['Area']]['Partes'][$fila['Departamento']]['Partes'][$fila['Puesto']]['Partes'][utf8_encode($fila['puesto_Alias'])]['Pendientes'][$fila['id']][$fields[$i]->name]=utf8_encode($fila[$i]);
                }

                if($fila['Activo']==0 && $fila['Status']!=0){
                    $data['CodigoPuesto']['Partes'][$fila['Unidad_de_Negocio']]['Partes'][$fila['Area']]['Partes'][$fila['Departamento']]['Partes'][$fila['Puesto']]['Partes'][utf8_encode($fila['puesto_Alias'])]['Inactivas'][$fila['id']][$fields[$i]->name]=utf8_encode($fila[$i]);
                }

                $data['oldDep'][$fila['Departamento_Alias']]['detalle'][$fila['id']][$fields[$i]->name]=utf8_encode($fila[$i]);
            }

            if($fila['Status']==1 AND $fila['Activo']==1){
                //HC
                @$data['CodigoPuesto']['Totales'][$fila['Unidad_de_Negocio']]['HC']++;
                @$data['CodigoPuesto']['Partes'][$fila['Unidad_de_Negocio']]['Totales'][$fila['Area']]['HC']++;
                @$data['CodigoPuesto']['Partes'][$fila['Unidad_de_Negocio']]['Partes'][$fila['Area']]['Totales'][$fila['Departamento']]['HC']++;
                @$data['CodigoPuesto']['Partes'][$fila['Unidad_de_Negocio']]['Partes'][$fila['Area']]['Partes'][$fila['Departamento']]['Totales'][$fila['Puesto']]['HC']++;
                @$data['CodigoPuesto']['Partes'][$fila['Unidad_de_Negocio']]['Partes'][$fila['Area']]['Partes'][$fila['Departamento']]['Partes'][$fila['Puesto']]['Totales'][utf8_encode($fila['puesto_Alias'])]['HC']++;


                //Vacantes
                if($fila['Asesor_Actual']==NULL){
                    @$data['CodigoPuesto']['Totales'][$fila['Unidad_de_Negocio']]['Vacantes']++;
                    @$data['CodigoPuesto']['Partes'][$fila['Unidad_de_Negocio']]['Totales'][$fila['Area']]['Vacantes']++;
                    @$data['CodigoPuesto']['Partes'][$fila['Unidad_de_Negocio']]['Partes'][$fila['Area']]['Totales'][$fila['Departamento']]['Vacantes']++;
                    @$data['CodigoPuesto']['Partes'][$fila['Unidad_de_Negocio']]['Partes'][$fila['Area']]['Partes'][$fila['Departamento']]['Totales'][$fila['Puesto']]['Vacantes']++;
                    @$data['CodigoPuesto']['Partes'][$fila['Unidad_de_Negocio']]['Partes'][$fila['Area']]['Partes'][$fila['Departamento']]['Partes'][$fila['Puesto']]['Totales'][utf8_encode($fila['puesto_Alias'])]['Vacantes']++;
                }
            }

            if($fila['Status']==0){
                //Pendientes
                @$data['CodigoPuesto']['Totales'][$fila['Unidad_de_Negocio']]['Pendientes']++;
                @$data['CodigoPuesto']['Partes'][$fila['Unidad_de_Negocio']]['Totales'][$fila['Area']]['Pendientes']++;
                @$data['CodigoPuesto']['Partes'][$fila['Unidad_de_Negocio']]['Partes'][$fila['Area']]['Totales'][$fila['Departamento']]['Pendientes']++;
                @$data['CodigoPuesto']['Partes'][$fila['Unidad_de_Negocio']]['Partes'][$fila['Area']]['Partes'][$fila['Departamento']]['Totales'][$fila['Puesto']]['Pendientes']++;
                @$data['CodigoPuesto']['Partes'][$fila['Unidad_de_Negocio']]['Partes'][$fila['Area']]['Partes'][$fila['Departamento']]['Partes'][$fila['Puesto']]['Totales'][utf8_encode($fila['puesto_Alias'])]['Pendientes']++;

             }

            if($fila['Activo']==0 && $fila['Status']!=0){
                //Inactivas
                @$data['CodigoPuesto']['Totales'][$fila['Unidad_de_Negocio']]['Inactivas']++;
                @$data['CodigoPuesto']['Partes'][$fila['Unidad_de_Negocio']]['Totales'][$fila['Area']]['Inactivas']++;
                @$data['CodigoPuesto']['Partes'][$fila['Unidad_de_Negocio']]['Partes'][$fila['Area']]['Totales'][$fila['Departamento']]['Inactivas']++;
                @$data['CodigoPuesto']['Partes'][$fila['Unidad_de_Negocio']]['Partes'][$fila['Area']]['Partes'][$fila['Departamento']]['Totales'][$fila['Puesto']]['Inactivas']++;
                @$data['CodigoPuesto']['Partes'][$fila['Unidad_de_Negocio']]['Partes'][$fila['Area']]['Partes'][$fila['Departamento']]['Partes'][$fila['Puesto']]['Totales'][utf8_encode($fila['puesto_Alias'])]['Inactivas']++;
            }
           $x++;
        }
    }else{
        echo "ERROR! -> ".$connectdb->error." ON $query <br>";
    }


    $connectdb->close();

    echo json_encode($data);

},$tkFlag);







 ?>
