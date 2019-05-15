<?php
header("Access-Control-Allow-Origin: *");


include_once("../../modules/modules.php");

$connectdb=Connection::mysqliDB('CC');

    $term=$_GET['term'];
    $cred['viewAll']=$_GET['viewAll'];
    $cred['puesto']=$_GET['puesto'];
    $cred['udn']=$_GET['udn'];
    $cred['area']=$_GET['area'];
    $cred['dep']=$_GET['dep'];
    $tipo=$_GET['tipo'];



    if($cred['viewAll'] == 1){
        $restrtict = "";
    }else{
        switch($cred['puesto']){
            case 'B2':
                $restrict="AND hc_udn=".$cred['udn'];
                break;
            case 'B3':
                $restrict="AND hc_area=".$cred['area'];
                break;
            default:
                $restrict="AND hc_dep=".$cred['dep'];
                break;
        }
    }

    switch($tipo){
        case "name":
            $where="(a.Nombre LIKE '%$term%'
                    OR a.Usuario LIKE '%$term%')
                    $restrict";
            $type="";
            break;
        case "dep":
            $where="(c.nombre LIKE '%$term%' OR d.nombre LIKE '%$term%' OR b.nombre LIKE '%$term%')";
            $type="";
            $order="DepartamentoOK, ";
            break;
        case "ingreso":
            $order="Ingreso, ";
            $where="(MONTH(Ingreso) = MONTH('20$term-01')
                        AND YEAR(Ingreso) = YEAR('20$term-01'))
                    $restrict";
            $type="";
            break;
        case "egreso":
            $order="Egreso, ";
            $where="(MONTH(Egreso) = MONTH('20$term-01')
                        AND YEAR(Egreso) = YEAR('20$term-01'))
                    $restrict";
            $type="";
            break;
    }

    switch($tipo){
      case 'dep':
        $query="SELECT
                    b.id AS id,
                    d.id AS hc_udn,
                    c.id AS hc_area,
                    b.id AS hc_departamento,
                    CONCAT(d.nombre,
                            ' - ',
                            c.nombre,
                            ' - ',
                            b.nombre) AS Codigo,
                    d.nombre AS UDN_nombre,
                    c.nombre AS Area_nombre,
                    b.nombre AS Departamento_nombre
                FROM
                    hc_codigos_Departamento b
                        LEFT JOIN
                    hc_codigos_Areas c ON b.area = c.id
                        LEFT JOIN
                    hc_codigos_UnidadDeNegocio d ON c.unidadDeNegocio = d.id
                WHERE
                  1 = 1 AND $where
                HAVING Codigo IS NOT NULL $restrict
                ORDER BY Codigo";

                if($result=$connectdb->query($query)){

                  while($fila=$result->fetch_assoc()){
                    $data[]=Array(
                      "id" => utf8_encode($fila['id']),
                      "Codigo" => utf8_encode($fila['Codigo'])
                      );
                  }
                }else{
                  echo $connectdb->error." ON $query<br>";
                }

        break;
      default:
        $query="SELECT
                    a.*,
                    CONCAT(d.nombre,' - ',c.nombre) as DepartamentoOK,
                    IF(Egreso < CURDATE(),
                        'Inactivo',
                        'Activo') AS status,
                    hc_udn, hc_area, hc_dep, Ingreso, Egreso
                FROM
                    Asesores a
                        LEFT JOIN
                    dep_asesores b ON a.id = b.asesor AND b.Fecha=CURDATE()
                        LEFT JOIN
                    hc_codigos_Departamento c ON b.hc_dep=c.id
                        LEFT JOIN
                    hc_codigos_UnidadDeNegocio d ON b.hc_udn=d.id
                WHERE
                    $where
                ORDER BY $order Nombre";
        if($result=$connectdb->query($query)){

          while($fila=$result->fetch_assoc()){
            $data[]=Array(
              "dep" => utf8_encode($fila['DepartamentoOK']),
              "user" => utf8_encode($fila['Usuario']),
              "ncorto" => utf8_encode($fila['N Corto']),
              "name" => utf8_encode($fila['Nombre']),
              "fdep" => utf8_encode("(".$fila['DepartamentoOK'].") ".$fila['Nombre']),
              "fingreso" => utf8_encode("(".$fila['Ingreso'].") ".$fila['Nombre']),
              "fegreso" => utf8_encode("(".$fila['Egreso'].") ".$fila['Nombre']),

              "status" => utf8_encode($fila['status']),
              "ingreso" => utf8_encode($fila['Ingreso']),
              "egreso" => utf8_encode($fila['Egreso']),
              "id" => $fila['id']
              );
          }
        }else{
          echo $connectdb->error." ON $query<br>";
        }
        break;
    }


    if(!isset($data)){
      $data[]=Array(
        "dep" => utf8_encode(""),
        "name" => utf8_encode("Sin resultados..."),
        "status" => utf8_encode(""),
        "id" => ""
        );
    }


    echo json_encode($data);



    $connectdb->close();

 ?>
