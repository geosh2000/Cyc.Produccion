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
		$tableTitles= 	"<td class=\"tg-53rh\">Dia</td>
		    		<th class=\"tg-53rh\">Mes</th>
		    		<th class=\"tg-53rh\">A&ntildeo</th>
		    		<th class=\"tg-53rh\">Motivo</th>
		    		<th class=\"tg-53rh\">Dias</th>";
		//Contenido Tabla
		$i=0;
		$idcss=0;
		while ($i<$DPnum){
		
			if($DPid[$i]==$id){
				if($idcss % 2 == 0){$style="tg-baqh";}else{$style="tg-ozja";}
				$tableContent=$tableContent."<tr><td class=\"".$style."\">".$DPday[$i]."</td>
								<td class=\"".$style."\">".$DPmonth[$i]."</td>
								<td class=\"".$style."\">".$DPyear[$i]."</td>
								<td class=\"".$style."\">".$DPmotivo[$i]."</td>
								<td class=\"".$style."\">".$DPdias[$i]."</td></tr>";
				$idcss++;
			}
		$i++;
		}
		break;
	case 2:
		$titulo="Detalle de Dias Redimidos";
		//Contenido Titulos de Tabla
		$colspan=6;
		$tableTitles= 	"<td class=\"tg-53rh\">Dia</td>
		    		<th class=\"tg-53rh\">Mes</th>
		    		<th class=\"tg-53rh\">A&ntildeo</th>
		    		<th class=\"tg-53rh\">Motivo</th>
		    		<th class=\"tg-53rh\">Dias</th>
		    		<th class=\"tg-53rh\">Caso</th>";
		 //Contenido Tabla
		$i=0;
		$idcss=0;
		while ($i<$DPRnum){
			if($DPRid[$i]==$id){
				if($idcss % 2 == 0){$style="tg-baqh";}else{$style="tg-ozja";}	
				$tableContent=$tableContent."<tr><td class=\"".$style."\">".$DPRday[$i]."</td>
								<td class=\"".$style."\">".$DPRmonth[$i]."</td>
								<td class=\"".$style."\">".$DPRyear[$i]."</td>
								<td class=\"".$style."\">".$DPRmotivo[$i]."</td>
								<td class=\"".$style."\">".$DPRdias[$i]."</td>
								<td class=\"".$style."\"><a href=\"http://sos.pricetravel.com.mx/default.asp?".$DPRcaso[$i]."\" target=\"_blank\">".$DPRcaso[$i]."</a></td></tr>";
				$idcss++;
			}
		$i++;
		}
		break;
}




?>
<body bgcolor=#000000>
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
<table class="tg" style="width:80%">
	<tr>
		<td class="tg-53rh1" colspan="<?php echo $colspan; ?>"><?php echo $titulo; ?></td>
		
	</tr>
	<tr>
		<td class="tg-53rh1" colspan="<?php echo $colspan; ?>"><?php echo $ASNCorto[$id-1]; ?></td>
	</tr>
	<tr>
		<?php echo $tableTitles; ?>
	</tr>
	<?php echo $tableContent; ?>
</table></div>
</body>