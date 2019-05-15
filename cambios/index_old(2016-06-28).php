<?
session_start();
$this_page=$_SERVER['PHP_SELF'];
if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
date_default_timezone_set('America/Bogota');
$credential="schedules_change";


$direction=realpath('../');
include("$direction/DBAsesores.php");
include("$direction/DBPcrcs.php");

$menu_programaciones="class='active'";

//
echo date('H:i:s',strtotime('::00'));
//Get Variables
	$applied=$_POST['applied'];

	$tipo=$_POST['tipo'];
	if($applied!=1){
		$id1=$_POST['asesor1'];
		$id2=$_POST['asesor2'];
		$dep=$_POST['dep'];
		$fecha1=$_POST['fecha1'];
		$fecha2=$_POST['fecha2'];
		$titulo="Horario Actual";
	}else{
		$id1=$_POST['id1'];
		$id2=$_POST['id2'];
		$dep=$_POST['dep'];
		$fecha1=$_POST['fechainicio'];
		$fecha2=$_POST['fechainicio2'];
		$titulo="Cambios Aplicados";
		$caso=$_POST['caso'];

		$sql_fecha1=$_POST['fechainicio'];
		$sql_js1=$_POST['horai1'].":".$_POST['mini1'].":00";
		$sql_je1=$_POST['horaf1'].":".$_POST['minf1'].":00";
		$sql_cs1=$_POST['horaci1'].":".$_POST['minci1'].":00";
		$sql_ce1=$_POST['horacf1'].":".$_POST['mincf1'].":00";

		$sql_fecha12=$_POST['fechainicio12'];
		$sql_js12=$_POST['horai12'].":".$_POST['mini12'].":00";
		$sql_je12=$_POST['horaf12'].":".$_POST['minf12'].":00";
		$sql_cs12=$_POST['horaci12'].":".$_POST['minci12'].":00";
		$sql_ce12=$_POST['horacf12'].":".$_POST['mincf12'].":00";

		$sql_fecha2=$_POST['fechainicio2'];
		$sql_js2=$_POST['horai2'].":".$_POST['mini2'].":00";
		$sql_je2=$_POST['horaf2'].":".$_POST['minf2'].":00";
		$sql_cs2=$_POST['horaci2'].":".$_POST['minci2'].":00";
		$sql_ce2=$_POST['horacf2'].":".$_POST['mincf2'].":00";

		$sql_fecha22=$_POST['fechainicio22'];
		$sql_js22=$_POST['horai22'].":".$_POST['mini22'].":00";
		$sql_je22=$_POST['horaf22'].":".$_POST['minf22'].":00";
		$sql_cs22=$_POST['horaci22'].":".$_POST['minci22'].":00";
		$sql_ce22=$_POST['horacf22'].":".$_POST['mincf22'].":00";
	}

