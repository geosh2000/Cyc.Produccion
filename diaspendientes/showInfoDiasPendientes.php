<?php
date_default_timezone_set('America/Bogota');
$id=$_POST['id'];
$getMotif=$_POST['motif'];
$newMotif=$_POST['newmotif'];
$getDias=$_POST['dias'];
$getCase=$_POST['case'];
$thisMonth=intval(date('m'));
$thisDay=intval(date('d'));
$thisYear=intval(date('y'));

//Upload Info
if($newMotif!=NULL){
	include("../connectDB.php");
	$queryUpload = "INSERT INTO `Dias Pendientes` (indice,id,`dias asignados`,day,month,year,motivo) VALUES (NULL,".$id.",".$getDias.",".$thisDay.",".$thisMonth.",".$thisYear.",'".$newMotif."')";
	mysql_query($queryUpload);
	$RedimStatus="AGREGADOS ".$getDias." d&iacutea(s) con motivo: \"".$newMotif."\"";
	mysql_close();

}
if($getMotif!=NULL){
if($getCase!=NULL){ 
	include("../connectDB.php");
	
	//Get Current
	$queryMot="SELECT * FROM `Dias Pendientes`\n"
    . "WHERE (`id`=".$id." AND `motivo`='".$getMotif."')";
	$resultMot=mysql_query($queryMot);
	$numMot=mysql_numrows($resultMot);
	$queryRed="SELECT * FROM `Dias Pendientes Redimidos`\n"
    . "WHERE (`id`=".$id." AND `motivo`='".$getMotif."')";
	$resultRed=mysql_query($queryRed);
	$numRed=mysql_numrows($resultRed);
	
	$diasasignRed=0;
	$diasasignMot=0;
	
	$iRed=0;
	while ($iRed<$numRed){
		$temp=mysql_result($resultRed,$iRed,"dias");
		$diasasignRed=$diasasignRed+$temp;
		$iRed++;
	}
	$iMot=0;
	while ($iMot<$numMot){
		$temp= mysql_result($resultMot,$iMot,"dias asignados");
		$diasasignMot=$diasasignMot+$temp;
		$iMot++;
	}
	
	
	if($getDias<=$diasasignMot-$diasasignRed){
	$pendientes=$diasasignMot-$diasasignRed-$getDias;
	$RedimStatus="REDIMIDOS<br><br>Total Pendientes: ".$pendientes;
	//Upload New
	$queryUpload = "INSERT INTO `Dias Pendientes Redimidos` (indice,id,dias,day,month,year,motivo,caso) VALUES (NULL,".$id.",".$getDias.",".$thisDay.",".$thisMonth.",".$thisYear.",'".$getMotif."',".$getCase.")";
	mysql_query($queryUpload);
	$RedimStatus="REDIMIDOS<br><br>Total Pendientes: ".$pendientes;

	
	
	
	}else{
	$RedimStatus="ERROR!!<br><br>Asignados: ".$diasasignMot." Redimidos: ".$diasasignRed." Solicitados: ".$getDias;}

	


	mysql_close();
}else{$RedimStatus="FALTA CASO";}}









include("../DBAsesores.php");
include("../DBDiasPendientes.php");
include("../DBDiasRedimidos.php");





//Sort Names Alphabetically
asort($ASNCorto);
    $i=0;
    foreach($ASNCorto as $key => $val){
    $Name[$i]=$val;
    $Id[$i]=$ASid[$key];
    $i++;
    }

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
$tableContentR2="<td class=\"tg-baqh\">".$ASNCorto[$id-1]."</td><td class=\"tg-baqh\"><form method=\"POST\" action=\"showInfoDiasPendientes.php\"><select name=\"motif\" onchange=\"\"><option value=\"\">Select...</option>";
$i=1;
while ($i<$MotifIndex){
	$tableContentR2=$tableContentR2."<option value=\"".$MotifR[$i]."\">".$MotifR[$i]."</option>";
$i++;
    }
$tableContentR2=$tableContentR2."</option></td><td class=\"tg-baqh\"><input type=\"text\" name=\"id\" value=\"".$id."\" size=\"3\" readonly>";


