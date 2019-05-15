<?php

include_once("../modules/modules.php");

initSettings::start(true, 'monitor_pya');
initSettings::printTitle('PyA Monitor');
timeAndRegion::setRegion('Cun');

Scripts::periodScript('date_search', 'fin', 'norange: true');

$connectdb=Connection::mysqliDB('CC');

$err_count=0;
$dateok=$_POST['fecha'];
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

$tbody="<td>Fecha</td><td><input type='text' value='$dateok' id='date_search' name='fecha'><input type='text' value='$dateok' id='fin' name='fechafin'></td>";

Filters::showFilter($_SERVER['PHP_SELF'], 'POST', 'consultar', 'Consultar' , $tbody);

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
IF($result=$connectdb->query($query)){
	$fila=$result->fetch_assoc();
	$lastupdate=$fila['update'];
}

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
	window.location.reload();
	}


</script>
<script>

var total =<?php echo $timetoupdate; ?>;
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




<?php

//Define DB and Functions
if(date('Y-m-d', strtotime($date))!=date('Y-m-d', strtotime('now'))){
	$function="";
	$db="t_Sesiones";
}else{
	$function="TD";
	$db="Sesiones";
}



$query="SELECT
			a.id as asesorid, b.id as horarioid, `N Corto`, g.Departamento, h.Puesto,
			IF(LogAsesor$function(b.Fecha,b.asesor,'in')>'23:42:00','00:00:00',LogAsesor$function(b.Fecha,b.asesor,'in')) as Login,
			LogAsesor$function(b.Fecha,b.asesor,'out') as Logout,
			`jornada start`, `jornada end`, `comida start`, `comida end`, `extra1 start`, `extra1 end`,  `extra2 start`, `extra2 end`,
			d.Excepcion, c.caso as excep_caso, c.Nota as excep_nota, c.username as excep_aplicado, c.`Last Update` as excep_f_aplicado,
			Ausentismo, e.caso as aus_caso, e.Comments as aus_nota, e.`Last Update` as aus_f_aplicado, e.username as aus_aplicado
		FROM
			(SELECT a.*, b.dep, b.puesto as puestoOK FROM Asesores a LEFT JOIN daily_dep b ON a.id=b.asesor WHERE ('$date' < Egreso AND '$date' >= Ingreso) HAVING dep NOT IN (1,29,30,31,47) AND dep IS NOT NULL) a
		LEFT JOIN
			(SELECT * FROM `Historial Programacion` WHERE Fecha='$date' AND  `jornada start`!=`jornada end`) b ON a.id=b.asesor
		LEFT JOIN
			(SELECT * FROM PyA_Exceptions a LEFT JOIN userDB b ON a.changed_by=b.userid) c ON b.id=c.horario_id
		LEFT JOIN
			`Tipos Excepciones` d ON c.tipo=d.exc_type_id
		LEFT JOIN
			(SELECT * FROM Ausentismos a LEFT JOIN userDB b ON a.User=b.userid) e ON b.Fecha BETWEEN Inicio AND Fin AND b.asesor=e.asesor
		LEFT JOIN
			`Tipos Ausentismos` f ON tipo_ausentismo=f.id
		LEFT JOIN
			PCRCs g ON a.dep=g.id
		LEFT JOIN
			PCRCs_puestos h ON puestoOK=h.id
		ORDER BY
			Departamento, Nombre";

if($result=$connectdb->query($query)){
	$field_count=$result->field_count;
	$fields=$result->fetch_fields();
	while($fila=$result->fetch_row()){
		for($x=0;$x<$field_count;$x++){
			switch($fields[$x]->name){
				case 'Login':
				case 'Logout':
				case 'comida start':
				case 'comida end':
				case 'extra1 start':
				case 'extra1 end':
				case 'extra2 start':
				case 'extra2 end':
				case 'jornada start':
				case 'jornada end':
					if($fila[$x]!=NULL){
						$time_tmp = new DateTime($date.' '.$fila[$x].' America/Mexico_city');
						$time_tmp -> setTimezone($cun_time);
						$pya[$fila[3]][$fila[2]][$fields[$x]->name]=$time_tmp->format('H:i:s');
					}else{
						$pya[$fila[3]][$fila[2]][$fields[$x]->name]=$fila[$x];
					}
					break;
				default:
					$pya[$fila[3]][$fila[2]][$fields[$x]->name]=utf8_encode($fila[$x]);
					break;
			}

		}
	}
}else{
	echo $connectdb->error." ON $query";
}


