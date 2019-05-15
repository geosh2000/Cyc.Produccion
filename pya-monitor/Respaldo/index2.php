<?
session_start();
$this_page=$_SERVER['PHP_SELF'];


if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="monitor_pya";
echo "<a href='monitor2.php'>Ir a Version Beta</a>";
$menu_monitores="class='active'";
include('getDataPyA.php');

global $num;
global $idnumber;
global $NCorto;
global $tiempo;
global $control;
global $fecha;
global $horario;
global $exc;





$n=1;
$i=0;
$fa=1;
$re=1;
$au=1;
$i=0;
while ($i<$num){
if ($control[$i] != 0) {
$date=$fecha[$i];
$control2[$n]=$control[$i];
$n++;
switch ($control[$i]){
	case 1:
		$ras[$re]=$NCorto[$i];
		$rho[$re]=$horario[$i];
		if($exc[$i]!=0){
			switch($exc[$i]){
				case 0:
					$rti[$re]= "Eliminado";
					break;
				case 1:
					$rti[$re]= "Notificado";
					break;
				case 2:
					$rti[$re]="Justificado";
					break;
				case 3:
					$rti[$re]="Regresado";
					break;
				case 4:
					$rti[$re]="No Aplica";
					break;
				case 5:
					$rti[$re]="Registrado (".$tiempo[$i].")";
					break;
			}
		}else{
			$rti[$re]=$tiempo[$i];
		}
		$rid[$re]=$idnumber[$i];
		$re++;
		break;
	case 2:
		$fas[$fa]=$NCorto[$i];
		$fho[$fa]=$horario[$i];
		$fti[$fa]=$tiempo[$i];
				$fid[$fa]=$idnumber[$i];
		$fa++;
		break;
	default:
		$aas[$au]=$NCorto[$i];
		$atip[$au]=$control[$i];
		
		$au++;
		break;
		
}
}
$i++;
}



?>
<head>
<link rel="stylesheet" type="text/css"
          href="http://comeycome.com/pt/styles/tables1.css"/>
</head>

<style type="text/css">
 @-webkit-keyframes invalid {
  from { background-color: yellow; }
  to { background-color: red; }
}
@-moz-keyframes invalid {
  from { background-color: yellow; }
  to { background-color: red; }
}
@-o-keyframes invalid {
  from { background-color: yellow; }
  to { background-color: red; }
}
@keyframes invalid {
  from { background-color: yellow; }
  to { background-color: red; }
}
.invalid {
  -webkit-animation: invalid 1s infinite; /* Safari 4+ */
  -moz-animation:    invalid 1s infinite; /* Fx 5+ */
  -o-animation:      invalid 1s infinite; /* Opera 12+ */
  animation:         invalid 1s infinite; /* IE 10+ */
}

td {
    padding: 1em;
}
}
</style>
<script>

var total =60000;
var myVar = setInterval(function(){ myTimer() }, 1000);

function myTimer() {
   total= total-1000;
    document.getElementById("timerok").innerHTML = "Reload in " + total/1000 + " sec.";
}
</script>

<script>

function updateStatus(str,iden,old) {
    
    if (str == "") {
        document.getElementById("txtHint"+iden).innerHTML = "";
        return;
    } else { 
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("textHint"+iden).innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET","uploadpyamon1.php?reg=3&"+str+"&old="+old,true);
        xmlhttp.send();
        
        
    }
}

</script>
<script>

function updateStatusF(str,iden,old) {
    
    if (str == "") {
        document.getElementById("txtHintF"+iden).innerHTML = "";
        return;
    } else { 
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("textHintF"+iden).innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET","uploadpyamon1.php?reg=4&"+str+"&old="+old,true);
        xmlhttp.send();
        
        
    }
}

</script>