$submitButton="<td class=tg-baqh><input type=\"Submit\" value=\"Enviar\"></td>";

?>
<body bgcolor="#000000">
<script>
function changeAsesor(str){
    if (str!==0){
    window.location.href= "http://comeycome.com/pt/diaspendientes/showInfoDiasPendientes.php?id="+str;
    }
    }
    </script>
    
    <script>



</script>

  
    
<div style="width:800px; margin:0 auto;">
<?php
if ($getMotif!=NULL){
echo "<table class=\"tg\" style=\"width:95%\">
<tr>
	<td class=\"tg-53rh1\" colspan=2>";
echo "Motivo: ".$getMotif." //  D&iacuteas: ".$getDias."</td></tr>";
echo "<tr><td class=\"tg-baqh\" colspan=2>Status: ".$RedimStatus."</td></tr></table><br><br><br>";
} 
if ($newMotif!=NULL){
echo "<table class=\"tg\" style=\"width:95%\">
<tr>
	<td class=\"tg-53rh1\" colspan=2>";
echo "Motivo: ".$newMotif." //  D&iacuteas: ".$getDias."</td></tr>";
echo "<tr><td class=\"tg-baqh\" colspan=2>Status: ".$RedimStatus."</td></tr></table><br><br><br>";
} 
?> 
</div>
<style type="text/css">
.tg  {border-collapse:collapse;border-spacing:0;}
.tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
.tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
.tg .tg-baqh{background-color:#FFFFFF;text-align:center;vertical-align:top;border-style: solid;border-color: #ccc394;}
.tg .tg-baqh3{text-align:center;vertical-align:top}
.tg .tg-baqh1{/* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#e1ffff+0,e1ffff+7,e1ffff+12,fdffff+12,e6f8fd+30,c8eefb+54,bee4f8+75,b1d8f5+100;Blue+Pipe+%232 */
background: #e1ffff; /* Old browsers */
background: -moz-linear-gradient(top,  #e1ffff 0%, #e1ffff 7%, #e1ffff 12%, #fdffff 12%, #e6f8fd 30%, #c8eefb 54%, #bee4f8 75%, #b1d8f5 100%); /* FF3.6-15 */
background: -webkit-linear-gradient(top,  #e1ffff 0%,#e1ffff 7%,#e1ffff 12%,#fdffff 12%,#e6f8fd 30%,#c8eefb 54%,#bee4f8 75%,#b1d8f5 100%); /* Chrome10-25,Safari5.1-6 */
background: linear-gradient(to bottom,  #e1ffff 0%,#e1ffff 7%,#e1ffff 12%,#fdffff 12%,#e6f8fd 30%,#c8eefb 54%,#bee4f8 75%,#b1d8f5 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#b1d8f5', endColorstr='#e1ffff',GradientType=0 ); /* IE6-9 */
text-align:center;vertical-align:top;font-weight:bold;border-style: solid;border-color: #b1d8f5}
.tg .tg-f8tx{color:#000000;text-align:center;vertical-align:top}
.tg .tg-nuus{background-color:#f9f1e6;color:#000000;text-align:center}
.tg .tg-53rh{/* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#f7ebc0+0,c9c190+76,ccc394+100 */
background: #f7ebc0; /* Old browsers */
background: -moz-linear-gradient(top,  #f7ebc0 0%, #c9c190 76%, #ccc394 100%); /* FF3.6-15 */
background: -webkit-linear-gradient(top,  #f7ebc0 0%,#c9c190 76%,#ccc394 100%); /* Chrome10-25,Safari5.1-6 */
background: linear-gradient(to bottom,  #f7ebc0 0%,#c9c190 76%,#ccc394 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f7ebc0', endColorstr='#ccc394',GradientType=0 ); /* IE6-9 */
text-align:center;vertical-align:top;border-style: solid;border-color: #ccc394;}
.tg .tg-53rh1{/* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#f7ebc0+0,c9c190+76,ccc394+100 */
background: #f7ebc0; /* Old browsers */
background: -moz-linear-gradient(top,  #f7ebc0 0%, #c9c190 76%, #ccc394 100%); /* FF3.6-15 */
background: -webkit-linear-gradient(top,  #f7ebc0 0%,#c9c190 76%,#ccc394 100%); /* Chrome10-25,Safari5.1-6 */
background: linear-gradient(to bottom,  #f7ebc0 0%,#c9c190 76%,#ccc394 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f7ebc0', endColorstr='#ccc394',GradientType=0 ); /* IE6-9 */
text-align:center;vertical-align:top;font-weight:bold;border-color: #ccc394;}
.tg .tg-nudq{color:#000000;text-align:center}
.tg .tg-ozja{background-color:#F0F0F5;text-align:center;vertical-align:top;border-style: solid;border-color: #ccc394;}
</style>
<div style="width:800px; margin:0 auto;">
<table class="tg" style="width:95%">
<tr>
	<td class="tg-53rh1" colspan=2>Asignaci&oacuten de D&iacuteas Pendientes</td>
</tr>
<tr>
	<td class="tg-53rh">Asesor</td>
	<td class="tg-baqh"><form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>"><select name="id" onchange="this.form.submit();">
    <option value="">Select...</option>
    <?php
    $i=0;
    while ($i<$ASnum){
    	echo "<option value=\"".$Id[$i]."\">".$Name[$i]."</option>";
    $i++;
    }
    
    ?>
    </select></form></td>
</tr>
</table>
<br>
<br>
<br>
<table class="tg" style="width:95%">
  <tr>
  	<td class="tg-53rh1"><?php echo $ASNCorto[$id-1]; ?></td>
  </tr>
</table>
<table class="tg" style="width:95%">
  <tr>
    <?php echo $tableTitles; ?>
  </tr>
  <tr>
    <?php echo $tableContent; ?>
    
  </tr>
  <tr>
    <?php echo $tableContentR; ?>
    
  </tr>
   <tr>
    <?php echo $tableContentT; ?>
    
  </tr>
</table>
<br><br><br><br>
<table class="tg" style="width:95%">
<tr>
	<td class="tg-53rh1" colspan=2>Redimir D&iacuteas</td>
</tr>

</table>

<table class="tg" style="width:95%">
  <tr>
  	<td class="tg-53rh">Asesor</td>
  	<td class="tg-53rh">Motivo</td>
  	<td class="tg-53rh">ID</td>
  	<td class="tg-53rh">Dias a Redimir</td>
  	<td class="tg-53rh">Caso</td>
  	<td class="tg-53rh"></td>  	
  	
  </tr>
  <tr>
  	<?php echo $tableContentR2; ?>
  	<td class="tg-baqh"><form><input type="text" name="dias" maxlength="2" size="2"></td>
  	<td class="tg-baqh"><form><input type="text" name="case" maxlength="7" size="7"></td>
  	<?php echo $submitButton; ?>
  </tr>
  
 </form></table>
 <br><br><br><br>
<table class="tg" style="width:95%">
<tr>
	<td class="tg-53rh1" colspan=2>Asignar D&iacuteas</td>
</tr>

</table>

<table class="tg" style="width:95%">
  <tr>
  	<td class="tg-53rh">Asesor</td>
  	<td class="tg-53rh">Motivo</td>
  	<td class="tg-53rh">ID</td>
  	<td class="tg-53rh">Dias a Asignar</td>
  	
  	<td class="tg-53rh"></td>  	
  	
  </tr>
  <tr>
  	<td class="tg-baqh"><form method="POST" action="showInfoDiasPendientes.php"><?php echo $ASNCorto[$id-1];?></td>
  	<td class="tg-baqh"><input type="text" name="newmotif" maxlength="30" size="30"></td>
  	<td class="tg-baqh"><input type="text" name="id" maxlength="3" size="3" value="<?php echo $id; ?>" readonly></td>
  	<td class="tg-baqh"><input type="text" name="dias" maxlength="2" size="2"></td>
  	
  	<?php echo $submitButton; ?>
  </tr>
  
 </form></table></div>
 </body>
  	