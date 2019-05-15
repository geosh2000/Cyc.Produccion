<?php
header('Content-Type: text/html; charset=utf-8');

session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="asesor_formulario_mt";

date_default_timezone_set('America/Bogota');

include("../connectDB.php");
include("../common/scripts.php");
$asesor=$_SESSION['asesor_id'];
$area=$_GET['area'];


switch($_GET['area']){
	case 'llamadas':
		$query="SELECT a.id, date_created, b.canal, c.motivo, d.tipo, localizador FROM trfMP_tipificacion a LEFT JOIN trfMP_canales b ON a.canal=b.id "
				."LEFT JOIN trfMP_motivos_llamadas c ON a.motivo=c.id LEFT JOIN trfMP_tipo_reserva d ON a.tipo_reserva=d.id "
				." WHERE CAST(date_created as DATE)=CURDATE() AND asesor=$asesor ORDER BY a.id DESC";
		if($result=$connectdb->query($query)){
			while($fila=$result->fetch_assoc()){
				$info[$fila['id']]['Creado']=$fila['date_created'];
				$info[$fila['id']]['Canal']=$fila['canal'];
				$info[$fila['id']]['Motivo']=$fila['motivo'];
				$info[$fila['id']]['localizador']=$fila['localizador'];
			}
		}else{
			echo "ERROR: ".$connectdb->error." ON <br>$query<br>";
		}
		break;
	case 'funciones':
		$query="SELECT 
			a.id, date_created, Nombre, b.actividad, 
				c1.opcion as opt_1, c2.opcion as opt_2,
				c3.opcion as opt_3, c4.opcion as opt_4,
				c5.opcion as opt_5, c6.opcion as opt_6,
				c7.opcion as opt_7, c8.opcion as opt_8,
				c9.opcion as opt_9,
				item,	pnrs_en_cola, codigo_aerolinea,
				a.em, a.loc, a.pnr
		FROM 
				trfMP_funciones a 
			LEFT JOIN 
				trfMP_actividad b ON a.actividad=b.id
			LEFT JOIN
				trfMP_opts c1 ON a.level1=c1.id
			LEFT JOIN
				trfMP_opts c2 ON a.level2=c2.id
			LEFT JOIN
				trfMP_opts c3 ON a.level3=c3.id
			LEFT JOIN
				trfMP_opts c4 ON a.level4=c4.id
			LEFT JOIN
				trfMP_opts c5 ON a.level5=c5.id
			LEFT JOIN
				trfMP_opts c6 ON a.level6=c6.id
			LEFT JOIN
				trfMP_opts c7 ON a.level7=c7.id
			LEFT JOIN
				trfMP_opts c8 ON a.level8=c8.id
			LEFT JOIN
				trfMP_opts c9 ON a.level9=c9.id
			LEFT JOIN
				Asesores d ON a.asesor=d.id
		WHERE
			CAST(a.date_created as DATE)=CURDATE() AND
			asesor=$asesor ORDER BY a.id DESC
";
		if($result=$connectdb->query($query)){
			while($fila=$result->fetch_assoc()){
				$info[$fila['id']]['Creado']=$fila['date_created'];
				$info[$fila['id']]['actividad']=$fila['actividad'];
				$info[$fila['id']]['em']=$fila['em'];
				$info[$fila['id']]['loc']=$fila['loc'];
				$info[$fila['id']]['pnr']=$fila['pnr'];
				$info[$fila['id']]['aerolinea']=$fila['codigo_aerolinea'];
				$info[$fila['id']]['en_cola']=$fila['pnrs_en_cola'];
				$info[$fila['id']]['item']=$fila['item'];
			}
		}else{
			echo "ERROR: ".$connectdb->error." ON <br>$query<br>";
		}
		break;
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
        	switch($_GET['area']){
					case 'llamadas':
		                echo "<th >Creado</th>\n";
		                echo "<th >Canal</th>\n";
		                echo "<th >Motivo</th>\n";
		                echo "<th >Localizador</th>\n";
						break;
					case 'funciones':
						echo "<th >Creado</th>\n";
		                echo "<th >Actividad</th>\n";
		                echo "<th >Detalle</th>\n";
		                break;
				}
        	?>
        </tr>
        </thead>
        <tbody>

        <?php
            unset($key,$iden);
            if(isset($info)){
	            foreach($info as $key => $iden){
	                echo "<tr>\n";
					switch($_GET['area']){
						case 'llamadas':
			                echo "<td >".$iden['Creado']."</td>\n";
			                echo "<td >".$iden['Canal']."</td>\n";
			                echo "<td >".$iden['Motivo']."</td>\n";
			                echo "<td >".$iden['localizador']."</td>\n";
							break;
						case 'funciones':
							echo "<td >".$iden['Creado']."</td>\n";
			                echo "<td >".$iden['actividad']."</td>\n";
							IF($iden['pnr']==NULL){
								IF($iden['loc']==NULL){
									IF($iden['em']==NULL){
										IF($iden['aerolinea']==NULL){
											IF($iden['en_cola']==NULL){
												echo "<td >".$iden['item']." (item genérico)</td>\n"; 
											}else{
												echo "<td >".$iden['en_cola']." (PNRs en cola)</td>\n"; 
											}
										}else{
											echo "<td >".$iden['aerolinea']." (Aerolinea)</td>\n"; 
										}
									}else{
										echo "<td >".$iden['em']." (EM)</td>\n";
									}
								}else{
									echo "<td >".$iden['loc']." (Localizador)</td>\n";
								}
							}else{
								echo "<td >".$iden['pnr']." (PNR)</td>\n";
							}
			                break;
					}
					echo "</tr>\n";
	            }
            }

        ?>
        </tbody>
   </table>
 </div>
<div id='login'></div>
<div id='error'></div>
