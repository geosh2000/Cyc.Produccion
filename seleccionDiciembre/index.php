<?
session_start();
$this_page=$_SERVER['PHP_SELF'];
if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
include("../connectDB.php");
include("../DBAsesores.php");
include("../common/list_asesores.php");
$credential="schedules_selectSpecial";
$menu_programaciones="class='active'";

//declare variables
$departamento=$_POST['dept'];
$id2=$_POST['id'];
$id=$_POST['asesor'];

$optSelected=$_POST['h'];
$bd="asesor sc";

//update sql
if(isset($_POST['asignar']) && $optSelected!=NULL){
	$query="UPDATE `horariosDic` SET `$bd`='$id' WHERE `id`='$optSelected'";
	mysql_query($query);
}

//query
$query="SELECT * FROM `horariosDic`";
$result=mysql_query($query);
$num=mysql_numrows($result);


$i=1;
while($i<=$num){
	$idhora[$i]=mysql_result($result,$i-1,'id');
	$asesor_v[$i]=mysql_result($result,$i-1,'asesor ventas');
	$asesor_sc[$i]=mysql_result($result,$i-1,'asesor sc');
$i++;
}


switch ($departamento){
	case 'V':
		$selV="selected";
		$dept_title="Ventas";
		$fin=36;
		$step=15;
		$asesor=$asesor_v;
		$depid=3;
		break;
	case 'SC':
		$selSC="selected";
		$dept_title="Servicio a Cliente";
		$fin=26;
		$step=10;
		$asesor=$asesor_sc;
		$depid=4;
		break;
}



//Function for listing Asesores
	function print_options($dept){
		global $id, $ASnum, $ASNCorto_Sorted, $ASdepto_Sorted, $ASactive_Sorted, $ASid_Sorted, $Asesor;
		
		$i=0;
		while ($i<$ASnum){
			//Print only Dept Asesores
			if($ASdepto_Sorted[$i]==$dept && $ASactive_Sorted[$i]==1){
				if($ASid_Sorted[$i]==$id){$sel=" selected"; $Asesor[$opt]=$i;}else{$sel="";}
				$optprint="$optprint<option value='$ASid_Sorted[$i]'$sel>$ASNCorto_Sorted[$i]</option>";
			}
		$i++;
		}
		
		echo $optprint;
	}
unset($id);

?>

<head>
<link rel="stylesheet" type="text/css" href="http://comeycome.com/pt/styles/tables1.css"/>
</head>

<? include("../common/menu.php"); $i=1;?>

<table class="t2" width='100%'><form action='<? $_SERVER['PHP SELF']; ?>' method='post'>
<tr class='title'>
	<th>Seleccion de Horarios</th>
	<td>Asesor:</td>
	<td class='pair'><select name='asesor' required><option value=''>Seleccionar...</option><? listasesores('x',1,4,0); ?></select></td>
	<td class='total'><input type='submit' name='asignar' value='Asignar'></td>
</tr>

