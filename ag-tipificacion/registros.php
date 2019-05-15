<?php

include_once("../modules/modules.php");

initSettings::start(false);
timeAndRegion::setRegion('Cun');

$connectdb=Connection::mysqliDB('CC');

$asesor=$_SESSION['asesor_id'];
$area=$_GET['area'];

$query="SELECT a.id, date_created, fecha_recepcion, b.status, em, localizador FROM ag_bo_tipificacion a LEFT JOIN ag_bo_status b ON a.status=b.id WHERE CAST(date_created as DATE)=CURDATE() AND asesor=$asesor AND a.area=$area ORDER BY a.id DESC";
if($result=$connectdb->query($query)){
	while($fila=$result->fetch_assoc()){
		$info[$fila['id']]['creado']=$fila['date_created'];
		$info[$fila['id']]['recibido']=$fila['fecha_recepcion'];
		$info[$fila['id']]['status']=$fila['status'];
		$info[$fila['id']]['caso']=$fila['em'];
		$info[$fila['id']]['localizador']=$fila['localizador'];
	}
}

$query="SELECT SUM(TIME_TO_SEC(Duracion))/60/60 as Total FROM Sesiones WHERE Fecha=CURDATE() AND asesor=$asesor";
if($result=$connectdb->query($query)){
	while($fila=$result->fetch_assoc()){
		$sesion=$fila['Total'];
	}
}

$query="SELECT COUNT(*) as casos FROM ag_bo_tipificacion a WHERE asesor=$asesor AND status!=8 AND CAST(a.date_created as DATE)=CURDATE()";
if($result=$connectdb->query($query)){
	while($fila=$result->fetch_assoc()){
		$casos=$fila['casos'];
	}
}

@$eficiencia=$casos/$sesion;

?>
<div id="sidebar">
   <table width='100%' class='t2'>
        <tr class='title' colspan=100>
            <th>Registros Exitosos (Ef. <?php echo number_format($eficiencia,2); ?> casos x hora)</th>
        </tr>
   </table>
   <table width='100%' id='hor-minimalist-a' class='tablesorter' >
        <thead>
        <tr class='title'>
            <th >Creado</th>
            <th>Recibido</th>
            <th >Status</th>
            <th>EM</th>
            <th >Caso</th>
        </tr>
        </thead>
        <tbody>

        <?php
            unset($key,$iden);
            foreach($info as $key => $iden){
                echo "<tr>\n";
                echo "<td >".$iden['creado']."</td>\n";
                echo "<td >".$iden['recibido']."</td>\n";
                echo "<td >".$iden['status']."</td>\n";
                echo "<td >".$iden['localizador']."</td>\n";
				echo "<td >".$iden['caso']."</td>\n";
                echo "</tr>\n";
            }

        ?>
        </tbody>
   </table>
 </div>
<?php $connectdb->close(); ?>