//queries for changes
if($applied==1){

	$query="SELECT * FROM `Historial Programacion` WHERE `asesor`='$id1' AND `Fecha`='$fecha1'";
	$result=mysql_query($query);
	$id_horario1=mysql_result($result,0,'id');
	$query="SELECT * FROM `Historial Programacion` WHERE `asesor`='$id2' AND `Fecha`='$fecha1'";
	$result=mysql_query($query);
	$id_horario2=mysql_result($result,0,'id');
	if($tipo==2){
		$query="SELECT * FROM `Historial Programacion` WHERE `asesor`='$id1' AND `Fecha`='$fecha2'";
		$result=mysql_query($query);
		$id_horario12=mysql_result($result,0,'id');
		$query="SELECT * FROM `Historial Programacion` WHERE `asesor`='$id2' AND `Fecha`='$fecha2'";
		$result=mysql_query($query);
		$id_horario22=mysql_result($result,0,'id');
	}
	if($tipo!=2){
		$query="INSERT INTO `Cambios de Turno` (`id_horario`,`id_asesor`,`id_asesor 2`,`tipo`,`caso`,`fecha`,`jornada start old`,`jornada end old`,`comida start old`,`comida end old`,`jornada start new`,`jornada end new`,`comida start new`,`comida end new`,User) VALUES ('$id_horario1','$id1','$id2','$tipo','$caso','$fecha1','$sql_js2','$sql_je2','$sql_cs2','$sql_ce2','$sql_js1','$sql_je1','$sql_cs1','$sql_ce1','".$_SESSION['id']."')";
		mysql_query($query);
		if(mysql_error()){
			echo mysql_error()."<br>$query<br>";
		}
		$query="SELECT MAX(id) as 'id' FROM `Cambios de Turno` WHERE `id_horario`='$id_horario1'";
		$result=mysql_query($query);
		$idchange=mysql_result($result,0,'id');
		$query="UPDATE `Historial Programacion` SET `jornada start`='$sql_js1',`jornada end`='$sql_je1',`comida start`='$sql_cs1',`comida end`='$sql_ce1',`change`='$idchange' WHERE `id`='$id_horario1'";
		mysql_query($query);
		if(mysql_error()){
			echo mysql_error()."<br>$query<br>";
		}
		if($tipo!=3){
		$query="INSERT INTO `Cambios de Turno` (`id_horario`,`id_asesor`,`id_asesor 2`,`tipo`,`caso`,`fecha`,`jornada start old`,`jornada end old`,`comida start old`,`comida end old`,`jornada start new`,`jornada end new`,`comida start new`,`comida end new`,USER) VALUES ('$id_horario2','$id2','$id1','$tipo','$caso','$fecha1','$sql_js1','$sql_je1','$sql_cs1','$sql_ce1','$sql_js2','$sql_je2','$sql_cs2','$sql_ce2','".$_SESSION['id']."')";
		mysql_query($query);
		if(mysql_error()){
			echo mysql_error()."<br>$query<br>";
		}
		$query="SELECT MAX(id) as 'id' FROM `Cambios de Turno` WHERE `id_horario`='$id_horario2'";
		$result=mysql_query($query);
		$idchange=mysql_result($result,0,'id');
		$query="UPDATE `Historial Programacion` SET `jornada start`='$sql_js2',`jornada end`='$sql_je2',`comida start`='$sql_cs2',`comida end`='$sql_ce2',`change`='$idchange' WHERE `id`='$id_horario2'";
		mysql_query($query);
		if(mysql_error()){
			echo mysql_error()."<br>$query<br>";
		}
		}
	}else{
		$query="INSERT INTO `Cambios de Turno` (`id_horario`,`id_asesor`,`id_asesor 2`,`tipo`,`caso`,`fecha`,`jornada start old`,`jornada end old`,`comida start old`,`comida end old`,`jornada start new`,`jornada end new`,`comida start new`,`comida end new`,User) VALUES ('$id_horario1','$id1','$id2','$tipo','$caso','$fecha1','$sql_js22','$sql_je22','$sql_cs22','$sql_ce22','$sql_js1','$sql_je1','$sql_cs1','$sql_ce1','".$_SESSION['id']."')";
		mysql_query($query);
		if(mysql_error()){
			echo mysql_error()."<br>$query<br>";
		}
		$query="SELECT MAX(id) as 'id' FROM `Cambios de Turno` WHERE `id_horario`='$id_horario1'";
		$result=mysql_query($query);
		$idchange=mysql_result($result,0,'id');
		$query="UPDATE `Historial Programacion` SET `jornada start`='$sql_js1',`jornada end`='$sql_je1',`comida start`='$sql_cs1',`comida end`='$sql_ce1',`change`='$idchange' WHERE `id`='$id_horario1'";
		mysql_query($query);
		if(mysql_error()){
			echo mysql_error()."<br>$query<br>";
		}
		$query="INSERT INTO `Cambios de Turno` (`id_horario`,`id_asesor`,`id_asesor 2`,`tipo`,`caso`,`fecha`,`jornada start old`,`jornada end old`,`comida start old`,`comida end old`,`jornada start new`,`jornada end new`,`comida start new`,`comida end new`,User) VALUES ('$id_horario12','$id1','$id2','$tipo','$caso','$fecha2','$sql_js2','$sql_je2','$sql_cs2','$sql_ce2','$sql_js12','$sql_je12','$sql_cs12','$sql_ce12','".$_SESSION['id']."')";
		mysql_query($query);
		$query="SELECT MAX(id) as 'id' FROM `Cambios de Turno` WHERE `id_horario`='$id_horario12'";
		$result=mysql_query($query);
		$idchange=mysql_result($result,0,'id');
		$query="UPDATE `Historial Programacion` SET `jornada start`='$sql_js12',`jornada end`='$sql_je12',`comida start`='$sql_cs12',`comida end`='$sql_ce12',`change`='$idchange' WHERE `id`='$id_horario12'";
		mysql_query($query);
		if(mysql_error()){
			echo mysql_error()."<br>$query<br>";
		}
		$query="INSERT INTO `Cambios de Turno` (`id_horario`,`id_asesor`,`id_asesor 2`,`tipo`,`caso`,`fecha`,`jornada start old`,`jornada end old`,`comida start old`,`comida end old`,`jornada start new`,`jornada end new`,`comida start new`,`comida end new`,User) VALUES ('$id_horario2','$id2','$id1','$tipo','$caso','$fecha1','$sql_js1','$sql_je1','$sql_cs1','$sql_ce1','$sql_js22','$sql_je22','$sql_cs22','$sql_ce22','".$_SESSION['id']."')";
		mysql_query($query);
		if(mysql_error()){
			echo mysql_error()."<br>$query<br>";
		}
		$query="SELECT MAX(id) as 'id' FROM `Cambios de Turno` WHERE `id_horario`='$id_horario2'";
		$result=mysql_query($query);
		$idchange=mysql_result($result,0,'id');
		if(mysql_error()){
			echo mysql_error()."<br>$query<br>";
		}
		$query="UPDATE `Historial Programacion` SET `jornada start`='$sql_js22',`jornada end`='$sql_je22',`comida start`='$sql_cs22',`comida end`='$sql_ce22',`change`='$idchange' WHERE `id`='$id_horario2'";
		mysql_query($query);
		if(mysql_error()){
			echo mysql_error()."<br>$query<br>";
		}
		$query="INSERT INTO `Cambios de Turno` (`id_horario`,`id_asesor`,`id_asesor 2`,`tipo`,`caso`,`fecha`,`jornada start old`,`jornada end old`,`comida start old`,`comida end old`,`jornada start new`,`jornada end new`,`comida start new`,`comida end new`,User) VALUES ('$id_horario22','$id2','$id1','$tipo','$caso','$fecha2','$sql_js12','$sql_je12','$sql_cs12','$sql_ce12','$sql_js2','$sql_je2','$sql_cs2','$sql_ce2','".$_SESSION['id']."')";
		mysql_query($query);
		if(mysql_error()){
			echo mysql_error()."<br>$query<br>";
		}
		$query="SELECT MAX(id) as 'id' FROM `Cambios de Turno` WHERE `id_horario`='$id_horario22'";
		$result=mysql_query($query);
		$idchange=mysql_result($result,0,'id');
		$query="UPDATE `Historial Programacion` SET `jornada start`='$sql_js2',`jornada end`='$sql_je2',`comida start`='$sql_cs2',`comida end`='$sql_ce2',`change`='$idchange' WHERE `id`='$id_horario22'";
		mysql_query($query);
		if(mysql_error()){
			echo mysql_error()."<br>$query<br>";
		}
		
	}
}

