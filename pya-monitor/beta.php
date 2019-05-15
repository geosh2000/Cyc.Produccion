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

$query="SELECT MAX(`Last Update`) as 'update' FROM Sesiones";
$lastupdate=mysql_result(mysql_query($query),0,'update');

$timetoupdate='60000';

function printOptions($id,$div,$hid){
	if($_SESSION['monitor_pya_exceptions']==1){
   	echo "\t\t<form><x id='dial-$div'><select name='excep' onchange='updateStatus(this.value,$id,$div,$hid)'><option value='0'>Selecciona...</option>";
		$query="SELECT * FROM `Tipos Excepciones` ORDER BY Excepcion";
		$result=mysql_query($query);
		$num=mysql_numrows($result);
		$i=0;
		while($i<$num){
			echo "<option value='".mysql_result($result,$i,'exc_type_id')."'>".mysql_result($result,$i,'Excepcion')."</option>";
		$i++;
		}
		
	echo "</select></form></x>\n";}
}

?>

  

  
 
  <script>

  $(function() {
   <? /*
    $( "x" ).dialog({
      autoOpen: false,
      show: {
        effect: "blind",
        duration: 1000
      },
      hide: {
        effect: "explode",
        duration: 1000
      }
    });


    $( ".opener" ).click(function() {
            var dialog = "#dial-" + this.id;

            $( dialog ).dialog( "open" );
    }); */?>
    var dialog, form,

      // From http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#e-mail-state-%28type=email%29
      emailRegex = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/,
      name = $( "#name" ),
      email = $( "#email" ),
      password = $( "#password" ),
      allFields = $( [] ).add( name ).add( email ).add( password ),
      tips = $( ".validateTips" );

    function updateTips( t ) {
      tips
        .text( t )
        .addClass( "ui-state-highlight" );
      setTimeout(function() {
        tips.removeClass( "ui-state-highlight", 1500 );
      }, 500 );
    }

    function checkLength( o, n, min, max ) {
      if ( o.val().length > max || o.val().length < min ) {
        o.addClass( "ui-state-error" );
        updateTips( "Length of " + n + " must be between " +
          min + " and " + max + "." );
        return false;
      } else {
        return true;
      }
    }

    function checkRegexp( o, regexp, n ) {
      if ( !( regexp.test( o.val() ) ) ) {
        o.addClass( "ui-state-error" );
        updateTips( n );
        return false;
      } else {
        return true;
      }
    }

    function addUser() {
      var valid = true;
      allFields.removeClass( "ui-state-error" );

      valid = valid && checkLength( name, "username", 3, 16 );
      valid = valid && checkLength( email, "email", 6, 80 );
      valid = valid && checkLength( password, "password", 5, 16 );

      valid = valid && checkRegexp( name, /^[a-z]([0-9a-z_\s])+$/i, "Username may consist of a-z, 0-9, underscores, spaces and must begin with a letter." );
      valid = valid && checkRegexp( email, emailRegex, "eg. ui@jquery.com" );
      valid = valid && checkRegexp( password, /^([0-9a-zA-Z])+$/, "Password field only allow : a-z 0-9" );

      if ( valid ) {
        $( "#users tbody" ).append( "<tr>" +
          "<td>" + name.val() + "</td>" +
          "<td>" + email.val() + "</td>" +
          "<td>" + password.val() + "</td>" +
        "</tr>" );
        dialog.dialog( "close" );
      }
      return valid;
    }

    dialog = $( formdial ).dialog({
      autoOpen: false,
      height: 300,
      width: 350,
      modal: true,
      buttons: {
        "Create an account": addUser,
        Cancel: function() {
          dialog.dialog( "close" );
        }
      },
      close: function() {
        form[ 0 ].reset();
        allFields.removeClass( "ui-state-error" );
      }
    });

    form = dialog.find( "form" ).on( "submit", function( event ) {
      event.preventDefault();
      addUser();
    });

    $( ".opener" ).button().on( "click", function() {
        var formdial = "#dial-" + this.id;
      dialog.dialog( "open" );
    });
  });
  </script>
<script>
setTimeout(function() {
	auto=document.getElementById('autosel').value
	if(document.forms["SelctDays"].autoselform.checked){
	
	document.forms["auto"].autoselform.checked=true;
	document.forms["auto"].submit();
	}else{
	
	document.forms["Noauto"].submit();
	
	window.location.reload();
	}
    
}, <? echo $timetoupdate; ?>);
</script>
<script>

var total =<? echo $timetoupdate; ?>;
var myVar = setInterval(function(){ myTimer() }, 1000);

