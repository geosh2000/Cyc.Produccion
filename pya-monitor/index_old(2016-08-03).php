<?
session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="monitor_pya";
include("../connectDB.php");
include("../DBPcrcs.php");?>

<?
include("../common/scripts.php");
$menu_monitores="class='active'";
date_default_timezone_set('America/Bogota');
define('TIMEZONE', 'America/Bogota');
$cun_time = new DateTimeZone('America/Bogota');
$err_count=0;
$dateok=$_POST['start'];
$timeok=$_POST['time'];

if(isset($_POST['autoselform'])){
$autoselok='checked';
}

if($dateok==NULL){
$dateok=date('Y-m-d');
}
if($timeok==NULL){
$timeok=date('G:i:s');
}

$date=date('Y-m-d',strtotime($dateok));
$datey=date('Y-m-d',strtotime($dateok.'-1 days'));
$datet=date('Y-m-d',strtotime($dateok.'+1 days'));
$Pstart=$date;
$showDeps=NULL;
$showHour=1;
if(intval(date('i',strtotime($timeok)))>=30){$pickhour="".date('H',strtotime($timeok)).":30:00";}else{$pickhour="".date('H',strtotime($timeok)).":00:00";}

$hour=date('G',strtotime($timeok));

if(date('i',strtotime($timeok))>=30){ $minuto=.5; }else{ $minuto=0;}

$horanow=$hour+$minuto;

$hora=array(
	0 => $horanow-1,
	1 => $horanow-0.5,
	2 => $horanow,
	3 => $horanow+0.5,
	4 => $horanow+1,
	);
if(date('I',strtotime($timeok))==0){
	$hora2=array(	
		0 => $horanow-2,
		1 => $horanow-1.5,
		2 => $horanow-1,
		3 => $horanow-0.5,
		4 => $horanow,
		);
}else{ $hora2=hora;}

foreach($hora as $keyHora => $time){
	if(intval($time)!=$time){$min="30";}else{$min="00";}
	if($time<0){$hr=intval(24+$time); $dia[$keyHora]=$datey;}elseif(intval($time)>23.5){$hr=24-intval($time); $dia[$keyHora]=$datet;}else{$hr=intval($time); $dia[$keyHora]=$date;}
	if($hr<10){$hr="0$hr";}
	$hora[$keyHora]="$hr:$min:00";
}
foreach($hora2 as $keyHora1 => $time1){
	if(intval($time1)!=$time1){$min="30";}else{$min="00";}
	if($time1<0){$hr1=intval(24+$time1); $dia2[$keyHora1]=$datey;}elseif(intval($time1)>23.5){$hr1=24-intval($time1); $dia2[$keyHora1]=$datet;}else{$h1r=intval($time1); $dia2[$keyHora1]=$date;}
	if($hr1<10){$hr1="0$hr";}
	$hora2[$keyHora1]="$hr1:$min:00";
}


$query="SELECT MAX(`Last_Update`) as 'update' FROM Sesiones";
$lastupdate=mysql_result(mysql_query($query),0,'update');
//echo "Last Update: $lastupdate<br>".mysql_error();

$timetoupdate='60000';


?>

  
  
  

  <script>
  $(function() {
     $(  '#0, sh:gt(0):lt(5000)' ).tooltip({

        track: true,
        show: {
            effect: "slideDown",
            delay: 250
        }
    });
  });
  </script>
<script>
var flag_reload=true;
function reload() {
	auto=document.getElementById('autosel').value
	if(document.forms["SelctDays"].autoselform.checked){

	document.forms["auto"].autoselform.checked=true;
	document.forms["auto"].submit();
	}else{

	document.forms["Noauto"].submit();
	
	window.location.reload();
	}

}
</script>
<script>

var total =<? echo $timetoupdate; ?>;
var myVar = setInterval(function(){ myTimer() }, 1000);

function myTimer() {
    if(flag_reload){
        if(total==0){reload();}
        total= total-1000;
        document.getElementById("demo").innerHTML = "   //   Reload in " + total/1000 + " sec.";
    }
}
</script>

