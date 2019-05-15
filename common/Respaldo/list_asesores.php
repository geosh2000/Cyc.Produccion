<?php
include("../connectDB.php");
include("../DBAsesores.php");
include("../DBPcrcs.php");

/*
Instrucciones:

$inputname: Nombre del <select>

$activeset:	0 - Asesores Inactivos
		1 - Asesores Activos
		3 - Todos los Asesores

$dept:		0 a 50 - id del Departamento
		100 - Todos los departamentos

$select:	0 - Only print options
		1 - Print select tags
*/

function listAsesores($inputname,$activeset,$dept,$select,$variable=NULL){
	global $ASNCorto_Sorted, $ASid_Sorted, $ASactive_Sorted, $ASdepto_Sorted;
	if($select==1){echo "<select name='$inputname'>\n";}
    if($dept==100){
		switch($activeset){
			case 1:
				$activeset=101;
				break;
			case 0:
				$activeset=100;
				break;
			case 3:
				$activeset=103;
				break;

		}
	}
	foreach($ASNCorto_Sorted as $list_id => $list_nombre){
	    if($variable==$ASid_Sorted[$list_id]){$select='selected';}else{$select="";}
		switch($activeset){
			case 3:
				if($ASdepto_Sorted[$list_id]==$dept){
						echo "\t<option value='$ASid_Sorted[$list_id]' $select>$list_nombre</option>\n";
					}
				break;
			case 103:
				echo "\t<option value='$ASid_Sorted[$list_id]' $select>$list_nombre</option>\n";
				break;
			case 101:
				if($ASactive_Sorted[$list_id]==1){
					echo "\t<option value='$ASid_Sorted[$list_id]' $select>$list_nombre</option>\n";
				}
				break;
			case 100:
				if($ASactive_Sorted[$list_id]==0){
					echo "\t<option value='$ASid_Sorted[$list_id]' $select>$list_nombre</option>\n";
				}
				break;
			default:
				if($ASactive_Sorted[$list_id]==$activeset){
					if($ASdepto_Sorted[$list_id]==$dept){
						echo "\t<option value='$ASid_Sorted[$list_id]' $select>$list_nombre</option>\n";
        			}
				}
				break;
		}
	}
	unset($list_nombre);
	if($select==1){echo "</select>\n";}

}

//List Depts
function list_departamentos($variable){	
	global $pcrcs_num, $pcrcs_id_Sorted, $pcrcs_departamento_Sorted;
	$i=0;
	$departs="<option value'' selected>Selecciona...</option>";
	while($i<$pcrcs_num){
		if($pcrcs_id_Sorted[$i]==$variable){$sel="selected";}else{$sel="";}
		$departs="$departs\t\t<option value='$pcrcs_id_Sorted[$i]' $sel>$pcrcs_departamento_Sorted[$i]</option>\n";
	$i++;
	}
	echo $departs;
}

?>