function myTimer() {
   total= total-1000;
    document.getElementById("demo").innerHTML = "   //   Reload in " + total/1000 + " sec.";
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


<? include("../common/menu.php"); ?>
<table class='t2' width='100%'>
	<tr class='title'>
		<th rowspan=2 width='200px'><? include("../common/datepicker.php"); ?></th>
		<th>Monitor de PyA (<? echo date('d M Y', strtotime($dateok)); ?>)</th>
	</tr>
	<tr class='title'>
		<th>Last Update: <? echo date('d-m-Y  H:i:s', strtotime($lastupdate)); ?><l id='demo'></l></th>
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
	
		<?
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
						
						
						
						$query="SELECT MIN(Hora) as 'Hora' FROM Sesiones WHERE asesor='$a_id' AND Fecha='$dia2[$hk]' AND Tipo='in' AND Hora > '04:00:00'";
						
						$result2=mysql_query($query);
						$h[$key][$hk][$i]=mysql_result($result2,0,'Hora');
						$query="SELECT * FROM Ausentismos a, `Tipos Ausentismos` b WHERE a.tipo_ausentismo=b.id AND a.asesor='$a_id' AND a.Inicio<='$dia2[$hk]' AND a.Fin>='$dia2[$hk]'";
						$result3=mysql_query($query);
						$num3=mysql_numrows($result3);
						if($num3>0){ $h[$key][$hk][$i]=mysql_result($result3,0,'Ausentismo'); $flag_aus=1;}
						$query3="SELECT * FROM PyA_Exceptions a, `Tipos Excepciones` b WHERE a.tipo=b.exc_type_id AND a.horario_id='".$hid[$key][$hk][$i]."'";
						$result3=mysql_query($query3);
						$num3=mysql_numrows($result3);
						if($num3>0){$exc[$key][$hk][$i]="<br>".mysql_result($result3,0,'Excepcion');}
						
						if(date('I',strtotime($h[$key][$hk][$i]))==0 && $h[$key][$hk][$i]!=NULL && $flag_aus==0){
							$tmp=date(':i:s',strtotime($h[$key][$hk][$i]));
							$tmpH=date('G',strtotime($h[$key][$hk][$i]))+1;
							
							if($tmpH>=24){$tmpH=24-$tmpH;}
							if($tmpH<10){$tmpH="0$tmpH";}
							$h[$key][$hk][$i]="$tmpH"."$tmp";
							
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
						if($i % 2 == 0){$class="pair";}else{$class="odd";}
							echo "\t<tr class=$class>\n";
						if($i==0){ echo "\t\t<th valign='middle' class='title' width='10%' rowspan='".max($num)."'>$depto</th>"; }
						$iddiv++;
						echo "\t\t<td>".$a[$key][0][$i]."</td>\n
							\t\t<td ".$style[$key][0][$i]."><z class='opener' id='$iddiv'>".$h[$key][0][$i].$exc[$key][0][$i]."</z>";
						printOptions($id[$key][0][$i],$iddiv,$hid[$key][0][$i]);
						$iddiv++;
						echo "</td>\n
							\t\t<td>".$a[$key][1][$i]."</td>\n
							\t\t<td ".$style[$key][1][$i]."><z class='opener' id='$iddiv'>".$h[$key][1][$i].$exc[$key][1][$i]."</z>";
						printOptions($id[$key][1][$i],$iddiv,$hid[$key][1][$i]);
						$iddiv++;
						echo "</td>\n
							\t\t<td>".$a[$key][2][$i]."</td>\n
							\t\t<td ".$style[$key][2][$i]."><z class='opener' id='$iddiv'>".$h[$key][2][$i].$exc[$key][2][$i]."</z>";
						printOptions($id[$key][2][$i],$iddiv,$hid[$key][2][$i]);
						$iddiv++;
						echo "</td>\n
							\t\t<td>".$a[$key][3][$i]."</td>\n
							\t\t<td ".$style[$key][3][$i]."><z class='opener' id='$iddiv'>".$h[$key][3][$i].$exc[$key][3][$i]."</z>";
						printOptions($id[$key][3][$i],$iddiv,$hid[$key][3][$i]);
						$iddiv++;
						echo "</td>\n
							\t\t<td>".$a[$key][4][$i]."</td>\n
							\t\t<td ".$style[$key][4][$i]."><z class='opener' id='$iddiv'>".$h[$key][4][$i].$exc[$key][4][$i]."</z>";
						printOptions($id[$key][4][$i],$iddiv,$hid[$key][4][$i]);
						echo "</td>\n
							\t</tr>\n";
					$i++;
				}
			}
		?>

</table></z>
<form name='Noauto' method='post' action='<?php $_SERVER['PHP_SELF']; ?>'><input type='text' name='start' value='<? echo $dateok; ?>' hidden><input type='text' name='start' value='<? echo $timeok; ?>' hidden><input type='checkbox' name='autoselform' id='autoselform' hidden></form>
<?
$dateok=date('Y-m-d');
$timeok=date('G:i:s');
?>
<form name='auto' method='post' action='<?php $_SERVER['PHP_SELF']; ?>'><input type='text' name='start' value='<? echo $dateok; ?>' hidden><input type='text' name='start' value='<? echo $timeok; ?>' hidden><input type='checkbox' name='autoselform' id='autoselform' <? echo $autoselok; ?> hidden></form>