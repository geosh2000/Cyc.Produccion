<?php
include_once("../modules/modules.php");

initSettings::start(true, 'schedules_diaspendientes');
initSettings::printTitle('Asignación de Días Pendientes');

timeAndRegion::setRegion('Cun');

date_default_timezone_set('America/Bogota');

$id=$_POST['id'];
$getMotif=$_POST['motif'];
$newMotif=$_POST['newmotif'];
$getDias=$_POST['dias'];
$getCase=$_POST['case'];
$thisMonth=intval(date('m'));
$thisDay=intval(date('d'));
$thisYear=intval(date('y'));

$tbody="<td>Departamento</td><td><select name='departamento' id='departamento'><option value='' >Selecciona...</option>";
$query="SELECT id, Departamento FROM PCRCs ORDER BY Departamento";
if($result=Queries::query($query)){
	while($fila=$result->fetch_assoc()){
		$tbody.="<option value='".$fila['id']."'>".utf8_encode($fila['Departamento'])."</option>";
	}
}
$tbody.="</select></td><td>Asesor</td><td><select name='asesor' id='asesor'><option value='' >Selecciona...</option>";
$query="SELECT id, Nombre, `id Departamento` as dep FROM Asesores ORDER BY Nombre";
if($result=Queries::query($query)){
	while($fila=$result->fetch_assoc()){
		$tbody.="<option value='".$fila['id']."' class='asesor_option as_".$fila['dep']."'>".utf8_encode($fila['Nombre'])."</option>";
	}
}
$tbody.="</select></td>";

Filters::showFilterNOFORM('search','Buscar',$tbody);

?>
<br>
<table class='t2' style='width: 80%; margin: auto; text-align: center;'>
	<tr>
		<th>Asesor</th><th>Motivo</th><th>Dias Asignados</th><th>Dias Redimidos</th>
	</tr>
</table>


<?php



//Upload Info
if($newMotif!=NULL){

	$query = "INSERT INTO `Dias Pendientes` (indice,id,`dias asignados`,day,month,year,motivo,User) VALUES (NULL,'$id','$getDias','$thisDay','$thisMonth','$thisYear','$newMotif','".$_SESSION['id']."')";
	if($result=Queries::query($query)){
		$RedimStatus="AGREGADOS ".$getDias." d&iacutea(s) con motivo: \"".$newMotif."\"";
	}else{
		$RedimStatus="ERROR";
	}

}

if($getMotif!=NULL){
	
	if($getCase!=NULL){ 
	
		//Get Current
		$query="SELECT COUNT(*) as dias FROM `Dias Pendientes` WHERE (`id`=$id AND `motivo`='$getMotif')";
		if($result=Queries::query($query)){
			$fila=$result->fetch_assoc();
			$diasasignMot=$fila['dias'];
		}
		
		$query="SELECT COUNT(*) as dias FROM `Dias Pendientes Redimidos` WHERE (`id`=$id AND `motivo`='$getMotif')";
		if($result=Queries::query($query)){
			$fila=$result->fetch_assoc();
			$diasasignRed=$fila['dias'];
		}
		
		if($getDias<=$diasasignMot-$diasasignRed){
			$pendientes=$diasasignMot-$diasasignRed-$getDias;
			$RedimStatus="REDIMIDOS<br><br>Total Pendientes: ".$pendientes;
			
			//Upload New
			$query = "INSERT INTO `Dias Pendientes Redimidos` (indice,id,dias,day,month,year,motivo,caso,User) VALUES (NULL,'$id','$getDias','$thisDay','$thisMonth','$thisYear','.$getMotif','$getCase','".$_SESSION['id']."')";
			if($result=Queries::query($query)){
				$RedimStatus="REDIMIDOS<br><br>Total Pendientes: ".$pendientes;
			}			
		}else{
			$RedimStatus="ERROR!!<br><br>Asignados: ".$diasasignMot." Redimidos: ".$diasasignRed." Solicitados: ".$getDias;
		}
	
	}else{
		$RedimStatus="FALTA CASO";
	}
}



//N Corto
$qname="SELECT `N Corto` FROM Asesores WHERE id=$id";
$name=mysql_result(mysql_query($qname),0,'N Corto');





include("../DBAsesores.php");
include("../DBDiasPendientes.php");
include("../DBDiasRedimidos.php");







//Suma de dias pendientes
$thisSum=0;
$MotifIndex=1;