//Set Values
$date1= date("Y-m-d", strtotime($fecha1) );
$date2= date("Y-m-d", strtotime($fecha2) );

//Queries for current progs
$query1="SELECT * FROM `Historial Programacion` WHERE `asesor`='$id1' AND `Fecha`='$date1'";
$result1=mysql_query($query1);
if($tipo==1 || $tipo==4){$date2ok=$date1;}else{$date2ok=$date2;}
$query2="SELECT * FROM `Historial Programacion` WHERE `asesor`='$id2' AND `Fecha`='$date2ok'";
$result2=mysql_query($query2);
$query12="SELECT * FROM `Historial Programacion` WHERE `asesor`='$id1' AND `Fecha`='$date2ok'";
$result12=mysql_query($query12);

$query22="SELECT * FROM `Historial Programacion` WHERE `asesor`='$id2' AND `Fecha`='$date1'";
$result22=mysql_query($query22);

$q1ji=mysql_result($result1,0,'jornada start');
$q1jf=mysql_result($result1,0,'jornada end');
$q1ci=mysql_result($result1,0,'comida start');
$q1cf=mysql_result($result1,0,'comida end');

$q2ji=mysql_result($result2,0,'jornada start');
$q2jf=mysql_result($result2,0,'jornada end');
$q2ci=mysql_result($result2,0,'comida start');
$q2cf=mysql_result($result2,0,'comida end');

$q12ji=mysql_result($result12,0,'jornada start');
$q12jf=mysql_result($result12,0,'jornada end');
$q12ci=mysql_result($result12,0,'comida start');
$q12cf=mysql_result($result12,0,'comida end');

$q22ji=mysql_result($result22,0,'jornada start');
$q22jf=mysql_result($result22,0,'jornada end');
$q22ci=mysql_result($result22,0,'comida start');
$q22cf=mysql_result($result22,0,'comida end');

$q1ji_F=date('H:i',strtotime($q1ji));
$q1jf_F=date('H:i',strtotime($q1jf));
$q1ci_F=date('H:i',strtotime($q1ci));
$q1cf_F=date('H:i',strtotime($q1cf));

$q2ji_F=date('H:i',strtotime($q2ji));
$q2jf_F=date('H:i',strtotime($q2jf));
$q2ci_F=date('H:i',strtotime($q2ci));
$q2cf_F=date('H:i',strtotime($q2cf));

$q12ji_F=date('H:i',strtotime($q12ji));
$q12jf_F=date('H:i',strtotime($q12jf));
$q12ci_F=date('H:i',strtotime($q12ci));
$q12cf_F=date('H:i',strtotime($q12cf));

$q22ji_F=date('H:i',strtotime($q22ji));
$q22jf_F=date('H:i',strtotime($q22jf));
$q22ci_F=date('H:i',strtotime($q22ci));
$q22cf_F=date('H:i',strtotime($q22cf));


$q1hi=date('H',strtotime($q1ji));
$q1mi=date('i',strtotime($q1ji));
$q1hf=date('H',strtotime($q1jf));
$q1mf=date('i',strtotime($q1jf));
$q1chi=date('H',strtotime($q1ci));
$q1cmi=date('i',strtotime($q1ci));
$q1chf=date('H',strtotime($q1cf));
$q1cmf=date('i',strtotime($q1cf));
$q2hi=date('H',strtotime($q2ji));
$q2mi=date('i',strtotime($q2ji));
$q2hf=date('H',strtotime($q2jf));
$q2mf=date('i',strtotime($q2jf));
$q2chi=date('H',strtotime($q2ci));
$q2cmi=date('i',strtotime($q2ci));
$q2chf=date('H',strtotime($q2cf));
$q2cmf=date('i',strtotime($q2cf));