foreach($pya as $departamento => $info){
	foreach($info as $asesor => $info2){
		$pya[$departamento][$asesor]['validado']="OK";

		//Retardos
		if($info2['Ausentismo']==NULL && date('Y-m-d',strtotime($date))<=date('Y-m-d')){
			if($info2['Login']==NULL){
				if(date('H:i:s')>=date('H:i:s', strtotime($info2['jornada start'].' +1 minutes'))){
					if(date('H:i:s')>date('H:i:s', strtotime($info2['jornada start'].' +10 minutes'))){
						$pya[$departamento][$asesor]['Retardo_tipo']="B";
					}else{
						$pya[$departamento][$asesor]['Retardo_tipo']="A";
					}
				}
			}else{
				if(date('H:i:s', strtotime($info2['Login']))>=date('H:i:s', strtotime($info2['jornada start'].' +1 minutes'))){
					if(date('H:i:s', strtotime($info2['Login']))>date('H:i:s', strtotime($info2['jornada start'].' +13 minutes'))){
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

				$retardos[$pya[$departamento][$asesor]['Retardo_tipo']][$asesor]['Ret']="$asesor<br><span style='font-size:12'>(".$info2['Departamento']." / ".$info2['Puesto'].")</span><br>js: ".$info2['jornada start']."<br>li: ".$info2['Login'].$excep;
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
		height: 70px;
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
	  width: 130px;
	  padding: 1em;
	  padding-top: 5px;
	  margin: 1px 7px 15px;
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
<div style='text-align: right'>Ultima Actualizacion: <?php echo date('d-m-Y  H:i:s', strtotime($lastupdate)); ?><l id='demo'></l></div>
<table id='retardostable' class='t2' style='width:100%; margin: auto; text-align: center; '>
	<tr>
	<td class='title' width='65px'>Retardos "A"</td>
	<td style='text-align: left'>
		<?php
			if(isset($retardos['A'])){
				foreach($retardos['A'] as $tipo => $info){
					echo "<div class='drop-shadow curved v2 block' title=\"".$info['Tit']."\" ncorto='".$info['ncorto']."', id='".$info['asesorid']."', asesorid='".$info['asesorid']."', fecha='".$info['fecha']."', horaid='".$info['horaid']."'>".$info['Ret']."</div>";
				}
			}
		?>
	</td>
	</tr>
	<tr>
	<td class='title'>Retardos "B"</td>
	<td style='text-align: left'>
		<?php

			if(isset($retardos['B'])){
				foreach($retardos['B'] as $tipo => $info){
					if($info['Login']==1){
						if($info['Excep']!=1){
							$class='flashredpya';
						}else{
							$class='green';
						}
					}else{
						if($info['Excep']!=1){
							$class='orange';
						}else{
							$class='green';
						}
					}
					echo "<div class='drop-shadow curved v2 block $class' title=\"".$info['Tit']."\" ncorto='".$info['ncorto']."', id='".$info['asesorid']."', asesorid='".$info['asesorid']."', fecha='".$info['fecha']."', horaid='".$info['horaid']."'>".$info['Ret']."</div>";
				}
			}
		?>
	</td>
	</tr>
</table>
<br>
<div style='width:100%; height: 470px; margin:auto; overflow: auto' id='content-horarios'>
<table class='t2' style='width:100%; margin: auto; text-align: center; '>
	<?php
		for($i=0;$i<48;$i++){
			if($i % 2 != 0){
				$x=date('H:i',strtotime(intval($i/2).':30:00'));
				$id="id='hora".intval($i/2)."_5'";
			}else{
				$x=date('H:i',strtotime(intval($i/2).':00:00'));
				$id="id='hora".intval($i/2)."_0'";
			}
			echo "<tr $id><td class='title' width='65px'>$x</td>";
			echo "<td style='text-align: left'>";
			foreach($pya as $departamento => $info){
				asort($info);
				foreach($info as $asesor => $info2){
					if(date('H:i', strtotime($info2['jornada start']))==$x){

						//Retardo
						if(isset($pya[$departamento][$asesor]['Retardo_tipo'])){
							if($info2['Login']==NULL){
								$class='orange';
							}else{
								if($pya[$departamento][$asesor]['Retardo_tipo']=="B"){
									$class='flashredpya';
								}else{
									$class='orange';
								}
							}
						$bg="";
						}else{
							$class='';
							if($info2['Login']==NULL){
								$bg="style='background: white'";
							}else{
								$bg="";
								$class='green';
							}
						}

						//Ausentismo
						if($info2['Ausentismo']==NULL){
							//Excepciones
							if($info2['Excepcion']==NULL){
								$showlog=$info2['Login'];
								$title="";
							}else{
								$showlog=$info2['Login']."<br>".$info2['Excepcion'];
								$title="Excepcion Aplicada por:<br><a style='color: red'>".$info2['excep_aplicado']."</a><br>".$info2['excep_f_aplicado']."<br>caso: ".$info2['excep_caso']."<br>Nota: ".$info2['excep_nota'];
								$class='green';
							}
						}else{
							//Excepciones
							if($info2['Excepcion']==NULL){
								$showlog=$info2['Ausentismo'];
								$title="Ausentismo Aplicado por:<br><a style='color: red'>".$info2['aus_aplicado']."</a><br>".$info2['aus_f_aplicado']."<br>caso: ".$info2['aus_caso']."<br>Nota: ".$info2['aus_nota'];
								$bg="";
								$class='';
							}else{
								$showlog=$info2['Login']."<br>".$info2['Excepcion'];
								$title="Excepcion Aplicada por:<br><a style='color: red'>".$info2['excep_aplicado']."</a><br>".$info2['excep_f_aplicado']."<br>caso: ".$info2['excep_caso']."<br>Nota: ".$info2['excep_nota'];
								$bg="";
								$class='';
							}

						}


						echo "<div class='drop-shadow curved v2 blockhora $class' $bg title=\"$title\" ncorto='".$info2['N Corto']."', id='h_".$info2['asesorid']."', asesorid='".$info2['asesorid']."', fecha='$date', horaid='".$info2['horarioid']."'>"
						."$asesor<br><span style='font-size:12'>($departamento / ".$info2['Puesto'].")</span><br>$showlog</div>";
					}

				}
				unset($asesor,$info2);
			}
			echo "</td>";
			unset($departamento,$info);
			echo "</tr>";
		}

$connectdb->close();
	?>
</table>
</div>


<!--Formulario-->
<?php include("../common/add_exception_test.php"); ?>
