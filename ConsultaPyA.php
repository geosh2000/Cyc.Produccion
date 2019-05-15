<?php
$month=$_GET['month'];

if ($month==NULL){
$m="";
}else{

$m="AND `Historial PyA`.`mes` = ".$month." ";

}

include("connectDB.php");

$sql = "SELECT `PyA Monitor`.`id`,`PyA Monitor`.`N Corto`,`Historial PyA`.`mes`,`Historial PyA`.`dia`,`Historial PyA`.`rcode`,`Historial PyA`.`tiempo`\n"
    . "FROM `PyA Monitor` LEFT JOIN `Historial PyA` ON `PyA Monitor`.`id` = `Historial PyA`.`idnumber`\n"
    . "WHERE (`Historial PyA`.`rcode` Is Not NULL  ".$m.") LIMIT 0, 100 ";

$result=mysql_query($sql);

$num=mysql_numrows($result);

mysql_close();

$i=0;
while ($i<$num){
$id[$i]=mysql_result($result,$i,"id");
$ncorto[$i]=mysql_result($result,$i,"N Corto");
$mes[$i]=mysql_result($result,$i,"mes");
$dia[$i]=mysql_result($result,$i,"dia");
$rcode[$i]=mysql_result($result,$i,"rcode");
$tiempo[$i]=mysql_result($result,$i,"tiempo");
$i++;
}



?>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1.1", {packages:["table"]});
      google.setOnLoadCallback(drawTable);

      function drawTable() {
        var data = new google.visualization.DataTable();
        data.addColumn('number', 'ID');
        data.addColumn('string', 'Asesor');
        data.addColumn('number', 'Mes');
        data.addColumn('number', 'Dia');
        data.addColumn('number', 'Codigo de Retardo');
        data.addColumn('number', 'Tiempo');
        data.addRows([
        <?php
        $i2=0;
	while ($i2<$num){
		echo "[".$id[$i2].",'".$ncorto[$i2]."',".$mes[$i2].",".$dia[$i2].",".$rcode[$i2].",".$tiempo[$i2]."],";
	$i2++;
	}
	?>
          
        ]);

        var table = new google.visualization.Table(document.getElementById('table_div'));

        table.draw(data, {showRowNumber: true});
      }
    </script>
    <script>
    function updateMonth(str){
    if (str!==0){
    window.location.href= "http://wfm.pricetravel.com/ConsultaPyA.php?month="+str;
    }
    }
    </script>
Ver mes: <form><select name="SelectMonth" onchange="updateMonth(this.value)">
  <option value=0>Seleccionar...</option>
  <option value=1>Enero</option>
  <option value=2>Febrero</option>
  <option value=3>Marzo</option>
  <option value=4>Abril</option>
  <option value=5>Mayo</option>
  <option value=6>Junio</option>
  <option value=7>Julio</option>
  <option value=8>Agosto</option>
  <option value=9>Septiembre</option>
  <option value=10>Octubre</option>
  <option value=11>Noviembre</option>
  <option value=12>Diciembre</option>
  <option value="">Todos</option>
</select></form>
<div id="table_div" ></div>