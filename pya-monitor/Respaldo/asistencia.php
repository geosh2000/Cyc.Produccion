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
$dateok=$_GET['fecha'];
$skill=$_GET['skill'];
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
     $(  '.log, .block, .blockhora' ).tooltip({
		content: function() {
		        return $(this).attr('title');
		   },
        track: true,
        show: {
            effect: "slideDown",
            delay: 250
        }
    });
    
    $('#date_search').datepicker();
    
    $('#content-horarios').animate({
        scrollTop: ($("#hora<?php
        				if(date('Y-m-d')==date('Y-m-d',strtotime($date))){ 
	        				if(intval(date('i'))>=30){$sub="5";}else{$sub="0";} 
	        				echo (date('H')-1)."_$sub";
	        				//echo "18_0";
						}else{
							echo "0_0";
						}
        			?>").offset().top) - 200
    }, 20);
    
    //$('#console').html('test1');
    
    function resizeHorarios(){
    	search=$('#searchtable').outerHeight(true);
    	retardos=$('#retardostable').outerHeight(true);
    	window=$( window ).height();
    	newHeight=($( window ).height()-($('#retardostable').outerHeight(true)+$('#searchtable').outerHeight(true))+50);
    	$( '#content-horarios' ).css('height',newHeight);
    	//$('#console').html('NewW: '+newHeight+'<br>Window: '+$( window ).height()+'<br>Search: '+$('#searchtable').outerHeight(true)+'<br>Retardos: '+$('#retardostable').outerHeight(true)+'<br>Horarios:'+$( '#content-horarios' ).outerHeight(true));
    }
    
    
    resizeHorarios();
    
    $(document).resize(function(){
    	resizeHorarios();
    });
    
    
    //$('#console').html('Window: '+$( window ).height()+'<br>Search: '+$('#searchtable').height()+'<br>Retardos: '+$('#retardostable').height()+'<br>Horarios:'+$( '#content-horarios' ).height());
    
    

  });
  </script>
<script>
var flag_reload=true;
function reload() {
	//$('#seleccion').submit();
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
    width: 200px;
    height: auto;
    padding: 10px 20px;
    color: black;
    border-radius: 20px;
    font: bold 14px "Helvetica Neue", Sans-Serif;
    text-align: center;
    box-shadow: 0 0 7px black;
  }
  
  .none{
  	background= gray;
  }
  
  body{
  	zoom: 0.9
  }
</style>


<?

include("../common/menu.php"); ?>
<table id='searchtable' class='t2' style='width:800px; margin: auto'>
	<tr class='title'><form action='<?php $_SERVER['PHP_SELF']; ?>' method='POST' id='seleccion'>
		<th rowspan=2 style='text-align: center'><input type='text' value='<?php echo $date; ?>' id='date_search' name='fecha' style='width: 90px'></th><th rowspan=2><input type='submit' value='Consultar'></th>
		<th>Monitor de PyA (<? echo date('d M Y', strtotime($dateok)); ?>)</th>
	</tr></form>
	<tr class='title'>
		<th>Ultima Actualizacion: <? echo date('d-m-Y  H:i:s', strtotime($lastupdate)); ?><l id='demo'></l></th>
	</tr>
</table>

<?php

//Define DB and Functions
if(date('Y-m-d', strtotime($date))!=date('Y-m-d', strtotime('now'))){
	$function="";
	$db="t_Sesiones";
}else{
	$function="TD";
	$db="Sesiones";
}

$query="SELECT *
		FROM 
			HoraGroup_Table a 
		LEFT JOIN
			(
				SELECT 
					a.id as asesorid, b.id as horarioid, `N Corto`, Departamento, LogAsesor$function(b.Fecha,b.asesor,'in') as Login, LogAsesor$function(b.Fecha,b.asesor,'out') as Logout, 
					`jornada start`, `jornada end`, `comida start`, `comida end`, `extra1 start`, `extra1 end`,  `extra2 start`, `extra2 end`,
					d.Excepcion, c.caso as excep_caso, c.Nota as excep_nota, c.username as excep_aplicado, c.`Last Update` as excep_f_aplicado,
					Ausentismo, e.caso as aus_caso, e.Comments as aus_nota, e.`Last Update` as aus_f_aplicado, e.username as aus_aplicado
				FROM
					Asesores a
				LEFT JOIN
					`Historial Programacion` b ON a.id=b.asesor
				LEFT JOIN
					(SELECT * FROM PyA_Exceptions a LEFT JOIN userDB b ON a.changed_by=b.userid) c ON b.id=c.horario_id
				LEFT JOIN
					`Tipos Excepciones` d ON c.tipo=d.exc_type_id
				LEFT JOIN
					(SELECT * FROM Ausentismos a LEFT JOIN userDB b ON a.User=b.userid) e ON b.Fecha BETWEEN Inicio AND Fin AND b.asesor=e.asesor
				LEFT JOIN
					`Tipos Ausentismos` f ON tipo_ausentismo=f.id
				LEFT JOIN
					PCRCs g ON a.`id Departamento`=g.id
				WHERE
					Activo=1 AND Fecha='$date' AND `jornada start`!=`jornada end` AND
					`id Departamento` IN ($skill)
				ORDER BY
					Departamento, Nombre) pya
			ON 
				a.Hora_time BETWEEN `jornada start` AND IF(`jornada end`<'08:00:00',ADDTIME(`jornada end`,'24:00:00'),`jornada end`) 
			WHERE
			 `N Corto` IS NOT NULL";
