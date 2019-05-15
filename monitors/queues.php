<?php
include_once('../modules/modules.php');

initSettings::start(false);
initSettings::printTitle('Monitor por Queues');

$queues=$_POST['colas'];
if(isset($_GET['colas'])){
  $queues=$_GET['colas'];
}

if(isset($_GET['zoom'])){
  $zoom=$_GET['zoom'];
}else{
  $zoom=.5;
}


if(isset($_GET['colas'])){
  $queues=$_GET['colas'];
}

$qs=explode('|',$queues);

$tbody="<td><select id='colas' name='colas'><option value=''>Selecciona...</option>";
  $query="SELECT a.id, Departamento, b.queue FROM PCRCs a LEFT JOIN Cola_Skill b ON a.id=b.Skill_sec WHERE a.main=1 AND calls=1 ORDER BY Departamento, queue";
  if($result=Queries::query($query)){
    while($fila=$result->fetch_assoc()){
      $skills[$fila['id']]['Cola']=$fila['Departamento'];
      $skills[$fila['id']]['Q'][]=$fila['queue'];
    }
  }

foreach($skills as $dep => $info){
  $valor=implode('|',$info['Q']);

  if($valor==$queues){
    $sel="selected";
  }else{
    $sel="";
  }

  $tbody.="<option value='$valor' $sel>".$info['Cola']."</option>";
  unset($valor);
}

$tbody.="</select></td>";

Filters::showFilter('','POST','show','Mostrar',$tbody);

 ?>
<style>
  .frame{
    width:100%;
    height: 400;
    resize: vertical;
  }


</style>
<?php
if($queues!=""){
  foreach($qs as $index => $cola){
    echo "<iframe scrolling='yes' src='/app/?module=rtmonitor&skill=$cola&tipo=queues&q=1&zoom=".$zoom."' class='frame'></iframe>";
  }
}
 ?>