//Matriz de Asignados
while ($thisSum<$DPnum){
	if ($thisId[$DPid[$thisSum]]==NULL){$thisId[$DPid[$thisSum]]=0;}
	$thisId[$DPid[$thisSum]]=$thisId[$DPid[$thisSum]]+$DPdias[$thisSum];
	$im=1;
	$xm=0;
	while ($im<=$MotifIndex){
		if ($thisMotif[$im]!=$DPmotivo[$thisSum]){$xm++;}
		else{ $motivo[$DPid[$thisSum]][$im]= $motivo[$DPid[$thisSum]][$im]+$DPdias[$thisSum];}
	
		
	$im++;
	}
	if ($xm!=$MotifIndex-1){
	$thisMotif[$MotifIndex]=$DPmotivo[$thisSum];
	$motivo[$DPid[$thisSum]][$MotifIndex]=$motivo[$DPid[$thisSum]][$MotifIndex]+$DPdias[$thisSum];
	$MotifIndex++;
	}
	$thisSum++;
}

//Matriz de Redimidos
$thisSum=0;
while ($thisSum<$DPRnum){
	if ($thisIdR[$DPRid[$thisSum]]==NULL){$thisIdR[$DPRid[$thisSum]]=0;}
	$thisIdR[$DPRid[$thisSum]]=$thisIdR[$DPRid[$thisSum]]+$DPRdias[$thisSum];
	$im=1;
	$xm=0;
	while ($im<=$MotifIndex){
		if ($thisMotif[$im]==$DPRmotivo[$thisSum]){
		 $motivoR[$DPRid[$thisSum]][$im]=$motivoR[$DPRid[$thisSum]][$im]+$DPRdias[$thisSum];}
	
		
	$im++;
	}
	
	$thisSum++;
}



//Contenido Titulos de Tabla
$tableTitles= 	"<td class=\"tg-53rh\">Movimiento</td>
    		<th class=\"tg-53rh\">Total D&iacuteas</th>";
$titles=1;
while ($titles<$MotifIndex){
	if($motivo[$id][$titles]!=NULL){
	$tableTitles= $tableTitles."<th class=\"tg-53rh\">".$thisMotif[$titles]."</th>";
	}
$titles++;
}
$tableTitles= $tableTitles."<th class=\"tg-53rh\"></th>";


//Contenido Dias Asignados
$i2=0;
$titles=1;
$tableContent="<td class=\"tg-baqh\">Asignados</td>
    <td class=\"tg-baqh\">".$thisId[$id]."</td>";
$tableContentR="<td class=\"tg-ozja\">Redimidos</td>
    <td class=\"tg-ozja\">".$thisIdR[$id]."</td>";
$thisIdT=$thisId[$id]-$thisIdR[$id];
$tableContentT="<td class=\"tg-baqh1\">Total Pendientes</td>
    <td class=\"tg-baqh1\">".$thisIdT."</td>";
while ($titles<$MotifIndex){
	//Asignados
	if ($motivo[$id][$titles]!=NULL){
	$tableContent= $tableContent."<td class=\"tg-baqh\">";
	
	$tableContent= $tableContent.$motivo[$id][$titles];
	$tableContent= $tableContent."</td>";
	//Redimidos
	$tableContentR= $tableContentR."<td class=\"tg-ozja\">";
	if ($motivoR[$id][$titles]==NULL){$motivoR[$id][$titles]=0;}
	$tableContentR= $tableContentR.$motivoR[$id][$titles];
	$tableContentR= $tableContentR."</td>";
	//Totales
	$tableContentT= $tableContentT."<td class=\"tg-baqh1\">";
	$motivoT[$id][$titles]=$motivo[$id][$titles]-$motivoR[$id][$titles];
	$tableContentT= $tableContentT.$motivoT[$id][$titles];
	$tableContentT= $tableContentT."</td>";
	}
$titles++;
}
$tableContent= $tableContent."<td class='tg-baqh'><form action='detailsAsign.php' method='POST' target='_blank'><input type='hidden' name='tipo' value='1'><input type='hidden' name='id' value='$id'><input type='submit' value='Detalles '></form></td>";


$tableContentR= $tableContentR."<td class='tg-ozja'><form action='detailsAsign.php' method='POST' target='_blank'><input type='hidden' name='tipo' value='2'><input type='hidden' name='id' value='$id'><input type='submit' value='Detalles'></td>";

$tableContentT=$tableContentT."<td class=\"tg-baqh1\"></form></td>";

//Sort Motifs Alphabetically
asort($thisMotif);
    $i=1;
    foreach($thisMotif as $key => $val){
    $MotifR[$i]=$val;
    
    $i++;
    }