$result=mysql_query($query);
if(mysql_error()){
	echo mysql_error()." ON QUERY<br>$query<br>";
}else{
	echo $query;
}
$num=mysql_numrows($result);
$numfields=mysql_num_fields($result);
for($i=0;$i<$num;$i++){
	for($x=0;$x<$numfields;$x++){
		if((mysql_field_name($result,$x)=='Login' || mysql_field_name($result,$x)=='Logout') && mysql_result($result,$i,$x)!=NULL){
			$time_tmp = new DateTime($date.' '.mysql_result($result,$i,$x).' America/Mexico_city');
			$time_tmp -> setTimezone($cun_time);
			$pya[mysql_result($result,$i,'Departamento')][mysql_result($result,$i,'N Corto')][mysql_field_name($result,$x)]=$time_tmp->format('H:i:s');
		}else{
			$pya[mysql_result($result,$i,'Departamento')][mysql_result($result,$i,'N Corto')][mysql_field_name($result,$x)]=mysql_result($result,$i,$x);
		}
	}
	//Display
		if(mysql_result($result,$i,'Ausentismo')==NULL){
			$block[mysql_result($result,$i,'Hora_int')][]=mysql_result($result,$i,'N Corto');
		}
}



foreach($pya as $departamento => $info){
	foreach($info as $asesor => $info2){
		$pya[$departamento][$asesor]['validado']="OK";
		
		
		
		//Retardos
		if($info2['Ausentismo']==NULL && date('Y-m-d',strtotime($date))<=date('Y-m-d')){
			if($info2['Login']==NULL){
				if(date('H:i:s')>date('H:i:s', strtotime($info2['jornada start'].' +1 minutes'))){
					if(date('H:i:s')>date('H:i:s', strtotime($info2['jornada start'].' +10 minutes'))){
						$pya[$departamento][$asesor]['Retardo_tipo']="B";
					}else{
						$pya[$departamento][$asesor]['Retardo_tipo']="A";
					}
				}	
			}else{
				if(date('H:i:s', strtotime($info2['Login']))>date('H:i:s', strtotime($info2['jornada start'].' +1 minutes'))){
					if(date('H:i:s', strtotime($info2['Login']))>date('H:i:s', strtotime($info2['jornada start'].' +10 minutes'))){
						$pya[$departamento][$asesor]['Retardo_tipo']="B";
					}else{
						$pya[$departamento][$asesor]['Retardo_tipo']="A";
					}
				}
			}
			
			if(isset($pya[$departamento][$asesor]['Retardo_tipo'])){
				//Excepcion
				if($info2['Excepcion']!=NULL){
					$excep="<br>".$info2['Excepcion'];
					$retardos[$pya[$departamento][$asesor]['Retardo_tipo']][$asesor]['Tit']="Excepcion Aplicada por:<br><a style='color: red'>".$info2['excep_aplicado']."</a><br>".$info2['excep_f_aplicado']."<br>caso: ".$info2['excep_caso']."<br>Nota: ".$info2['excep_nota'];
					if($info2['Excepcion']!="Retardo Notificado"){$retardos[$pya[$departamento][$asesor]['Retardo_tipo']][$asesor]['Excep']=1;}	
				}else{
					$excep="";
					
					
					
				}
				
				//Login
				if($info2['Login']!=NULL){
					$retardos[$pya[$departamento][$asesor]['Retardo_tipo']][$asesor]['Login']=1;	
				}
				
				$retardos[$pya[$departamento][$asesor]['Retardo_tipo']][$asesor]['Ret']="$asesor<br>(".$info2['Departamento'].")<br>js: ".$info2['jornada start']."<br>li: ".$info2['Login'].$excep;
				$retardos[$pya[$departamento][$asesor]['Retardo_tipo']][$asesor]['ncorto']=$asesor;
				$retardos[$pya[$departamento][$asesor]['Retardo_tipo']][$asesor]['fecha']=$date;
				$retardos[$pya[$departamento][$asesor]['Retardo_tipo']][$asesor]['asesorid']=$info2['asesorid'];
				$retardos[$pya[$departamento][$asesor]['Retardo_tipo']][$asesor]['horaid']=$info2['horarioid'];
			}
		}
	}
	unset($asesor,$info2);
}
unset($departamento,$info);
?>
<style>
	.block{
		display: inline-block; 
		width: 145px; 
		margin: 4px; 
		background: cyan;
		height: 20px;
	}
	.blockhora{
		display: inline-block; 
		width: 145px; 
		margin: 4px; 
		background: cyan;
		height: auto;
	}
	
	#container {
	  width: 600px;
	  margin: 0 auto;
	}
	.drop-shadow {
	  position: relative;
	  display: inline-block;
	  width: 20;
	  padding: 0;
	  padding-top: 5px;
	  margin: -8px 0px 0px;
	  background: #F7E414;
	  vertical-align: top;
	  text-align: center;
	  -webkit-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3) , 0 0 40px rgba(0, 0, 0, 0.1) inset;
	  -mox-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3) , 0 0 40px rgba(0, 0, 0, 0.1) inset;
	  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3) , 0 0 40px rgba(0, 0, 0, 0.1) inset;
	}
	.drop-shadow:before,
	.drop-shadow:after {
	  content: "";
	  position: absolute;
	  z-index: -2;
	}
	.drop-shadow p {
	  font-size: 16px;
	  font-weight: bold;
	}
	.lifted {
	  -moz-border-radius: 4px;
	  border-radius: 4px;
	}
	.lifted:before,
	.lifted:after {
	  bottom: 15px;
	  left: 10px;
	  width: 50%;
	  height: 20%;
	  max-width: 300px;
	  max-height: 100px;
	  -webkit-box-shadow: 0 15px 10px rgba(0, 0, 0, 0.7);
	  -mox-box-shadow: 0 15px 10px rgba(0, 0, 0, 0.7);
	  box-shadow: 0 15px 10px rgba(0, 0, 0, 0.7);
	  -webkit-transform: rotate(-3deg);
	  -moz-transform: rotate(-3deg);
	  -ms-transform: rotate(-3deg);
	  -o-transform: rotate(-3deg);
	  transform: rotate(-3deg);
	}
	.lifted:after {
	  right: 10px;
	  left: auto;
	  -webkit-transform: rotate(3deg);
	  -moz-transform: rotate(3deg);
	  -ms-transform: rotate(3deg);
	  -o-transform: rotate(3deg);
	  transform: rotate(3deg);
	}
	.curled {
	  border: 1px solid #efefef;
	  -moz-border-radius: 0 0 120px 120px / 0 0 6px 6px;
	  border-radius: 0 0 120px 120px / 0 0 6px 6px;
	}
	.curled:before,
	.curled:after {
	  bottom: 12px;
	  left: 10px;
	  width: 50%;
	  height: 55%;
	  max-width: 200px;
	  max-height: 100px;
	  -webkit-box-shadow: 0 8px 12px rgba(0, 0, 0, 0.5);
	  -mox-box-shadow: 0 8px 12px rgba(0, 0, 0, 0.5);
	  box-shadow: 0 8px 12px rgba(0, 0, 0, 0.5);
	  -webkit-transform: skew(-8deg) rotate(-3deg);
	  -moz-transform: skew(-8deg) rotate(-3deg);
	  -ms-transform: skew(-8deg) rotate(-3deg);
	  -o-transform: skew(-8deg) rotate(-3deg);
	  transform: skew(-8deg) rotate(-3deg);
	}
	.curled:after {
	  right: 10px;
	  left: auto;
	  -webkit-transform: skew(8deg) rotate(3deg);
	  -moz-transform: skew(8deg) rotate(3deg);
	  -ms-transform: skew(8deg) rotate(3deg);
	  -o-transform: skew(8deg) rotate(3deg);
	  transform: skew(8deg) rotate(3deg);
	}
	.perspective:before {
	  left: 80px;
	  bottom: 5px;
	  width: 50%;
	  height: 35%;
	  max-width: 200px;
	  max-height: 50px;
	  -webkit-box-shadow: -80px 0 8px rgba(0, 0, 0, 0.4);
	  -mox-box-shadow: -80px 0 8px rgba(0, 0, 0, 0.4);
	  box-shadow: -80px 0 8px rgba(0, 0, 0, 0.4);
	  -webkit-transform: skew(50deg);
	  -moz-transform: skew(50deg);
	  -ms-transform: skew(50deg);
	  -o-transform: skew(50deg);
	  transform: skew(50deg);
	  -webkit-transform-origin: 0 100%;
	  -moz-transform-origin: 0 100%;
	  -ms-transform-origin: 0 100%;
	  -o-transform-origin: 0 100%;
	  transform-origin: 0 100%;
	}
	.perspective:after {
	  display: none;
	}
	.raised {
	  -webkit-box-shadow: 0 15px 10px -10px rgba(0, 0, 0, 0.5) , 0 1px 4px rgba(0, 0, 0, 0.3) , 0 0 40px rgba(0, 0, 0, 0.1) inset;
	  -mox-box-shadow: 0 15px 10px -10px rgba(0, 0, 0, 0.5) , 0 1px 4px rgba(0, 0, 0, 0.3) , 0 0 40px rgba(0, 0, 0, 0.1) inset;
	  box-shadow: 0 15px 10px -10px rgba(0, 0, 0, 0.5) , 0 1px 4px rgba(0, 0, 0, 0.3) , 0 0 40px rgba(0, 0, 0, 0.1) inset;
	}
	.curved:before {
	  top: 10px;
	  bottom: 10px;
	  left: 0;
	  right: 50%;
	  -webkit-box-shadow: 0 0 15px rgba(0, 0, 0, 0.6);
	  -mox-box-shadow: 0 0 15px rgba(0, 0, 0, 0.6);
	  box-shadow: 0 0 15px rgba(0, 0, 0, 0.6);
	  -moz-border-radius: 10px / 100px;
	  border-radius: 10px / 100px;
	}
	.curved.v2:before {
	  right: 0;
	}
	.curved.h1:before {
	  top: 50%;
	  bottom: 0;
	  left: 10px;
	  right: 10px;
	  -moz-border-radius: 100px / 10px;
	  border-radius: 100px / 10px;
	}
	.curved.h2:before {
	  top: 0;
	  bottom: 0;
	  left: 10px;
	  right: 10px;
	  -moz-border-radius: 100px / 10px;
	  border-radius: 100px / 10px;
	}
	.rotated {
	  -webkit-box-shadow: none;
	  -mox-box-shadow: none;
	  box-shadow: none;
	  -webkit-transform: rotate(-3deg);
	  -moz-transform: rotate(-3deg);
	  -ms-transform: rotate(-3deg);
	  -o-transform: rotate(-3deg);
	  transform: rotate(-3deg);
	}
	.rotated > :first-child:before {
	  content: "";
	  position: absolute;
	  z-index: -1;
	  top: 0;
	  bottom: 0;
	  left: 0;
	  right: 0;
	  background: #fff;
	  -webkit-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3) , 0 0 40px rgba(0, 0, 0, 0.1) inset;
	  -mox-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3) , 0 0 40px rgba(0, 0, 0, 0.1) inset;
	  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3) , 0 0 40px rgba(0, 0, 0, 0.1) inset;
	}
</style>

<table id='retardostable' class='t2' style='width:100%; margin: auto; text-align: center; '>
	
		<?php
			foreach($retardos['A'] as $tipo => $info){
				//echo "<div class='drop-shadow curved v2 block' title=\"".$info['Tit']."\" ncorto='".$info['ncorto']."', id='".$info['asesorid']."', asesorid='".$info['asesorid']."', fecha='".$info['fecha']."', horaid='".$info['horaid']."'>".$info['Ret']."</div>";
			}
			for($i=25;$i>=0;$i--){
				echo "<tr><td style='text-align: center; padding: 11 0 5 0'>\n";
				for($x=0;$x<48;$x++){
					if($i==25){
						echo "<div class='drop-shadow curved v2 block none'>".($x/2)."</div>\n\t";	
					}else{
						if(isset($block[$x][$i])){
							echo "<div class='drop-shadow curved v2 block green' title='".$block[$x][$i]."'></div>\n\t";	
						}else{
							echo "<div class='drop-shadow curved v2 block none'></div>\n\t";	
						}
					}
				}
				echo "</td></tr>\n";
			}
		?>
	
</table>
data ok
<pre>
	<?php print_r($pya); ?>
</pre>


