<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];


if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="sup_asing";

//default timezone
date_default_timezone_set('America/Bogota');

include("../connectDB.php");
include("../common/list_asesores.php");
include("../common/scripts.php");

//GET VARIABLES
$dept=$_POST['dept'];
$asesor=$_POST['asesor'];
if(!isset($_POST['date'])){$fecha=date('Y-m-d');}else{$fecha=date('Y-m-d',strtotime($_POST['date']));}
$sup=$_POST['super'];
$i=0;
while($i<200){
	if(isset($_POST['asesor'.$i])){$asesor[$i]=$_POST['asesor'.$i]; $check[$i]="checked";}else{$asesor[$i]=$_POST['asesor'.$i]; $check[$i]="";}
$i++;
}

//QUERY Asesores
$query="SELECT * FROM Asesores WHERE `id Departamento`='$dept' AND Activo=1 ORDER BY `N Corto`";
$result=mysql_query($query);
$num=mysql_numrows($result);
if($num % 5 == 0){$filas=$num/5;}else{$filas=$num/5+1;}



include("../common/menu.php");
?>
<table width='100%' class='t2'><form action="<?php $_SERVER['PHP_SELF'];?>" method='post'>
    <tr class='title'>
        <td colspan=100>Asignacion de Supervisores</td>

    </tr>
    <tr class='subtitle'>
        <td>Departamento</td>
        <td>Fecha de Inicio</td>
        <td>Supervisor</td>
        <td rowspan=2 class='total'><input type="submit" value='asignar' name='asignar' /></td>
    </tr>
    <tr class='pair'>
        <td><select name="dept" id="dept" onchange="this.form.submit();" required><?php list_departamentos($dept); ?></select></td>
        <td><input type="date" name='date' value='<?php echo $fecha;?>' required></td>
        <td><select name="super" id="super" required><?php listAsesores('super',1,12,0); ?></select></td>
    </tr>
</table>
<table width='100%' class='t2'>
	<tr class='title'>
		<th colspan=100>Asesores</th>
	</tr>
      <?php
        $i=1;
        $z=0;
        while($i<=$filas){
            if($i % 2 == 0){$class='pair';}else{$class='odd';}
             echo "\t<tr class='$class'>\n";
             $x=1;
             while($x<=5){

                  echo "\t\t<td  style='text-alingn:left'>";
                  if(mysql_result($result,$z,'id')!=NULL){
                    echo "<input type='checkbox' name='asesor$z' value='".mysql_result($result,$z,'id')."' $check[$z]>".mysql_result($result,$z,'N Corto');
                  }
                  echo "</td>\n";
             $x++;
             $z++;
             }
             echo "\t</tr>\n";
        $i++;
        }
      ?>
</form></table>
<br>
<?
//Update Supers
if(isset($_POST['asignar'])){
$i=0;
while($i<200){
	if(isset($_POST['asesor'.$i])){
		$query_ud="SELECT * FROM Supervisores WHERE Fecha='$fecha' AND asesor='$asesor[$i]'";
		$result_ud=mysql_query($query_ud);
		$num_ud=mysql_numrows($result_ud);
		
		if($num_ud>0){
			$q_id=mysql_result($result,0,'rel_sup_id');
			$query_ud="UPDATE Supervisores SET Fecha='$fecha' AND asesor='$asesor[$i]' AND supervisor='$sup' AND user='".$_SESSION['id']."' AND pcrc='$dept' WHERE rel_sup_id='$q_id' ";
		}else{
			$query_ud="INSERT INTO Supervisores (Fecha,asesor,supervisor,user,pcrc) VALUES('$fecha','$asesor[$i]','$sup','".$_SESSION['id']."','$dept')";
		}
		mysql_query($query_ud);
		if(mysql_errno()){
		    echo mysql_result($result,$asesor[$i],'N Corto')." // MySQL error ".mysql_errno().": "
		         .mysql_error()."\n<br>When executing <br>\n$query\n<br><br>";
		}else{ echo "OK<br>";}
		
		
	}
$i++;
}
}
?>
<br>
<table width='100%' class='t2'>
	<tr class='title'>
		<th colspan=100>Supervisores Asignados</th>
	</tr>
	<tr class='subtitle'>
	<?
		$query="SELECT DISTINCT `N Corto` FROM Supervisores a, Asesores b WHERE a.supervisor=b.id AND Fecha='$fecha' AND pcrc=$dept"; 
		$result=mysql_query($query);
		$num=mysql_numrows($result);
		$i=0;
		while($i<$num){
			$supervisor[$i]=mysql_result($result,$i,'N corto');
			echo "\t\t<td>$supervisor[$i]</td>\n";
		$i++;
		}
		
		
	
	?>
	</tr>

</table>