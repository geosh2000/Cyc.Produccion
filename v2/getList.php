<?php
include("../modules/modules.php");

session_start();

$cat=utf8_decode($_POST['cat']);
$prov=utf8_decode($_POST['prov']);

if($prov!=""){
  $prov="proveedor LIKE '%$prov%' ";
}else{
  $prov="1=1 ";
}

if($cat!=""){
  $cat="categoria LIKE '%$cat%' ";
}else{
  $cat="1=1 ";
}

$query="SELECT
            *
          FROM
        pantallas_display
          WHERE
        $cat AND $prov";


if ($result=Queries::query($query)) {
	$info_field=$result->fetch_fields();
   while ($fila = $result->fetch_row()) {
		for($i=0;$i<$result->field_count;$i++){
      switch($info_field[$i]->name){
        case 'activo':
        case 'Ventas1':
        case 'Ventas2':
        case 'Sac1':
        case 'Sac2':
        case 'Upsell':
        case 'Backoffice':
        case 'Tmt':
        case 'Tmp':
        case 'Agencias':
          if($fila[$i]==1){
            $check="checked";
          }else{
            $check="";
          }
          $data[$fila[0]][]=array("html"=> utf8_encode("<input type='checkbox' class='chk' col='".$info_field[$i]->name."' row='".$fila[0]."' $check>"), "class"=>'active');
          break;
        case 'inicio':
        case 'fin':
          $data[$fila[0]][]=array("html"=> utf8_encode("<p col='".$info_field[$i]->name."' row='".$fila[0]."'>".$fila[$i]."</p>"), "class" => 'datechange');
          break;
        case 'categoria':
        case 'proveedor':
        case 'descripcion':
          $data[$fila[0]][]=array("html"=> utf8_encode("<p col='".$info_field[$i]->name."' row='".$fila[0]."'>".$fila[$i]."</p>"), "class" => 'txtchange');
          break;
        default:
          $data[$fila[0]][]=array("text"=> utf8_encode($fila[$i]), "class" => 'all');
          break;
      }
		}
    $data[$fila[0]][]=array("html"=> utf8_encode("<button class='button button_red_w delete' col='".$info_field[$i]->name."' row='".$fila[0]."'>Borrar</button>"));
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

//Add delete header
$headers[]=array("text"=>"Borrar");

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
