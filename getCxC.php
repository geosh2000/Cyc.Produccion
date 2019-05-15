<?php
include("../modules/modules.php");

timeAndRegion::setRegion('Cun');

session_start();

$inicio=date('Y-m-d',strtotime($_POST['inicio']));
$fin=date('Y-m-d',strtotime($_POST['fin']));

if($_POST['asesor']==''){
	$searchAsesor='';
}else{
	$searchAsesor=" AND asesor='".$_POST['asesor']."' ";
}

$query="SELECT a.id, NombreAsesor(asesor,2) as Nombre, Departamento, fecha_cxc, monto, localizador, fecha_aplicacion, firmado, comments, date_created, last_update, NombreAsesor(created_by,1) as created_by, NombreAsesor(updated_by,1) as updated_by FROM asesores_cxc a LEFT JOIN PCRCs b ON getDepartamento(asesor,fecha_cxc)=b.id WHERE fecha_cxc BETWEEN '$inicio' AND '$fin' $searchAsesor ORDER BY fecha_cxc";
if ($result=Queries::query($query)) {
	$info_field=$result->fetch_fields();
   while ($fila = $result->fetch_row()) {
		for($i=0;$i<$result->field_count;$i++){
      switch($info_field[$i]->name){
        case 'firmado':
          if($fila[$i]==1){
            $check='checked';
            $title="Si";
          }else{
            $check='';
            $title="No";
          }
            $data[$fila[0]][]=utf8_encode("$title<br><input type='checkbox' class='docSigned' id='".$fila[0]."' $check>");
          break;
        case 'updated_by':
            $data[$fila[0]][]=utf8_encode("<p id='updatedId_".$fila[0]."'>$fila[$i]</p>");
            break;
        case 'last_update':
            $data[$fila[0]][]=utf8_encode("<p id='lupdatedId_".$fila[0]."'>$fila[$i]</p>");
            break;
        case 'comments':
            $data[$fila[0]][]=utf8_encode("<textarea rows='4' cols='15' class='commentEdit' cxcid='".$fila[0]."' id='commentId_".$fila[0]."'>$fila[$i]</textarea>");
            break;
				case 'fecha_aplicacion':
            $data[$fila[0]][]=utf8_encode("<div cxcid='".$fila[0]."' class='d_ap_sel' style='width: 80px; height: 60px;'>".$fila[$i]."</div>");
            break;
				case 'monto':
            $data[$fila[0]][]=utf8_encode("$".number_format($fila[$i],2));
            break;
        default:
		       $data[$fila[0]][]=utf8_encode($fila[$i]);
           break;
      }
		}
    if($_SESSION['cxc_registro']==1){
      $data[$fila[0]][]=utf8_encode("<button class='button button_red_w deletePuesto' cxcid='".$fila[0]."'>Delete</button>");
    }
	}
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

//Add Edit Button
if($_SESSION['cxc_registro']==1){$headers[]=array("text"=>"Eliminar");}

//Build JSON
$table=array();
$table = array("rows" => $row,"headers"=>array($headers));

//Print JSON
print json_encode($table,JSON_UNESCAPED_UNICODE);
//print json_encode($table,JSON_PRETTY_PRINT);

?>