$q12hi=date('H',strtotime($q12ji));
$q12mi=date('i',strtotime($q12ji));
$q12hf=date('H',strtotime($q12jf));
$q12mf=date('i',strtotime($q12jf));
$q12chi=date('H',strtotime($q12ci));
$q12cmi=date('i',strtotime($q12ci));
$q12chf=date('H',strtotime($q12cf));
$q12cmf=date('i',strtotime($q12cf));
$q22hi=date('H',strtotime($q22ji));
$q22mi=date('i',strtotime($q22ji));
$q22hf=date('H',strtotime($q22jf));
$q22mf=date('i',strtotime($q22jf));
$q22chi=date('H',strtotime($q22ci));
$q22cmi=date('i',strtotime($q22ci));
$q22chf=date('H',strtotime($q22cf));
$q22cmf=date('i',strtotime($q22cf));

if($q1ji==$q1jf){
	$q1horario="Descanso";
	$q1comida="N/A";
}else{
	$q1horario= "$q1ji_F - $q1jf_F";
	if($q1ci==$q1cf){
		$q1comida="N/A";
	}else{
		$q1comida= "$q1ci_F - $q1cf_F";
	}
}

if($q2ji==$q2jf){
	$q2horario="Descanso";
	$q2comida="N/A";
}else{
	$q2horario= "$q2ji_F - $q2jf_F";
	if($q2ci==$q2cf){
		$q2comida="N/A";
	}else{
		$q2comida= "$q1ci_F - $q1cf_F";
	}
}

if($q12ji==$q12jf){
	$q12horario="Descanso";
	$q12comida="N/A";
}else{
	$q12horario= "$q12ji_F - $q12jf_F";
	if($q12ci==$q12cf){
		$q12comida="N/A";
	}else{
		$q12comida= "$q12ci_F - $q12cf_F";
	}
}

if($q22ji==$q22jf){
	$q22horario="Descanso";
	$q22comida="N/A";
}else{
	$q22horario= "$q22ji_F - $q22jf_F";
	if($q22ci==$q22cf){
		$q22comida="N/A";
	}else{
		$q22comida= "$q12ci_F - $q12cf_F";
	}
}

//List Depts
	$i=0;
	$departs="<option value'' selected>Selecciona...</option>";
	while($i<$pcrcs_num){
		if($pcrcs_id_Sorted[$i]==$dep){$sel="selected";}else{$sel="";}
		$departs="$departs<option value='$pcrcs_id_Sorted[$i]' $sel>$pcrcs_departamento_Sorted[$i]</option>\n";
	$i++;
	}

//Function for listing Asesores
	function print_options($opt,$dept){
		global $id1, $id2, $ASnum, $ASNCorto_Sorted, $ASdepto_Sorted, $ASactive_Sorted, $ASid_Sorted, $Asesor;
		if($opt==1){$as=$id1;}else{$as=$id2;}
		$i=0;
		while ($i<$ASnum){
			//Print only Dept Asesores
			if($ASdepto_Sorted[$i]==$dept && $ASactive_Sorted[$i]==1){
				if($ASid_Sorted[$i]==$as){$sel=" selected"; $Asesor[$opt]=$i;}else{$sel="";}
				$optprint="$optprint<option value='$ASid_Sorted[$i]'$sel>$ASNCorto_Sorted[$i]</option>";
			}
		$i++;
		}
		
		echo $optprint;
	}
	
//Get number of changes
$query="SELECT count(`id`) as 'cambios' FROM `Cambios de Turno` WHERE `id_asesor`='$id1' AND MONTH(`fecha`)='".date('n',strtotime($fecha1))."' AND YEAR(`fecha`)='".date('Y',strtotime($fecha1))."' AND `tipo`=1";
$result=mysql_query($query);
$cambios1=mysql_result($result,0,'cambios');
$query="SELECT count(`id`) as 'cambios' FROM `Cambios de Turno` WHERE `id_asesor`='$id1' AND MONTH(`fecha`)='".date('n',strtotime($fecha1))."' AND YEAR(`fecha`)='".date('Y',strtotime($fecha1))."'  AND `tipo`=2";
$result=mysql_query($query);
$cambios1=$cambios1 + (mysql_result($result,0,'cambios')/2);
$query="SELECT count(`id`) as 'cambios' FROM `Cambios de Turno` WHERE `id_asesor`='$id2' AND MONTH(`fecha`)='".date('n',strtotime($fecha1))."' AND YEAR(`fecha`)='".date('Y',strtotime($fecha1))."' AND `tipo`=1";
$result=mysql_query($query);
$cambios2=mysql_result($result,0,'cambios');
$query="SELECT count(`id`) as 'cambios' FROM `Cambios de Turno` WHERE `id_asesor`='$id2' AND MONTH(`fecha`)='".date('n',strtotime($fecha1))."' AND YEAR(`fecha`)='".date('Y',strtotime($fecha1))."'  AND `tipo`=2";
$result=mysql_query($query);
$cambios2=$cambios2 + (mysql_result($result,0,'cambios')/2);


