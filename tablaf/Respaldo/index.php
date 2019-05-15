<?php

include_once("../modules/modules.php");

initSettings::start(true,'tablas_f');

$from=$_POST['start'];
$to=$_POST['end'];
$tipo=$_POST['tipo'];

$tipos=array('MP'=>'MP',
              'MT'=>'MT',
              'SAC IN' => 'SACIN',
              'Tráfico'=>'Trafico');

$tbody="<td><input type='text' value='$from' id='start' name='start' required><input type='text' value='$to' id='end' name='end' required><input type='hidden' name='consultar'></td>
        <td>Departamento</td><td>
        <select name='tipo' id='tipo' required>
          <option value=''>Selecciona...</option>";
foreach($tipos as $option => $val){
  if($val==$tipo){
    $selected='selected';
  }else{
    $selected='';
  }
  
  $tbody.="<option value='$val' $selected>$option</option>";
}
$tbody.="</select></td>";

Filters::showFilter('','POST','search','Consultar',$tbody);

?>
<script>
$(function(){
  $('#start').periodpicker({
    lang: 'en',
		formatDate: 'YYYY-MM-DD',
		end: '#end'
  });
});
</script>

<?php 

if(!isset($_POST['consultar'])){
  initSettings::printTitle('Tabla F');
  exit;
}else{
  switch($tipo){
    case 'MP':
      include('inbound_mp.php');
      break;
    case 'MT':
      include('inbound_mt.php');
      break;
    case 'Trafico':
      include('tabla_trafico.php');
      break;
    case 'SACIN':
      include('tabla_sac_in.php');
      break;
  }
}
