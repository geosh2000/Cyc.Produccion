<?php
 
include_once("../modules/modules.php");

initSettings::start(false,'afiliados');
initSettings::printTitle('Reportes Afiliados');

if($_SESSION['default']==0){
  $query="SELECT * FROM afiliados_view WHERE users LIKE '%".$_SESSION['id']."%' ORDER BY afiliado";
}else{
  $query="SELECT * FROM afiliados_view ORDER BY afiliado";
}

if(!isset($_GET['search'])){
  $tbody="<form><td><select name='reporte' id='reporte' required>
              <option value=''>Selecciona...</option>";
              if($result=Queries::query($query)){
                while($fila=$result->fetch_assoc()){
                  $tbody.="<option value='".$fila['afiliado']."'>".$fila['afiliado']."</option>";
                }
              }else{
                $tbody.="<option value='error'>Error</option>";
              }
  $tbody.="</select><input type='hidden' name='search' value=1></td>";

  Filters::showFilterNOFORM('search','Consultar',$tbody);
}

if($_SESSION['default']!=0){
  echo "</form>";
  if(isset($_GET['search'])){
    if($_GET['reporte']=='copa'){$include='copa';}else{$include='afiliados_show';}
    include_once($include.".php");
  }
}else{
  if($_GET['reporte']=='copa'){$include='copa';}else{$include='afiliados_show';}
    include_once($include.".php");
}

?>