<script>
function updateStatus(a,b,c,d) {
var str=a;
var id=b;
var div=c;
var hid=d;

if(str==3 || str==8){
	var caso = prompt("Please enter the case number", "");
    if (caso == null){return;}
    if (caso == "") {
        
        alert("You must enter a valid case number for this exception. No changes applied");
        return;
    }else{
    caso=caso;
    
    }
}

	
    if (str == "") {
        document.getElementById(div).innerHTML = "";
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
                document.getElementById(div).innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET","exceptions.php?excep="+str+"&asesor="+id+"&h="+hid+"&caso1="+caso,true);
        xmlhttp.send();
       
        
        
    }
}

function test(str){
alert('text'+str);
}
</script>

<style>
    .ui-tooltip {
    width: 120px;
    height: auto;
    padding: 10px 20px;
    color: black;
    border-radius: 20px;
    font: bold 14px "Helvetica Neue", Sans-Serif;
    text-align: center;
    box-shadow: 0 0 7px black;
  }
</style>


<?

include("../common/menu.php"); ?>
<table class='t2' width='100%'>
	<tr class='title'>
		<th rowspan=2 width='200px'><? include("../common/datepicker.php"); ?></th>
		<th>Monitor de PyA (<? echo date('d M Y', strtotime($dateok)); ?>)</th>
	</tr>
	<tr class='title'>
		<th>Ultima Actualizacion: <? echo date('d-m-Y  H:i:s', strtotime($lastupdate)); ?><l id='demo'></l></th>
	</tr>
</table>
<br><br>
<? include("comidas.php"); ?>
<br><br>

