									<?php
include("../DBDiasPendientes.php");
include("../DBDiasRedimidos.php");
include("../DBAsesores.php");

$id=$_POST['id'];
$tipo=$_POST['tipo'];

switch ($tipo){
	case 1:
		$titulo="Detalle de Dias Asignados";
		//Contenido Titulos de Tabla
		$colspan=5;
		$tableTitles= 	"<td>Dia</td>
		    		<td>Mes</td>
		    		<td>A&ntildeo</td>
		    		<td>Motivo</td>
		    		<td>Dias</td>";
		//Contenido Tabla
		$i=0;
		$idcss=0;
		while ($i<$DPnum){
		
			if($DPid[$i]==$id){
				if($idcss % 2 == 0){$style="pair";}else{$style="odd";}
				$tableContent=$tableContent."<tr class='$style'><td>".$DPday[$i]."</td>
								<td>".$DPmonth[$i]."</td>
								<td>".$DPyear[$i]."</td>
								<td>".$DPmotivo[$i]."</td>
								<td>".$DPdias[$i]."</td></tr>";
				$idcss++;
			}
		$i++;
		}
		break;
	case 2:
		$titulo="Detalle de Dias Redimidos";
		//Contenido Titulos de Tabla
		$colspan=6;
		$tableTitles= 	"<td>Dia</td>
		    		<td>Mes</td>
		    		<td>A&ntildeo</d>
		    		<td>Motivo</td>
		    		<td>Dias</td>
		    		<td>Caso</td>";
		 //Contenido Tabla
		$i=0;
		$idcss=0;
		while ($i<$DPRnum){
			if($DPRid[$i]==$id){
				if($idcss % 2 == 0){$style="pair";}else{$style="odd";}	
				$tableContent=$tableContent."<tr class='$style'><td>".$DPRday[$i]."</td>
								<td>".$DPRmonth[$i]."</td>
								<td>".$DPRyear[$i]."</td>
								<td>".$DPRmotivo[$i]."</td>
								<td>".$DPRdias[$i]."</td>
								<td><a href=\"http://sos.pricetravel.com.mx/default.asp?".$DPRcaso[$i]."\" target=\"_blank\">".$DPRcaso[$i]."</a></td></tr>";
				$idcss++;
			}
		$i++;
		}
		break;
}




?>
<head>
<link rel="stylesheet" type="text/css"
          href="http://comeycome.com/pt/styles/tables1.css"/>
</head>

<div style="width:800px; margin:0 auto;">
<table class="t2" style="width:80%">
	<tr>
		<th class="title" colspan="<?php echo $colspan; ?>"><?php echo $titulo; ?></th>
		
	</tr>
	<tr>
		<td class="title" colspan="<?php echo $colspan; ?>"><?php echo $ASNCorto[$id-1]; ?></td>
	</tr>
	<tr class="title">
		<?php echo $tableTitles; ?>
	</tr>
	<?php echo $tableContent; ?>
</table></div>
</body>