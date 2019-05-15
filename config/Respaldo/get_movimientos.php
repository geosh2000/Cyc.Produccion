<?php
include("../modules/modules.php");

session_start();

$tipo=$_POST['tipo'];
$fecha=$_POST['fecha'];
$dep=$_POST['dep'];
$vacante=$_POST['vacante'];
$asesor=$_POST['asesor'];

switch($tipo){
  case 'disponibles':
    if($dep==""){
      $dep_optional="";
    }else{
      $dep_optional="AND b.departamento=".$dep;
    }
    $query="SELECT
            	a.id as id_movimiento, vacante as id_vacante, c.Departamento, d.Puesto, CONCAT(f.Estado,' / ', e.Ciudad) as ubicacion, PDV as Oficina, esquema, IF(CAST(date_approbed as DATE) > fecha_out,CAST(date_approbed as DATE),fecha_out) as fecha_liberacion_plaza, nombreAsesor(h.id,2) as asesor_baja, moper, a.comentarios, nombreAsesor(userupdate,1) as asesor_ultima_actualizacion, a.last_update as ultima_actualizacion, b.Status as Status
            FROM
            		asesores_movimiento_vacantes a
            	LEFT JOIN
            		asesores_plazas b ON a.vacante=b.id
            	LEFT JOIN
            		PCRCs c ON b.departamento=c.id
            	LEFT JOIN
            		PCRCs_puestos d ON b.puesto=d.id
            	LEFT JOIN
            		db_municipios e ON b.ciudad=e.id
            	LEFT JOIN
            		db_estados f ON e.estado=f.id
              LEFT JOIN
                PDVs g ON b.oficina=g.id
              LEFT JOIN
               	(SELECT LastV, id FROM (SELECT *, getLastVacante(id,0) as LastV FROM Asesores WHERE Egreso<=CURDATE() HAVING LastV IS NOT NULL ORDER BY Egreso DESC) a GROUP BY LastV) h ON vacante=LastV
            WHERE b.Activo=1 AND a.fecha_out<=CURDATE() AND asesor_in IS NULL $dep_optional
            ORDER BY fecha_out";
    break;
  case 'vacante':
    $query="SELECT
            	a.id as id_movimiento, vacante as id_vacante, c.Departamento, d.Puesto, CONCAT(f.Estado,' / ', e.Ciudad) as ubicacion, esquema, IF(CAST(date_approbed as DATE) > fecha_out,CAST(date_approbed as DATE),fecha_out) as fecha_liberacion_plaza, nombreAsesor(asesor_out,2) as asesor_baja, fecha_in as fecha_cubre_plaza, nombreAsesor(asesor_in,2) as asesor_alta, moper, a.comentarios, nombreAsesor(userupdate,1) as asesor_ultima_actualizacion, a.last_update as ultima_actualizacion
            FROM
            		asesores_movimiento_vacantes a
            	LEFT JOIN
            		asesores_plazas b ON a.vacante=b.id
            	LEFT JOIN
            		PCRCs c ON b.departamento=c.id
            	LEFT JOIN
            		PCRCs_puestos d ON b.puesto=d.id
            	LEFT JOIN
            		db_municipios e ON b.ciudad=e.id
            	LEFT JOIN
            		db_estados f ON e.estado=f.id
            WHERE vacante=$vacante ORDER BY fecha_out";
    break;
    case 'departamento':
      $query="SELECT
              	a.id as id_movimiento, vacante as id_vacante, c.Departamento, d.Puesto, CONCAT(f.Estado,' / ', e.Ciudad) as ubicacion, esquema, fecha_out as fecha_liberacion_plaza, nombreAsesor(asesor_out,2) as asesor_baja, fecha_in as fecha_cubre_plaza, nombreAsesor(asesor_in,2) as asesor_alta, moper, a.comentarios, nombreAsesor(userupdate,1) as asesor_ultima_actualizacion, a.last_update as ultima_actualizacion
              FROM
              		asesores_movimiento_vacantes a
              	LEFT JOIN
              		asesores_plazas b ON a.vacante=b.id
              	LEFT JOIN
              		PCRCs c ON b.departamento=c.id
              	LEFT JOIN
              		PCRCs_puestos d ON b.puesto=d.id
              	LEFT JOIN
              		db_municipios e ON b.ciudad=e.id
              	LEFT JOIN
              		db_estados f ON e.estado=f.id
              WHERE b.departamento=$dep ORDER BY fecha_out";
    break;
    case 'asesor':
      $query="SELECT
              	a.id as id_movimiento, vacante as id_vacante, c.Departamento, d.Puesto, CONCAT(f.Estado,' / ', e.Ciudad) as ubicacion, esquema, fecha_out as fecha_liberacion_plaza, nombreAsesor(asesor_out,2) as asesor_baja, fecha_in as fecha_cubre_plaza, nombreAsesor(asesor_in,2) as asesor_alta, moper, a.comentarios, nombreAsesor(userupdate,1) as asesor_ultima_actualizacion, a.last_update as ultima_actualizacion
              FROM
              		asesores_movimiento_vacantes a
              	LEFT JOIN
              		asesores_plazas b ON a.vacante=b.id
              	LEFT JOIN
              		PCRCs c ON b.departamento=c.id
              	LEFT JOIN
              		PCRCs_puestos d ON b.puesto=d.id
              	LEFT JOIN
              		db_municipios e ON b.ciudad=e.id
              	LEFT JOIN
              		db_estados f ON e.estado=f.id
              WHERE asesor_in=$asesor OR asesor_out=$asesor ORDER BY fecha_in";
    break;
}

if ($result=Queries::query($query)) {
	$info_field=$result->fetch_fields();
   while ($fila = $result->fetch_row()) {
		for($i=0;$i<$result->field_count;$i++){
      switch($info_field[$i]->name){
        case 'moper':
        case 'comentarios':
          $class= 'textedit';
          $title="";
          break;
        case 'fecha_liberacion_plaza':
          $class= 'dateedit';
          $title="";
          break;
        case 'id_vacante':
          $class='searchvac';
          $title="Click para abrir historial de vacante";
          break;
        default:
          $class= 'all';
          $title="";
          break;
      }

      $button="<button class='button ";

      if($info_field[$i]->name=='Status'){
        switch($fila[$i]){
          case 0:
            $button.="button_orange_w'>Pendiente</button>";
            break;
          case 1:
            $button.="button_green_w'>Aprobada</button>";
            break;
          case 2:
            $button.="button_red_w'>Desactivada</button>";
            break;
        }
        $data[$fila[0]][]=array("html"=> utf8_encode($button), "class"=>$class, "title" => $title, "row" => $fila[0], "col" => $info_field[$i]->orgname);
      }else{
        $data[$fila[0]][]=array("text"=> utf8_encode($fila[$i]), "class"=>$class, "title" => $title, "row" => $fila[0], "col" => $info_field[$i]->orgname);
      }

		}
	}
}else{
  $table['error']="ERROR!";
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
  $row[]=array("cells" => $info);
}


//Build JSON
$table=array();
$table = array("rows" => $row,"headers"=>array($headers));

//Print JSON
print json_encode($table,JSON_UNESCAPED_UNICODE);
//print json_encode($table,JSON_PRETTY_PRINT);

?>
