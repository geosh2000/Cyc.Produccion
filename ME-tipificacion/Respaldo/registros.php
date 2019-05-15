<?php
header('Content-Type: text/html; charset=utf-8');

session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}

date_default_timezone_set('America/Bogota');

include("../connectDB.php");
include("../common/scripts.php");
$asesor=$_SESSION['asesor_id'];

$titles['asesor']='Hora';

$query="SELECT nivel, titulo FROM ME_opts GROUP BY nivel";
if($result=$connectdb->query($query)){
	while($fila=$result->fetch_array(MYSQL_BOTH)){
		$titles[$fila['nivel']]=$fila['titulo'];
	}
}else{
	echo "Error al obtener info: ".$connectdb->error."<br>";
}


$query="SELECT a.id, CAST(date_created as TIME) as Hora, 
			IF(b.opcion IS NULL, level1, b.opcion) as level1,  
			IF(c.opcion IS NULL, level2, c.opcion) as level2,  
			IF(d.opcion IS NULL, level3, d.opcion) as level3, 
			IF(e.opcion IS NULL, level4, e.opcion) as level4, 
			IF(f.opcion IS NULL, level5, f.opcion) as level5, 
			IF(g.opcion IS NULL, level6, g.opcion) as level6  FROM ME_tipificacion a 
		LEFT JOIN ME_opts b ON a.level1=b.id
		LEFT JOIN ME_opts c ON a.level2=c.id
		LEFT JOIN ME_opts d ON a.level3=d.id
		LEFT JOIN ME_opts e ON a.level4=e.id
		LEFT JOIN ME_opts f ON a.level5=f.id
		LEFT JOIN ME_opts g ON a.level6=g.id
		WHERE
			CAST(date_created as DATE)=CURDATE() AND
			asesor=$asesor ORDER BY date_created DESC";
if($result=$connectdb->query($query)){
	$fields=$result->fetch_fields();
	while($fila=$result->fetch_array(MYSQL_BOTH)){
		for($i=1;$i<$result->field_count;$i++){
			$data[$fila['id']][$fields[$i]->name]=utf8_encode($fila[$i]);
		}
	}
}else{
	echo "Error al obtener info: ".$connectdb->error."<br>";
}

?>
<link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.4/jquery.datetimepicker.css"/>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.4/build/jquery.datetimepicker.full.min.js'></script>
<script>

$(function(){
	
	

});

</script>

<style>
</style>
<?php


?>
<div id="sidebar">
   <table width='100%' class='t2'>
        <tr class='title' colspan=100>
            <th>Registros Exitosos</th>
        </tr>
   </table>
   <table width='100%' id='hor-minimalist-a' class='tablesorter' >
        <thead>
        <tr class='title'>
        	<?php
        	foreach($titles as $level => $info){
        		echo "<th>$info</th>";
        	}
        	?>
        </tr>
        </thead>
        <tbody>

        <?php
            foreach($data as $id => $info){
            	echo "<tr>";
				foreach($info as $level => $info2){
					echo "<td>$info2</td>";
				}
				echo "</tr>\n\t";
            }

        ?>
        </tbody>
   </table>
 </div>
<div id='login'></div>
<div id='error'></div>