<? include("../common/menu.php"); ?>
<table style='width:100%' class="t2">
<tr class='title'>
    <th colspan="2">Monitor PyA CC Canc&uacuten</th>
  </tr>
  <tr class='subtitle'>
    <th><strong>Ultima Actualizaci&oacuten:   </strong><?php echo $date; ?></th>
    <th id="timerok" ></th>
  </tr>
</table>
<br>
<table style='width:100%' class="t2">
  
  <tr>
    <th class="title" colspan="3">Sin Logueo</th>
    <td class="tg-698h" rowspan="<? echo $n; ?>"></td>
    <th class="title" colspan="4">Retardos</th>
    <td class="tg-6un8" rowspan="<? echo $n; ?>"></td>
    <th class="title" colspan="2">Ausentismos</th>
  </tr>
  <tr>
    <td class="subtitle">Asesor</td>
    <td class="subtitle">Horario</td>
    <td class="subtitle">Tiempo</td>
    
    <td class="subtitle">Asesor</td>
    <td class="subtitle">Horario</td>
    <td class="subtitle">Tiempo</td>
    <td class="subtitle">Excepcion</td>
    <td class="subtitle">Asesor</td>
    <td class="subtitle">Tipo</td>
  </tr>
<?
  
$x=1;
$span=9;

if ($fa > $re){
	if ($fa > $au){
		$max=$fa;
	}else{
		$max=$au;
	}
}else{
	if ($re > $au){
		$max=$re;
	}else{
		$max=$au;
	}
}
	

while ($x<$max){
	if ($control2[$x] != 0) {
		if($x % 2 ==0){$class="class='pair'";}else{$class="class='odd'";}
		$tipo="";
		$clase="tg-i81m";
		$clasef="tg-i81m";
		switch ($atip[$x]){
			case 3:
				$tipo="PC";
				break;
			case 4:
				$tipo="PS";
				break;
			case 5:
				$tipo="Vacaciones";
				break;
			case 6:
				$tipo="Suspension";
				break;
			case 7:
				$tipo="Incapacidad";
				break;
		}
		if ($rti[$x]>10){
			$clase="invalid";
		}
		if ($fti[$x]>10 && $fti[$x]<60){
			$clasef="invalid";
		}
		if($fti[$x]>=60){
			$fti[$x]="Falta";
		}
		echo "  
		  <tr ".$class.">
		    <td class=\"tg-3we0\">".$fas[$x]."</td>
		    <td class=\"tg-i81m\">".$fho[$x]."</td>
		    <td id=\"textHintF".$x."\" class=\"".$clasef."\">".$fti[$x]."</td>
		    <td class=\"tg-3we0\">".$ras[$x]."</td>
		    <td class=\"tg-i81m\">".$rho[$x]."</td>
		    <td id=\"textHint".$x."\" class=\"".$clase."\">".$rti[$x]."</td>
		    <td class=\"tg-i81m\">";
		if ($ras[$x] != NULL){
			echo "<form><select name=\"excR\" onchange=\"updateStatus(this.value,".$x.",".$tiempo[$rid[$x]-1].")\">
			  <option value=\"id1=".$rid[$x]."&e1=0\">Select...</option>
			  <option value=\"id1=".$rid[$x]."&e1=1\">Notificado</option>
			  <option value=\"id1=".$rid[$x]."&e1=2\">Justificado</option>
			  <option value=\"id1=".$rid[$x]."&e1=3\">Regresado</option>
			  <option value=\"id1=".$rid[$x]."&e1=4\">No Aplica</option>
			  <option value=\"id1=".$rid[$x]."&e1=5\">Registrado</option>
			  <option value=\"id1=".$rid[$x]."&e1=0\">Eliminar Excepcion</option>
			</select></form>";
		}
		echo "</td>
		    <td class=\"tg-3we0\">".$aas[$x]."</td>
		    <td class=\"tg-i81m\">".$tipo."</td>
		  </tr>";
		 $span=$span++;
	}
$x++;
}

?>
  
</table>
</div>


</body>
<script>
setTimeout(function() {
    window.location.reload();
}, 60000);
</script>