<?php
header("Access-Control-Allow-Origin: *");

include_once("../../modules/modules.php");

$connectdb=Connection::mysqliDB('CC');

$query="SELECT
            a.id,
            `N Corto`,
            Nombre,
            d.Esquema,
            `jornada start`,
            `jornada end`,
            `comida start`,
            `comida end`,
            `extra1 start`,
            `extra1 end`,
            `extra2 start`,
            `extra2 end`,
            posicion,
            Ausentismo,
            Vacante,
            c.comida
        FROM
            (SELECT
                *,
                    GETDEPARTAMENTO(id, '2017-07-20') AS Departamento,
                    GETPUESTO(id, '2017-07-20') AS Puesto_OK,
                    GETVACANTEASESOR(id, '2017-07-20') AS Vacante
            FROM
                Asesores
            WHERE
                Egreso > '2017-07-20'
                    AND Ingreso <= '2017-07-20'
            HAVING Departamento IN (35)
                AND Puesto_ok = 1) a
                LEFT JOIN
            (SELECT
                *
            FROM
                `Historial Programacion`
            WHERE
                Fecha = '2017-07-20') b ON a.id = b.asesor
                LEFT JOIN
            (SELECT
                b.Ausentismo, Inicio, Fin, asesor
            FROM
                Ausentismos a
            LEFT JOIN `Tipos Ausentismos` b ON a.tipo_ausentismo = b.id) d ON b.Fecha BETWEEN Inicio AND Fin
                AND b.asesor = d.asesor
                LEFT JOIN
            horarios_position_select c ON a.id = c.asesor
                AND IF(WEEK(Fecha, 1) = 0,
                52,
                WEEK(Fecha, 1)) = c.semana
                AND IF(WEEK(Fecha, 1) = 0,
                YEAR(Fecha) - 1,
                YEAR(Fecha)) = c.year
                LEFT JOIN
            asesores_plazas d ON a.Vacante = d.id
        ORDER BY `jornada start`, Nombre, a.Departamento, d.Esquema";
if($result=$connectdb->query($query)){
    while($fila=$result->fetch_assoc()){
        $data[$fila['id']]['name']=utf8_encode($fila['Nombre']);
        $data[$fila['id']]['id']=utf8_encode($fila['id']);
        $data[$fila['id']]['jornada']=utf8_encode($fila['Esquema']);
        $data[$fila['id']]['comida']=utf8_encode($fila['comida']);
        $data[$fila['id']]['ausentismo']=utf8_encode($fila['Ausentismo']);
    }
}

$connectdb->close();

echo json_encode($data);