<table class='t2' width='100%'>
	<tr class='title'>
		<th width='10%'>PCRC</th>
		<th width='15%' colspan=2><? echo $hora[0]; ?></th>
		<th width='15%' colspan=2><? echo $hora[1]; ?></th>
		<th width='15%' colspan=2><? echo $hora[2]; ?></th>
		<th width='15%' colspan=2><? echo $hora[3]; ?></th>
		<th width='15%' colspan=2><? echo $hora[4]; ?></th>
	</tr>

		<?  $iddiv=0;
			foreach($pcrcs_departamento as $key => $depto){
			
				$depid=$pcrcs_id[$key];
				foreach($hora as $hk => $htime){
					$query="SELECT Asesores.`N Corto`, `Historial Programacion`.id as 'hid', Asesores.id FROM `Historial Programacion` LEFT JOIN `Asesores` ON `Historial Programacion`.asesor=`Asesores`.id WHERE `Historial Programacion`.Fecha='$dia[$hk]' AND `Asesores`.`id Departamento`='$depid' AND `Asesores`.`Activo`=1 AND `Historial Programacion`.`jornada start`='$htime' AND (`Historial Programacion`.`jornada start`!='00:00:00' OR `Historial Programacion`.`jornada end`!='00:00:00') ORDER BY `Asesores`.`N Corto`";
					$result=mysql_query($query);
					$num[$hk]=mysql_numrows($result);
					$i=0;
					while($i<$num[$hk]){
						$flag_aus=0;
						$a[$key][$hk][$i]=mysql_result($result,$i,'N Corto');
						$hid[$key][$hk][$i]=mysql_result($result,$i,'hid');
						$a_id=mysql_result($result,$i,'id');
						$id[$key][$hk][$i]=$a_id;
						
						
						
						$query="SELECT MIN(Hora) as 'Hora' FROM Sesiones WHERE asesor='$a_id' AND Fecha='$dia2[$hk]' AND Hora > '04:00:00'";
						
						$result2=mysql_query($query);
						$h[$key][$hk][$i]=mysql_result($result2,0,'Hora');
                        $hora_mx[$key][$hk][$i]=new DateTime($dia2[$hk].' '.$h[$key][$hk][$i].' America/Mexico_city');
                        $hora_mx[$key][$hk][$i]->setTimezone($cun_time);
                        $hora_ok[$key][$hk][$i]=$hora_mx[$key][$hk][$i]->format('H:i:s');
                        $query="SELECT * FROM Ausentismos a, `Tipos Ausentismos` b WHERE a.tipo_ausentismo=b.id AND a.asesor='$a_id' AND a.Inicio<='$dia2[$hk]' AND a.Fin>='$dia2[$hk]'";
						$result3=mysql_query($query);
						$num3=mysql_numrows($result3);
						if($num3>0){ $h[$key][$hk][$i]=mysql_result($result3,0,'Ausentismo'); $flag_aus=1;}
						$query3="SELECT * FROM PyA_Exceptions a, `Tipos Excepciones` b WHERE a.tipo=b.exc_type_id AND a.horario_id='".$hid[$key][$hk][$i]."'";
						$result3=mysql_query($query3);
						$num3=mysql_numrows($result3);
						if($num3>0){$exc[$key][$hk][$i]="<br>".mysql_result($result3,0,'Excepcion');}

						if($h[$key][$hk][$i]!=NULL && $flag_aus==0){
						  $h[$key][$hk][$i]=$hora_ok[$key][$hk][$i];


						} 
						if($h[$key][$hk][$i]==NULL && strtotime($htime)<strtotime('now')){
							$style[$key][$hk][$i]="class='flashred'";
						}
						if(strtotime($h[$key][$hk][$i])>strtotime($htime.'+10 minutes'))
						
						{
							$style[$key][$hk][$i]="class='flashred'";
						}elseif(strtotime($h[$key][$hk][$i])>=strtotime($htime.'+1 minutes') )
						
						{	
							$style[$key][$hk][$i]="class='orange'";
						}elseif($h[$key][$hk][$i]!=NULL){
							$style[$key][$hk][$i]="class='green'";
						}
						
						if(intval(date('G',strtotime($h[$key][$hk][$i])))>=23 || date('H:i:s',strtotime($htime))=='00:00:00'){
							$style[$key][$hk][$i]="class='green'";
						}
						
						if($exc[$key][$hk][$i]!=""){$style[$key][$hk][$i]="class='orange'";}
						if($flag_aus==1){$style[$key][$hk][$i]="class='yellow'";}
					$i++;
					}
					$i=0;
					
				}

				while($i<max($num)){
				        $index=0;

						if($i % 2 == 0){$class="pair title='$depto'";}else{$class="odd";}
							echo "\t<tr class=$class>\n";
						if($i==0){ echo "\t\t<th valign='middle' class='title' width='10%' rowspan='".max($num)."'>$depto</th>"; }
                        while($index<5){
						echo "\t\t<td>".$a[$key][$index][$i]."</td>\n
							\t\t<td ".$style[$key][$index][$i].">";
                            $qinfo="SELECT * FROM PyA_Exceptions a, userDB b WHERE a.changed_by=b.userid AND horario_id='".$hid[$key][$index][$i]."' AND asesor='".$id[$key][$index][$i]."'";
                            $rinfo=mysql_query($qinfo);
                            if(mysql_numrows($rinfo)>0){
                                $notes=mysql_result($rinfo,0,'Nota');
                                $tool_case=mysql_result($rinfo,0,'caso');
                                if(strlen($notes)>0){$notes=" //  Nota: $notes";}
                                if($tool_case>0){$tool_case="//  Caso: $tool_case";}else{$tool_case="";}
                                $title="Ajuste aplicado ".date('d/m/y',strtotime(mysql_result($rinfo,0,'Last Update')))." por ".mysql_result($rinfo,0,'username')." $tool_case  $notes";}else{$title="";
                            }
                            if($a[$key][$index][$i]!=NULL){ echo"<sh style='width:100%' id='$iddiv' ncorto='".$a[$key][$index][$i]."' horaid='".$hid[$key][$index][$i]."' nameid='".$id[$key][$index][$i]."' fecha='".date('Y-m-d')."' ".$style[$key][$index][$i]." title='$title'>".$h[$key][$index][$i].$exc[$key][$index][$i]."<z  id='a$iddiv'></z></sh>";$iddiv++;}
                            echo "</td>\n";
                        $index++;

						}
						echo "\t</tr>\n";
					$i++;
				}
			}
		?>

</table></z>
<form name='Noauto' method='post' action='<?php $_SERVER['PHP_SELF']; ?>'><input type='text' name='start' value='<? echo $dateok; ?>' hidden><input type='text' name='start' value='<? echo $timeok; ?>' hidden><input type='checkbox' name='autoselform' id='autoselform' hidden></form>
<?
if($_SESSION['monitor_pya_exceptions']==1){include("../common/add_exception.php");}     
$dateok=date('Y-m-d');
$timeok=date('G:i:s');
?>
<form name='auto' method='post' action='<?php $_SERVER['PHP_SELF']; ?>'><input type='text' name='start' value='<? echo $dateok; ?>' hidden><input type='text' name='start' value='<? echo $timeok; ?>' hidden><input type='checkbox' name='autoselform' id='autoselform' <? echo $autoselok; ?> hidden></form>