include("../common/scripts.php");
?>

          
<ul>
    <li></li>
</ul>
<script>
$(document).ready(function() {

//Script -- Define "Asesores" listed depending on "Departamento"
    $("#pcrc_select").change(function() {
        var val = $(this).val();
        switch(val){
        	<?php

        	$i=0;
        	while($i<$pcrcs_num){
        		echo "case '$i':\n";
        		echo "$(\"#sel1\").html(\"";
        		print_options(1,$i);
        		echo "\");\n";
        		echo "$(\"#sel2\").html(\"";
        		print_options(2,$i);
        		echo "\");\n break;\n";
        	$i++;
        	}

        	?>

        }



   });

});

//Change out hour 1
  function changehour(opt,esq){
  	var horai= $("#horai"+opt).val();
  	var mini= $("#mini"+opt).val();
  	var minf=0;
  	if (esq!=8){
    		var fin= parseInt(horai) + parseInt(esq);
    		var minfin = parseInt(mini)
    	}else{
    		var minfin = parseInt(mini)
    		if(horai>=16){
    			if(horai==16 && mini<30){
    				esq=parseInt(esq)-1;
    				if(mini>30){
    					minfin=parseInt(mini)-30;
    				}else{
    					minfin=parseInt(mini)+30;
    				}
    			}else{
    			esq=parseInt(esq)-1;
    			minfin=parseInt(mini);
    			}
    		}else if (horai>=12){
    			if(horai!=12){
    				esq=parseInt(esq)-1;
    				if(mini>=30){
    					minfin=parseInt(mini)-30;
    				}else{
    					minfin=parseInt(mini)+30;
    				}
    			} else if (mini>0){
    				esq=parseInt(esq)-1;
    				if(mini>=30){
    					minfin=parseInt(mini)-30;
    				}else{
    					minfin=parseInt(mini)+30;
    				}
    			}
    		}
    	
    	var fin= parseInt(horai) + parseInt(esq);
    	}
    	
    	if (fin>=24){fin=fin-24;}
    	if (fin<10){fin="0" + fin;}
    	if (minfin<10){minfin="0" + minfin;}
    	    	
    $("#horafin"+opt).val(fin);
    $("#minfin"+opt).val(minfin);
    
        	
   }

//Show Fecha 2
	function showfecha(){
		var tipo= $("#tipo").val();
		switch (tipo){
		case '2':
			$("#fecha2").html("<input type='date' name='fecha2' value='$fecha2'/>");
			$("#fecha2label").html("Fecha 2:");
			break;
		case '1':
			$("#fecha2").html("<input type='date' name='fecha2' value='$fecha2' hidden/>");
			$("#fecha2label").html("");
			break;
		}
	}

</script>
<script>
  $(function() {
    $( ".show" ).tooltip({

        track: true,
        show: {
            effect: "slideDown",
            delay: 250
        }
    });
  });
</script>
<style>
    .ui-tooltip {
    width: 120px;
    height: auto;
    padding: 10px 20px;
    color: black;
    border-radius: 20px;
    font: bold 14px "Helvetica Neue", Sans-Serif;
    text-transform: uppercase;
    text-align: center;
    box-shadow: 0 0 7px black;
  }
</style>


<body>


<?php include("$direction/common/menu.php"); ?>

<table  class='t2' style='width: 100%'>
<form method='POST' action='<?php $_SERVER['PHP_SELF']; ?>' name="Cambios" id="cambios">
	<tr class='title'>
		<th colspan=5>Parametros</th>
	</tr>
	<tr>
		<td class='subtitle'>Departamento:</td>
		<td  class='pair'><select name='dep' id="pcrc_select" onchange='this.form.submit()'><? echo $departs; ?></select></td>
		<td class='subtitle'>Tipo de Cambio:</td>
		<td class='pair'><select name='tipo' id="tipo" onchange='this.form.submit()'><option value='1' <? if($tipo==1){ echo "selected"; }?>>Turno</option><option value='2' <? if($tipo==2){ echo "selected"; }?>>Descanso</option><option value='3' <? if($tipo==3){ echo "selected"; }?>>Ajuste</option><option value='4' <? if($tipo==4){ echo "selected"; }?>>Ajuste Turnos</option></select></td>
		<td rowspan=3  class='total'><input type='submit' name='consulta' value='Consultar'></td>
	</tr>
	<tr>
		<td class='subtitle'>Asesor 1</td>
		<td class='odd'><select name='asesor1' id="sel1" onchange='this.form.submit()'><? print_options(1,$dep); ?></select></td>
		<td class='subtitle'>Fecha 1:</td>
		<td class='odd'><input type='date' onchange='this.form.submit()' name='fecha1' value='<? echo $fecha1; ?>'/></td>
	</tr>