</table>
<style type="text/css">
	table.tableizer-table {
	border: 1px solid #CCC; font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
} 
.tableizer-table td {
	padding: 4px;
	margin: 3px;
	border: 1px solid #ccc;
}
.tableizer-table th {
	background-color: #104E8B; 
	color: #FFF;
	font-weight: bold;
}
</style><table class="tableizer-table" width="100%">
<tr class="tableizer-firstrow"><th>ID</th><th>Esquema</th><th>Asesor</th><th>Lunes</th><th>Martes</th><th>Miércoles</th><th>Jueves</th><th>Viernes</th><th>Sábado</th><th>Domingo</th></tr>
 <tr><td>1</td><td>8</td><td><? $query="SELECT b.`N Corto` as 'asesor' FROM horariosDic a, Asesores b WHERE a.`asesor sc`=b.id AND a.id='$i'"; if(mysql_result(mysql_query($query),0,'asesor')==NULL){ echo "<input type='radio' name='h' value='$i'>"; $i++;}else{echo mysql_result(mysql_query($query),0,'asesor'); $i++;}?></td><td>00:00 - 07:00</td><td>00:00 - 07:00</td><td>00:00 - 07:00</td><td>00:00 - 07:00</td><td>00:00 - 07:00</td><td>00:00 - 07:00</td><td>Descanso</td></tr>
 <tr><td>2</td><td>8</td><td><? $query="SELECT b.`N Corto` as 'asesor' FROM horariosDic a, Asesores b WHERE a.`asesor sc`=b.id AND a.id='$i'"; if(mysql_result(mysql_query($query),0,'asesor')==NULL){ echo "<input type='radio' name='h' value='$i'>"; $i++;}else{echo mysql_result(mysql_query($query),0,'asesor'); $i++;}?></td><td>00:00 - 07:00</td><td>00:00 - 07:00</td><td>00:00 - 07:00</td><td>00:00 - 07:00</td><td>00:00 - 07:00</td><td>Descanso</td><td>00:00 - 07:00</td></tr>
 <tr><td>3</td><td>8</td><td><? $query="SELECT b.`N Corto` as 'asesor' FROM horariosDic a, Asesores b WHERE a.`asesor sc`=b.id AND a.id='$i'"; if(mysql_result(mysql_query($query),0,'asesor')==NULL){ echo "<input type='radio' name='h' value='$i'>"; $i++;}else{echo mysql_result(mysql_query($query),0,'asesor'); $i++;}?></td><td>07:00 - 15:00</td><td>07:00 - 15:00</td><td>07:00 - 15:00</td><td>07:00 - 15:00</td><td>07:00 - 15:00</td><td>07:00 - 15:00</td><td>Descanso</td></tr>
 <tr><td>4</td><td>8</td><td><? $query="SELECT b.`N Corto` as 'asesor' FROM horariosDic a, Asesores b WHERE a.`asesor sc`=b.id AND a.id='$i'"; if(mysql_result(mysql_query($query),0,'asesor')==NULL){ echo "<input type='radio' name='h' value='$i'>"; $i++;}else{echo mysql_result(mysql_query($query),0,'asesor'); $i++;}?></td><td>08:30 - 16:30</td><td>09:30 - 17:30</td><td>08:30 - 17:30</td><td>09:00 - 17:00</td><td>08:30 - 16:30</td><td>09:00 - 17:00</td><td>Descanso</td></tr>
 <tr><td>5</td><td>8</td><td><? $query="SELECT b.`N Corto` as 'asesor' FROM horariosDic a, Asesores b WHERE a.`asesor sc`=b.id AND a.id='$i'"; if(mysql_result(mysql_query($query),0,'asesor')==NULL){ echo "<input type='radio' name='h' value='$i'>"; $i++;}else{echo mysql_result(mysql_query($query),0,'asesor'); $i++;}?></td><td>10:00 - 18:00</td><td>09:30 - 17:30</td><td>09:30 - 17:30</td><td>09:00 - 17:00</td><td>09:30 - 17:30</td><td>Descanso</td><td>07:00 - 15:00</td></tr>
 <tr><td>6</td><td>8</td><td><? $query="SELECT b.`N Corto` as 'asesor' FROM horariosDic a, Asesores b WHERE a.`asesor sc`=b.id AND a.id='$i'"; if(mysql_result(mysql_query($query),0,'asesor')==NULL){ echo "<input type='radio' name='h' value='$i'>"; $i++;}else{echo mysql_result(mysql_query($query),0,'asesor'); $i++;}?></td><td>10:00 - 18:00</td><td>10:00 - 18:00</td><td>10:00 - 18:00</td><td>10:00 - 18:00</td><td>09:30 - 17:30</td><td>Descanso</td><td>08:00 - 16:00</td></tr>
 <tr><td>7</td><td>8</td><td><? $query="SELECT b.`N Corto` as 'asesor' FROM horariosDic a, Asesores b WHERE a.`asesor sc`=b.id AND a.id='$i'"; if(mysql_result(mysql_query($query),0,'asesor')==NULL){ echo "<input type='radio' name='h' value='$i'>"; $i++;}else{echo mysql_result(mysql_query($query),0,'asesor'); $i++;}?></td><td>10:00 - 18:00</td><td>10:00 - 18:00</td><td>10:00 - 18:00</td><td>10:00 - 18:00</td><td>09:30 - 17:30</td><td>Descanso</td><td>10:00 - 18:00</td></tr>
 <tr><td>8</td><td>8</td><td><? $query="SELECT b.`N Corto` as 'asesor' FROM horariosDic a, Asesores b WHERE a.`asesor sc`=b.id AND a.id='$i'"; if(mysql_result(mysql_query($query),0,'asesor')==NULL){ echo "<input type='radio' name='h' value='$i'>"; $i++;}else{echo mysql_result(mysql_query($query),0,'asesor'); $i++;}?></td><td>10:00 - 18:00</td><td>10:00 - 18:00</td><td>10:00 - 18:00</td><td>10:00 - 18:00</td><td>09:30 - 17:30</td><td>10:00 - 18:00</td><td>Descanso</td></tr>
 <tr><td>9</td><td>8</td><td><? $query="SELECT b.`N Corto` as 'asesor' FROM horariosDic a, Asesores b WHERE a.`asesor sc`=b.id AND a.id='$i'"; if(mysql_result(mysql_query($query),0,'asesor')==NULL){ echo "<input type='radio' name='h' value='$i'>"; $i++;}else{echo mysql_result(mysql_query($query),0,'asesor'); $i++;}?></td><td>17:00 - 00:00</td><td>17:00 - 00:00</td><td>17:00 - 00:00</td><td>15:00 - 22:30</td><td>15:00 - 22:30</td><td>10:00 - 18:00</td><td>Descanso</td></tr>
 <tr><td>10</td><td>8</td><td><? $query="SELECT b.`N Corto` as 'asesor' FROM horariosDic a, Asesores b WHERE a.`asesor sc`=b.id AND a.id='$i'"; if(mysql_result(mysql_query($query),0,'asesor')==NULL){ echo "<input type='radio' name='h' value='$i'>"; $i++;}else{echo mysql_result(mysql_query($query),0,'asesor'); $i++;}?></td><td>Descanso</td><td>11:00 - 19:00</td><td>10:30 - 18:30</td><td>10:30 - 18:30</td><td>10:30 - 18:30</td><td>13:00 - 21:00</td><td>13:00 - 21:00</td></tr>
 <tr><td>11</td><td>8</td><td><? $query="SELECT b.`N Corto` as 'asesor' FROM horariosDic a, Asesores b WHERE a.`asesor sc`=b.id AND a.id='$i'"; if(mysql_result(mysql_query($query),0,'asesor')==NULL){ echo "<input type='radio' name='h' value='$i'>"; $i++;}else{echo mysql_result(mysql_query($query),0,'asesor'); $i++;}?></td><td>12:30 - 20:30</td><td>12:30 - 20:30</td><td>10:30 - 18:30</td><td>13:00 - 21:00</td><td>10:30 - 18:30</td><td>Descanso</td><td>14:30 - 22:00</td></tr>
 <tr><td>12</td><td>8</td><td><? $query="SELECT b.`N Corto` as 'asesor' FROM horariosDic a, Asesores b WHERE a.`asesor sc`=b.id AND a.id='$i'"; if(mysql_result(mysql_query($query),0,'asesor')==NULL){ echo "<input type='radio' name='h' value='$i'>"; $i++;}else{echo mysql_result(mysql_query($query),0,'asesor'); $i++;}?></td><td>12:30 - 20:30</td><td>12:30 - 20:30</td><td>16:00 - 23:30</td><td>16:00 - 23:30</td><td>16:00 - 23:30</td><td>Descanso</td><td>18:00 - 01:00</td></tr>
 <tr><td>13</td><td>8</td><td><? $query="SELECT b.`N Corto` as 'asesor' FROM horariosDic a, Asesores b WHERE a.`asesor sc`=b.id AND a.id='$i'"; if(mysql_result(mysql_query($query),0,'asesor')==NULL){ echo "<input type='radio' name='h' value='$i'>"; $i++;}else{echo mysql_result(mysql_query($query),0,'asesor'); $i++;}?></td><td>15:00 - 22:30</td><td>12:30 - 20:30</td><td>Descanso</td><td>16:00 - 23:30</td><td>16:00 - 23:30</td><td>13:00 - 21:00</td><td>10:00 - 18:00</td></tr>
 <tr><td>14</td><td>8</td><td><? $query="SELECT b.`N Corto` as 'asesor' FROM horariosDic a, Asesores b WHERE a.`asesor sc`=b.id AND a.id='$i'"; if(mysql_result(mysql_query($query),0,'asesor')==NULL){ echo "<input type='radio' name='h' value='$i'>"; $i++;}else{echo mysql_result(mysql_query($query),0,'asesor'); $i++;}?></td><td>17:00 - 00:00</td><td>Descanso</td><td>17:00 - 00:00</td><td>16:00 - 23:30</td><td>17:00 - 00:00</td><td>16:00 - 23:30</td><td>10:00 - 18:00</td></tr>
 <tr><td>15</td><td>8</td><td><? $query="SELECT b.`N Corto` as 'asesor' FROM horariosDic a, Asesores b WHERE a.`asesor sc`=b.id AND a.id='$i'"; if(mysql_result(mysql_query($query),0,'asesor')==NULL){ echo "<input type='radio' name='h' value='$i'>"; $i++;}else{echo mysql_result(mysql_query($query),0,'asesor'); $i++;}?></td><td>17:00 - 00:00</td><td>17:00 - 00:00</td><td>17:00 - 00:00</td><td>17:00 - 00:00</td><td>17:00 - 00:00</td><td>17:00 - 00:00</td><td>Descanso</td></tr>
 <tr><td>16</td><td>8</td><td><? $query="SELECT b.`N Corto` as 'asesor' FROM horariosDic a, Asesores b WHERE a.`asesor sc`=b.id AND a.id='$i'"; if(mysql_result(mysql_query($query),0,'asesor')==NULL){ echo "<input type='radio' name='h' value='$i'>"; $i++;}else{echo mysql_result(mysql_query($query),0,'asesor'); $i++;}?></td><td>17:00 - 00:00</td><td>17:00 - 00:00</td><td>17:00 - 00:00</td><td>17:00 - 00:00</td><td>17:00 - 00:00</td><td>18:00 - 01:00</td><td>Descanso</td></tr>
 <tr><td>17</td><td>8</td><td><? $query="SELECT b.`N Corto` as 'asesor' FROM horariosDic a, Asesores b WHERE a.`asesor sc`=b.id AND a.id='$i'"; if(mysql_result(mysql_query($query),0,'asesor')==NULL){ echo "<input type='radio' name='h' value='$i'>"; $i++;}else{echo mysql_result(mysql_query($query),0,'asesor'); $i++;}?></td><td>18:00 - 01:00</td><td>18:00 - 01:00</td><td>18:00 - 01:00</td><td>18:00 - 01:00</td><td>18:00 - 01:00</td><td>09:30 - 16:30</td><td>Descanso</td></tr>
 <tr><td>18</td><td>8</td><td><? $query="SELECT b.`N Corto` as 'asesor' FROM horariosDic a, Asesores b WHERE a.`asesor sc`=b.id AND a.id='$i'"; if(mysql_result(mysql_query($query),0,'asesor')==NULL){ echo "<input type='radio' name='h' value='$i'>"; $i++;}else{echo mysql_result(mysql_query($query),0,'asesor'); $i++;}?></td><td>16:00 - 23:30</td><td>16:00 - 23:30</td><td>16:00 - 23:30</td><td>16:00 - 23:30</td><td>16:00 - 23:30</td><td>11:00 - 17:00</td><td>Descanso</td></tr>
 <tr><td>19</td><td>6</td><td><? $query="SELECT b.`N Corto` as 'asesor' FROM horariosDic a, Asesores b WHERE a.`asesor sc`=b.id AND a.id='$i'"; if(mysql_result(mysql_query($query),0,'asesor')==NULL){ echo "<input type='radio' name='h' value='$i'>"; $i++;}else{echo mysql_result(mysql_query($query),0,'asesor'); $i++;}?></td><td>08:00 - 14:00</td><td>08:00 - 14:00</td><td>08:00 - 14:00</td><td>08:00 - 14:00</td><td>08:00 - 14:00</td><td>17:00 - 23:00</td><td>Descanso</td></tr>
 <tr><td>20</td><td>6</td><td><? $query="SELECT b.`N Corto` as 'asesor' FROM horariosDic a, Asesores b WHERE a.`asesor sc`=b.id AND a.id='$i'"; if(mysql_result(mysql_query($query),0,'asesor')==NULL){ echo "<input type='radio' name='h' value='$i'>"; $i++;}else{echo mysql_result(mysql_query($query),0,'asesor'); $i++;}?></td><td>12:00 - 17:00</td><td>17:00 - 23:00</td><td>12:30 - 18:30</td><td>11:30 - 17:30</td><td>Descanso</td><td>17:00 - 00:00</td><td>11:00 - 17:00</td></tr>
 <tr><td>21</td><td>6</td><td><? $query="SELECT b.`N Corto` as 'asesor' FROM horariosDic a, Asesores b WHERE a.`asesor sc`=b.id AND a.id='$i'"; if(mysql_result(mysql_query($query),0,'asesor')==NULL){ echo "<input type='radio' name='h' value='$i'>"; $i++;}else{echo mysql_result(mysql_query($query),0,'asesor'); $i++;}?></td><td>16:00 - 22:00</td><td>11:00 - 19:00</td><td>15:00 - 21:00</td><td>11:30 - 17:30</td><td>12:00 - 20:00</td><td>11:00 - 17:00</td><td>Descanso</td></tr>
 <tr><td>22</td><td>6</td><td><? $query="SELECT b.`N Corto` as 'asesor' FROM horariosDic a, Asesores b WHERE a.`asesor sc`=b.id AND a.id='$i'"; if(mysql_result(mysql_query($query),0,'asesor')==NULL){ echo "<input type='radio' name='h' value='$i'>"; $i++;}else{echo mysql_result(mysql_query($query),0,'asesor'); $i++;}?></td><td>16:00 - 22:00</td><td>17:00 - 23:00</td><td>16:00 - 22:00</td><td>15:00 - 21:00</td><td>14:00 - 20:00</td><td>11:00 - 17:00</td><td>Descanso</td></tr>
 <tr><td>23</td><td>6</td><td><? $query="SELECT b.`N Corto` as 'asesor' FROM horariosDic a, Asesores b WHERE a.`asesor sc`=b.id AND a.id='$i'"; if(mysql_result(mysql_query($query),0,'asesor')==NULL){ echo "<input type='radio' name='h' value='$i'>"; $i++;}else{echo mysql_result(mysql_query($query),0,'asesor'); $i++;}?></td><td>19:00 - 00:00</td><td>18:00 - 00:00</td><td>Descanso</td><td>14:00 - 21:00</td><td>14:30 - 20:30</td><td>11:00 - 17:00</td><td>17:00 - 23:00</td></tr>
 <tr><td>24</td><td>6</td><td><? $query="SELECT b.`N Corto` as 'asesor' FROM horariosDic a, Asesores b WHERE a.`asesor sc`=b.id AND a.id='$i'"; if(mysql_result(mysql_query($query),0,'asesor')==NULL){ echo "<input type='radio' name='h' value='$i'>"; $i++;}else{echo mysql_result(mysql_query($query),0,'asesor'); $i++;}?></td><td>10:00 - 16:00</td><td>10:00 - 16:00</td><td>Descanso</td><td>10:00 - 16:00</td><td>10:00 - 16:00</td><td>16:30 - 22:30</td><td>18:00 - 00:00</td></tr>
 <tr><td>25</td><td>4</td><td><? $query="SELECT b.`N Corto` as 'asesor' FROM horariosDic a, Asesores b WHERE a.`asesor sc`=b.id AND a.id='$i'"; if(mysql_result(mysql_query($query),0,'asesor')==NULL){ echo "<input type='radio' name='h' value='$i'>"; $i++;}else{echo mysql_result(mysql_query($query),0,'asesor'); $i++;}?></td><td>10:00 - 18:00</td><td>10:00 - 18:00</td><td>10:00 - 18:00</td><td>10:00 - 18:00</td><td>10:00 - 18:00</td><td>Descanso</td><td>14:00 - 21:30</td></tr>
 <tr><td>26</td><td>4</td><td><? $query="SELECT b.`N Corto` as 'asesor' FROM horariosDic a, Asesores b WHERE a.`asesor sc`=b.id AND a.id='$i'"; if(mysql_result(mysql_query($query),0,'asesor')==NULL){ echo "<input type='radio' name='h' value='$i'>"; $i++;}else{echo mysql_result(mysql_query($query),0,'asesor'); $i++;}?></td><td>18:00 - 22:00</td><td>17:00 - 21:00</td><td>17:00 - 21:00</td><td>17:00 - 21:00</td><td>17:00 - 21:00</td><td>17:00 - 21:00</td><td>Descanso</td></tr>
 <tr><td>27</td><td>4</td><td><? $query="SELECT b.`N Corto` as 'asesor' FROM horariosDic a, Asesores b WHERE a.`asesor sc`=b.id AND a.id='$i'"; if(mysql_result(mysql_query($query),0,'asesor')==NULL){ echo "<input type='radio' name='h' value='$i'>"; $i++;}else{echo mysql_result(mysql_query($query),0,'asesor'); $i++;}?></td><td>Descanso</td><td>18:00 - 22:00</td><td>18:00 - 22:00</td><td>18:00 - 22:00</td><td>18:00 - 22:00</td><td>17:00 - 21:00</td><td>10:00 - 14:00</td></tr>
</table>






</div></div>

<br><br><br>