//Contenido Redimir
$thispage=$_SERVER['PHP_SELF'];
$tableContentR2="<td class=\"tg-baqh\">".$name."</td><td class=\"tg-baqh\"><form method=\"POST\" action=\"$thispage\"><select name=\"motif\" onchange=\"\"><option value=\"\">Select...</option>";
$i=1;
while ($i<$MotifIndex){
	$tableContentR2=$tableContentR2."<option value=\"".$MotifR[$i]."\">".$MotifR[$i]."</option>";
$i++;
    }
$tableContentR2=$tableContentR2."</option></td><td class=\"tg-baqh\"><input type=\"text\" name=\"id\" value=\"".$id."\" size=\"3\" readonly>";


$submitButton="<td class=tg-baqh><input type=\"Submit\" value=\"Enviar\"></td>";

?>

          
<body>
<script>
function changeAsesor(str){
    if (str!==0){
    window.location.href= "../diaspendientes/showInfoDiasPendientes.php?id="+str;
    }
    }
    </script>
    
    <script>



</script>

  
<?php include("../common/menu.php"); ?>   

<?php
if ($getMotif!=NULL){
echo "<div style='width:80%; margin:auto'><table class='t2' style='width:95%'>
<tr>
	<td class='title' colspan=2>";
echo "Motivo: $getMotif //  D&iacuteas: $getDias</td></tr>";
echo "<tr><td class='pair' colspan=2>Status: $RedimStatus</td></tr></table></div><br><br><br>";
} 
if ($newMotif!=NULL){
echo "<div style='width:80%; margin:auto'><table class='t2' style='width:95%'>
<tr>
	<td class='title' colspan=2>";
echo "Motivo: $newMotif //  D&iacuteas: $getDias</td></tr>";
echo "<tr><td class='pair' colspan=2>Status: $RedimStatus</td></tr></table></div><br><br><br>";
} 
?> 

<div style="width:80%; margin:auto">
<table class="t2" style="width:95%">
<tr>
	<th class="title" colspan=2>Asignaci&oacuten de D&iacuteas Pendientes</th>
</tr>
<tr>
	<td class="subtitle">Asesor</td>
	<td class="pair"><form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>"><select name="id" onchange="this.form.submit();">
    <option value="">Select...</option>
    <?php
    listAsesores('id',1,100,0);

    
    ?>
    </select></form></td>
</tr>
</table>
<br>
<br>
<br>
<table class="t2" style="width:95%">
  <tr>
  	<th class="title"><?php echo $name; ?></th>
  </tr>
</table>
<table class="t2" style="width:95%">
  <tr class="title">
    <?php echo $tableTitles; ?>
  </tr>
  <tr class="pair">
    <?php echo $tableContent; ?>
    
  </tr>
  <tr class="odd">
    <?php echo $tableContentR; ?>
    
  </tr>
   <tr class="total">
    <?php echo $tableContentT; ?>
    
  </tr>
</table>
<br><br><br><br>
<table class="t2" style="width:95%">
<tr>
	<th class="title" colspan=2>Redimir D&iacuteas</th>
</tr>

</table>

<table class="t2" style="width:95%">
  <tr class="subtitle">
  	<td>Asesor</td>
  	<td>Motivo</td>
  	<td>ID</td>
  	<td>Dias a Redimir</td>
  	<td>Caso</td>
  	<td></td>  	
  	
  </tr>
  <tr class="pair">
  	<?php echo $tableContentR2; ?>
  	<td><form><input type="text" name="dias" maxlength="2" size="2"></td>
  	<td><form><input type="text" name="case" maxlength="7" size="7"></td>
  	<?php echo $submitButton; ?>
  </tr>
  
 </form></table>
 <br><br><br><br>
<table class="t2" style="width:95%">
<tr>
	<th class="title" colspan=2>Asignar D&iacuteas</th>
</tr>

</table>

<table class="t2" style="width:95%">
  <tr class="subtitle">
  	<td>Asesor</td>
  	<td>Motivo</td>
  	<td>ID</td>
  	<td>Dias a Asignar</td>
  	
  	<td></td>  	
  	
  </tr>
  <tr class="pair">
  	<td><form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>"><?php echo $name;?></td>
  	<td><input type="text" name="newmotif" maxlength="30" size="30"></td>
  	<td><input type="text" name="id" maxlength="3" size="3" value="<?php echo $id; ?>" readonly></td>
  	<td><input type="text" name="dias" maxlength="2" size="2"></td>
  	
  	<?php echo $submitButton; ?>
  </tr>
  
 </form></table></div></div></div>
 </body>
