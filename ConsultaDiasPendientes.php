<?php
include("DBAsesores.php");
include("DBDiasPendientes.php");

$tableContent="";
$tableTitles="";
$operator=$_GET['operator'];
$getId=$_GET['id'];

switch ($operator){
	case NULL or 0:
		$tableTitles= "data.addColumn('number', 'ID'); data.addColumn('string', 'Asesor'); data.addColumn('number', 'Dias Asignados'); data.addColumn('string', 'Fecha'); data.addColumn('string', 'motivo');";
		$i2=0;
		while ($i2<$DPnum){
			$tableContent= $tableContent. "[".$DPid[$i2].",'".$ASNCorto[$DPid[$i2]-1]."',".$DPdias[$i2].",'".$DPday[$i2]."-".$DPmonth[$i2]."-".$DPyear[$i2]."','".$DPmotivo[$i2]."'],";
		$i2++;
		}
		break;
	case 5:
		$thisSum=0;
		$MotifIndex=1;

		while ($thisSum<$DPnum){
			if ($thisId[$DPid[$thisSum]]==NULL){$thisId[$DPid[$thisSum]]=0;}
			$thisId[$DPid[$thisSum]]=$thisId[$DPid[$thisSum]]+$DPdias[$thisSum];
			$im=1;
			$xm=0;
			while ($im<=$MotifIndex){
				if ($thisMotif[$im]!=$DPmotivo[$thisSum]){$xm++;}
				else{ $motivo[$DPid[$thisSum]][$im]=$DPdias[$thisSum];}
			
				
			$im++;
			}
			if ($xm!=$MotifIndex-1){
			$thisMotif[$MotifIndex]=$DPmotivo[$thisSum];
			$motivo[$DPid[$thisSum]][$MotifIndex]=$DPdias[$thisSum];
			$MotifIndex++;
			}
			$thisSum++;
		}
		$tableTitles= "data.addColumn('number', 'ID'); data.addColumn('string', 'Asesor'); data.addColumn('number', 'Total');";
		$titles=1;
		while ($titles<$MotifIndex){
			$tableTitles= $tableTitles."data.addColumn('number', '".$thisMotif[$titles]."');";
		$titles++;
		}
		$i2=0;
		while ($i2<$ASnum){
			if ($getId==NULL){
				if($thisId[$i2]!=NULL){
				$tableContent= $tableContent. "[".$i2.",'".$ASNCorto[$i2-1]."',".$thisId[$i2];
				$titles=1;
				while ($titles<$MotifIndex){
					$tableContent= $tableContent.",";
					if ($motivo[$i2][$titles]==NULL){$tableContent= $tableContent."0";}else{$tableContent= $tableContent.$motivo[$i2][$titles];}
				$titles++;
				}
				$tableContent= $tableContent."],";
				}
			}else{
				if($i2==$getId){
				$tableContent= $tableContent. "[".$i2.",'".$ASNCorto[$i2-1]."',".$thisId[$i2];
				$titles=1;
				while ($titles<$MotifIndex){
					$tableContent= $tableContent.",";
					if ($motivo[$i2][$titles]==NULL){$tableContent= $tableContent."0";}else{$tableContent= $tableContent.$motivo[$i2][$titles];}
				$titles++;
				}
				$tableContent= $tableContent."],";
				}
				}
			
		$i2++;
		}
		break;
}

?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1.1", {packages:["table"]});
      google.setOnLoadCallback(drawTable);

      function drawTable() {
        var data = new google.visualization.DataTable();
        <?php echo $tableTitles; ?>
        
        data.addRows([
        <?php echo $tableContent; ?>
          
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