<?php if($tipo!=3){
	echo "<tr>
		<td class='subtitle'>Asesor 2</td>
		<td class='pair'><select name='asesor2' id='sel2' onchange='this.form.submit()'>";
		print_options(2,$dep);
		echo "</select></td>
		<td id='fecha2label' class='subtitle'>";
		if($tipo==2){echo "Fecha 2:";}
		echo "</td>
		<td id='fecha2' class='pair'>";
		if($tipo==2){echo "<input type='date' onchange='this.form.submit()' name='fecha2' value='$fecha2'/>";}else{echo "<input type='date' name='fecha2' value='$fecha2' hidden/>";}
		echo "</td>
	</tr>";}?>
	
</form>
</table>
</div>
<br><br><br>


<div style="width:1000px; margin:auto;">



<table  class='t2' style='width: 100%'>
	<tr class='title' >
		<th  colspan=9><? echo $titulo; ?></th>
	</tr>
	<tr class='subtitle'>
		<td>ID</td>
		<td>Asesor</td>
		<td>Esquema</td>
		<td>Fecha</td>
		<td>Horario</td>
		
		<td>Comida</td>
		<td>Cambios en<br>el mes</td>

	</tr>
<? if($tipo!=2){ goto Actual2; } ?>	
	<tr class='pair'>
		<td><? echo $ASid_Sorted[$Asesor[1]]; ?><input type='text' name='id12' value='<? echo $ASid_Sorted[$Asesor[1]]; ?>' hidden/></td>
		<td><? echo $ASNCorto_Sorted[$Asesor[1]]; ?></td>
		<td><? echo $ASesquema_Sorted[$Asesor[1]]; ?></td>
		<td><? echo $date2ok; ?></td>
		<td><? echo $q12horario; ?></td>
		
		<td><? echo $q12comida; ?></td>
		<td class='total'><div class='show' title='
        <?php
            $query="SELECT DISTINCT caso FROM `Cambios de Turno` WHERE id_asesor='".$ASid_Sorted[$Asesor[1]]."' AND MONTH(`fecha`)='".date('n',strtotime($fecha1))."' AND YEAR(`fecha`)='".date('Y',strtotime($fecha1))."' AND (`tipo`=1 OR `tipo`=2)";
            $result=mysql_query($query);
            $num=mysql_numrows($result);
            $i=0;
            while($i<$num){
                echo "Caso: ";
                echo mysql_result($result,$i,'caso');
                echo "&#013";
            $i++;
            }
        ?>

        '><? echo $cambios1; ?></div></td>

	</tr>
<? Actual2: ?>
	<tr class='odd'>
		<td><? echo $ASid_Sorted[$Asesor[1]]; ?><input type='text' name='id1' value='<? echo $ASid_Sorted[$Asesor[1]]; ?>' hidden/></td>
		<td><? echo $ASNCorto_Sorted[$Asesor[1]]; ?></td>
		<td><? echo $ASesquema_Sorted[$Asesor[1]]; ?></td>
		<td><? echo $date1; ?></td>
		<td><? echo $q1horario; ?></td>

		<td><? echo $q1comida; ?></td>
		<td class='total'><div class='show' title='
        <?php
            $query="SELECT DISTINCT caso FROM `Cambios de Turno` WHERE id_asesor='".$ASid_Sorted[$Asesor[1]]."' AND MONTH(`fecha`)='".date('n',strtotime($fecha1))."' AND YEAR(`fecha`)='".date('Y',strtotime($fecha1))."' AND (`tipo`=1 OR `tipo`=2)";
            $result=mysql_query($query);
            $num=mysql_numrows($result);

            $i=0;
            while($i<$num){
                echo "Caso: ";
                echo mysql_result($result,$i,'caso');
                echo "&#013";
            $i++;
            }
        ?>

        '><? echo $cambios1; ?></div></td>

	</tr>

<? if($tipo!=3){ echo "
	<tr class='pair'>
		<td>".$ASid_Sorted[$Asesor[2]]."<input type='text' name='id2' value='".$ASid_Sorted[$Asesor[2]]."' hidden/></td>
		<td>".$ASNCorto_Sorted[$Asesor[2]]."</td>
		<td>".$ASesquema_Sorted[$Asesor[2]]."</td>
		<td>$date2ok</td>
		<td>$q2horario</td>

		<td>$q2comida</td>
		<td class='total'><div class='show' title='";
            $query="SELECT DISTINCT caso FROM `Cambios de Turno` WHERE id_asesor='".$ASid_Sorted[$Asesor[2]]."' AND MONTH(`fecha`)='".date('n',strtotime($fecha1))."' AND YEAR(`fecha`)='".date('Y',strtotime($fecha1))."' AND (`tipo`=1 OR `tipo`=2)";
            $result=mysql_query($query);
            $num=mysql_numrows($result);
            $i=0;
            while($i<$num){
                echo "Caso: ";
                echo mysql_result($result,$i,'caso');
                echo "&#013";
            $i++;
            }
        echo "'>$cambios2</div></td>
	</tr>";}
if($tipo!=2){ goto EndActual; } ?>
	<tr class='odd'>
		<td><? echo $ASid_Sorted[$Asesor[2]]; ?><input type='text' name='id22' value='<? echo $ASid_Sorted[$Asesor[2]]; ?>' hidden/></td>
		<td><? echo $ASNCorto_Sorted[$Asesor[2]]; ?></td>
		<td><? echo $ASesquema_Sorted[$Asesor[2]]; ?></td>
		<td><? echo $date1; ?></td>
		<td><? echo $q22horario; ?></td>

		<td><? echo $q22comida; ?></td>
		<td class='total'><div class='show' title='
        <?php
            $query="SELECT DISTINCT caso FROM `Cambios de Turno` WHERE id_asesor='".$ASid_Sorted[$Asesor[2  ]]."' AND MONTH(`fecha`)='".date('n',strtotime($fecha1))."' AND YEAR(`fecha`)='".date('Y',strtotime($fecha1))."' AND (`tipo`=1 OR `tipo`=2)";
            $result=mysql_query($query);
            $num=mysql_numrows($result);

            $i=0;
            while($i<$num){
                echo "Caso: ";
                echo mysql_result($result,$i,'caso');
                echo "&#013";
            $i++;
            }
        ?>

        '><? echo $cambios2; ?></div></td>
	</tr>
<? EndActual: ?>
</table>
</div>
<? if(!isset($_POST['consulta'])){ exit;} ?>
<br><br><br>
<? if($applied==1){goto EndPage; } ?>
<div style="width:1000px; margin:auto;">
<table  class='t2' style='width: 100%'>
	<tr class='title'>
		<th colspan=8>Horario Nuevo</th>
	</tr>
	<tr class='subtitle'>
		
		<td>ID</td>
		<td>Asesor</td>
		<td>Esquema</td>
		<td>Fecha</td>
		<td>Horario Inicio</td>
		<td>Horario Fin</td>
		<td>Comida Inicio</td>
		<td>Comida Fin</td>
	</tr>
	<tr class='pair'><form name='cambio' action='<? $_SERVER['PHP_SELF'] ?>' method='post'>
		<td><? echo $ASid_Sorted[$Asesor[1]]; ?><input type='text' name='id1' value='<? echo $ASid_Sorted[$Asesor[1]]; ?>' hidden/><input type='text' name='tipo' value='<? echo $tipo; ?>' hidden/><input type='text' name='dep' value='<? echo $dep; ?>' hidden/><input type='text' name='applied' value='1' hidden/></td>
		<td><? echo $ASNCorto_Sorted[$Asesor[1]]; ?></td>
		<td><? echo $ASesquema_Sorted[$Asesor[1]]; ?></td>
		<td><input type="date" name="fechainicio" value='<? if($tipo==3){ echo date('Y-m-d', strtotime($date1));}else{echo $date2ok;} ?>'/></td>
		<td><input type="number" name="horai1" min="0" max="23" size='2' id="horai1" value='<? echo $q2hi; ?>' onchange="changehour(1,<? echo $ASesquema_Sorted[$Asesor[1]]; ?>);"/> : <input type="number" name="mini1" min="0" max="55" step="5" size='2' id='mini1' value='<? echo $q2mi; ?>' onchange="changehour(1,<? echo $ASesquema_Sorted[$Asesor[1]]; ?>);"/></td>
		<td><input type="number" name="horaf1" min="0" max="23" size='2' id="horafin1" value='<? echo $q2hf; ?>' /> : <input type="number" name="minf1" min="0" max="55" step="5" size='2' id='minfin1'  value='<? echo $q2mf; ?>' /></td>
		<td><input type="number" name="horaci1" min="0" max="23" size='2' id="horaci1" value='<? echo $q2chi; ?>' /> : <input type="number" name="minci1" min="0" max="55" step="5" size='2' id='minci1' value='<? echo $q2cmi; ?>' /></td>
		<td><input type="number" name="horacf1" min="0" max="23" size='2' id="horacf1" value='<? echo $q2chf; ?>' /> : <input type="number" name="mincf1" min="0" max="55" step="5" size='2' id='mincf1' value='<? echo $q2cmf; ?>' /></td>
	</tr>
<? 
if($tipo==3){ goto EndNuevo;}
if($tipo!=2){ goto Nuevo2;}
 ?>
	<tr class='odd'>
		<td><? echo $ASid_Sorted[$Asesor[2]]; ?><input type='text' name='id22' value='<? echo $ASid_Sorted[$Asesor[2]]; ?>' hidden/></td>
		<td><? echo $ASNCorto_Sorted[$Asesor[2]]; ?></td>
		<td><? echo $ASesquema_Sorted[$Asesor[2]]; ?></td>
		<td><input type="date" name="fechainicio22" value='<? echo $date2ok; ?>'/></td>
		<td><input type="number" name="horai22" min="0" max="23" size='2' id="horai22" value='<? echo $q12hi; ?>' onchange="changehour(22,<? echo $ASesquema_Sorted[$Asesor[2]]; ?>);"/> : <input type="number" name="mini22" min="0" max="55" step="5" size='2' id='mini22' value='<? echo $q12mi; ?>' onchange="changehour(22,<? echo $ASesquema_Sorted[$Asesor[1]]; ?>);"/></td>
		<td><input type="number" name="horaf22" min="0" max="23" size='2' id="horafin22" value='<? echo $q12hf; ?>' /> : <input type="number" name="minf22" min="0" max="55" step="5" size='2' id='minfin22'  value='<? echo $q12mf; ?>' /></td>
		<td><input type="number" name="horaci22" min="0" max="23" size='2' id="horaci22" value='<? echo $q12chi; ?>' /> : <input type="number" name="minci22" min="0" max="55" step="5" size='2' id='minci22' value='<? echo $q12cmi; ?>' /></td>
		<td><input type="number" name="horacf22" min="0" max="23" size='2' id="horacf22" value='<? echo $q12chf; ?>' /> : <input type="number" name="mincf22" min="0" max="55" step="5" size='2' id='mincf22' value='<? echo $q12cmf; ?>' /></td>
	</tr>

	<tr class='pair'>
		<td><? echo $ASid_Sorted[$Asesor[1]]; ?><input type='text' name='id12' value='<? echo $ASid_Sorted[$Asesor[1]]; ?>' hidden/></td>
		<td><? echo $ASNCorto_Sorted[$Asesor[1]]; ?></td>
		<td><? echo $ASesquema_Sorted[$Asesor[1]]; ?></td>
		<td><input type="date" name="fechainicio12" value='<? echo $date1; ?>'/></td>
		<td><input type="number" name="horai12" min="0" max="23" size='2' id="horai12" value='<? echo $q22hi; ?>' onchange="changehour(12,<? echo $ASesquema_Sorted[$Asesor[1]]; ?>);"/> : <input type="number" name="mini12" min="0" max="55" step="5" size='2' id='mini12' value='<? echo $q22mi; ?>' onchange="changehour(12,<? echo $ASesquema_Sorted[$Asesor[1]]; ?>);"/></td>
		<td><input type="number" name="horaf12" min="0" max="23" size='2' id="horafin12" value='<? echo $q22hf; ?>' /> : <input type="number" name="minf12" min="0" max="55" step="5" size='2' id='minfin12'  value='<? echo $q22mf; ?>' /></td>
		<td><input type="number" name="horaci12" min="0" max="23" size='2' id="horaci12" value='<? echo $q22chi; ?>' /> : <input type="number" name="minci12" min="0" max="55" step="5" size='2' id='minci12' value='<? echo $q22cmi; ?>' /></td>
		<td><input type="number" name="horacf12" min="0" max="23" size='2' id="horacf12" value='<? echo $q22chf; ?>' /> : <input type="number" name="mincf12" min="0" max="55" step="5" size='2' id='mincf12' value='<? echo $q22cmf; ?>' /></td>
	</tr>
<? Nuevo2: ?>
	<tr class='odd'>
		<td><? echo $ASid_Sorted[$Asesor[2]]; ?><input type='text' name='id2' value='<? echo $ASid_Sorted[$Asesor[2]]; ?>' hidden/></td>
		<td><? echo $ASNCorto_Sorted[$Asesor[2]]; ?></td>
		<td><? echo $ASesquema_Sorted[$Asesor[2]]; ?></td>
		<td><input type="date" name="fechainicio2" value='<? echo $date1; ?>'/></td>
		<td><input type="number" name="horai2" min="0" max="23" size='2' id="horai2" value='<? echo $q1hi; ?>' onchange="changehour(2,<? echo $ASesquema_Sorted[$Asesor[2]]; ?>);"/> : <input type="number" name="mini2" min="0" max="55" step="5" size='2' id='mini2' value='<? echo $q1mi; ?>' onchange="changehour(2,<? echo $ASesquema_Sorted[$Asesor[1]]; ?>);"/></td>
		<td><input type="number" name="horaf2" min="0" max="23" size='2' id="horafin2" value='<? echo $q1hf; ?>' /> :
		<input type="number" name="minf2" min="0" max="55" step="5" size='2' id='minfin2'  value='<? echo $q1mf; ?>' /></td>
		<td><input type="number" name="horaci2" min="0" max="23" size='2' id="horaci2" value='<? echo $q1chi; ?>' /> : <input type="number" name="minci2" min="0" max="55" step="5" size='2' id='minci2' value='<? echo $q1cmi; ?>' /></td>
		<td><input type="number" name="horacf2" min="0" max="23" size='2' id="horacf2" value='<? echo $q1chf; ?>' /> : <input type="number" name="mincf2" min="0" max="55" step="5" size='2' id='mincf2' value='<? echo $q1cmf; ?>' /></td>
	</tr>


	

	
<? EndNuevo: 
if(isset($_POST["consulta"])) { echo "<tr class='total'>
		<td colspan=4>Caso: <input type='text' name='caso' size=7 required></td>
		<td colspan=4><input type='submit' value='Aplicar'></td>
	</tr>";}
?>
	
</table>
<? EndPage: ?>
</div>
</div>


</body>