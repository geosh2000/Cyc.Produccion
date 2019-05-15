<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];
if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
date_default_timezone_set('America/Bogota');
$credential="schedules_change";
$menu_programaciones="class='active'";

include("../connectDB.php");
include("../common/scripts.php");
include("../common/menu.php");

//GET VARIABLES
if(isset($_POST['Aplicar'])){
    for($i=1;$i<=4;$i++){
            if(!isset($_POST['js_old'.$i])){continue;}
             $data[$i]['fecha']=$_POST['fecha'.$i];
             $data[$i]['caso']=$_POST['caso'];
             $data[$i]['js_old']=$_POST['js_old'.$i];
             $data[$i]['je_old']=$_POST['je_old'.$i];
             $data[$i]['cs_old']=$_POST['cs_old'.$i];
             $data[$i]['ce_old']=$_POST['ce_old'.$i];
             if($_POST['js_new'.$i]=="" || $_POST['js_new'.$i]==NULL){$data[$i]['js_new']="00:00:00";}else{$data[$i]['js_new']=$_POST['js_new'.$i].":00";}
             if($_POST['je_new'.$i]=="" || $_POST['je_new'.$i]==NULL){$data[$i]['je_new']="00:00:00";}else{$data[$i]['je_new']=$_POST['je_new'.$i].":00";}
             if($_POST['cs_new'.$i]=="" || $_POST['cs_new'.$i]==NULL){$data[$i]['cs_new']="00:00:00";}else{$data[$i]['cs_new']=$_POST['cs_new'.$i].":00";}
             if($_POST['ce_new'.$i]=="" || $_POST['ce_new'.$i]==NULL){$data[$i]['ce_new']="00:00:00";}else{$data[$i]['ce_new']=$_POST['ce_new'.$i].":00";}
             $data[$i]['idHorario_ch']=$_POST['idHorario'.$i];
             $data[$i]['asesor']=$_POST['asesor'.$i];
             $data[$i]['asesorch']=$_POST['asesorch'.$i];
    }
}


	$tipo=$_POST['tipo'];
	$asesor1=$_POST['asesor1'];
    $asesor2=$_POST['asesor2'];
    $dep=$_POST['dep'];
    $fecha1=date('Y-m-d',strtotime($_POST['fecha1']));
    if($_POST['fecha1']==NULL || $_POST['fecha1']==""){
        $fecha1=date('Y-m-d');
    }else{
        $fecha1=date('Y-m-d',strtotime($_POST['fecha1']));
    }
    if($_POST['fecha2']==NULL || $_POST['fecha2']=="" || !isset($_POST['fecha2'])){
        $fecha2=$fecha1;
    }else{
        $fecha2=date('Y-m-d',strtotime($_POST['fecha2']));
    }

//SAVE QUERIES
if(isset($_POST['Aplicar'])){
    //Save Historial Cambios
    foreach($data as $index => $info){
        $query="INSERT INTO `Cambios de Turno` (id_horario,id_asesor,`id_asesor 2`, tipo, caso, fecha, "
            ."`jornada start old`, `jornada end old`, `comida start old`, `comida end old`, `extra1 start old`, `extra1 end old`, `extra2 start old`, `extra2 end old`,"
            ."`jornada start new`, `jornada end new`, `comida start new`, `comida end new`, `extra1 start new`, `extra1 end new`, `extra2 start new`, `extra2 end new`,"
            ."`User`) VALUES "
            ."('".$info['idHorario_ch']."', '".$info['asesor']."', '".$info['asesorch']."', '".$tipo."', '".$info['caso']."', '".$info['fecha']."',"
            ." '".$info['js_old']."', '".$info['je_old']."', '".$info['cs_old']."', '".$info['ce_old']."', '00:00:00', '00:00:00', '00:00:00', '00:00:00',"
            ." '".$info['js_new']."', '".$info['je_new']."', '".$info['cs_new']."', '".$info['ce_new']."', '00:00:00', '00:00:00', '00:00:00', '00:00:00',"
            ." '".$_SESSION['id']."')";
        mysql_query($query);
        if(mysql_error()){
            $error[]=$info['idHorario_ch']." Historial Cambios ERROR => ".mysql_error()."on:<br>$query<br><br>";
        }else{
            //Save Historial Programacion
            $last_id=mysql_insert_id();
            $query="UPDATE `Historial Programacion` SET "
                    ."`jornada start`='".$info['js_new']."', `jornada end`='".$info['je_new']."', "
                    ."`comida start`='".$info['cs_new']."', `comida end`='".$info['ce_new']."', `change`='$last_id' "
                    ."WHERE id='".$info['idHorario_ch']."'";
            mysql_query($query);
            if(mysql_error()){
                $error[]=$info['idHorario_ch']." Historial Programacion ERROR => ".mysql_error()."on:<br>$query<br><br>";
                $query="DELETE `Cambios de Turno` WHERE id='$last_id'";
                mysql_query($query);
            }
        }
    }
    unset($index,$info);
}

//Current Schedules
switch($tipo){
    case 2:
    case 4:
        if(isset($_POST['Aplicar'])){$asesor2=$_POST['asesor4'];}
        $query="SELECT a.id as idH, Fecha, `jornada start`, `jornada end`, `comida start`, `comida end`, b.id, `N Corto`, Esquema, cambios FROM `Historial Programacion` a "
                ."LEFT JOIN Asesores b ON a.asesor=b.id "
                ."LEFT JOIN
                	(
                		SELECT id_asesor as asesor, COUNT(*) as cambios FROM `Cambios de Turno` WHERE tipo IN (1,2) AND id_asesor IN ('$asesor1','$asesor2') AND MONTH(Fecha)=".date('m',strtotime($fecha1))." GROUP BY id_asesor
                	) c ON a.asesor=c.asesor "
                ."WHERE a.`asesor` IN ('$asesor1','$asesor2') AND `Fecha` IN ('$fecha1','$fecha2')";
        break;
    case 1:
        $query="SELECT a.id as idH, Fecha, `jornada start`, `jornada end`, `comida start`, `comida end`, b.id, `N Corto`, Esquema, cambios FROM `Historial Programacion` a "
                ."LEFT JOIN Asesores b ON a.asesor=b.id "
                ."LEFT JOIN
                	(
                		SELECT id_asesor as asesor, COUNT(*) as cambios FROM `Cambios de Turno` WHERE tipo IN (1,2) AND id_asesor IN ('$asesor1','$asesor2') AND MONTH(Fecha)=".date('m',strtotime($fecha1))." GROUP BY id_asesor
                	) c ON a.asesor=c.asesor "
                ."WHERE a.`asesor` IN ('$asesor1','$asesor2') AND `Fecha` IN ('$fecha1')";
        break;
    case 3:
        $query="SELECT a.id as idH, Fecha,  `jornada start`, `jornada end`, `comida start`, `comida end`, b.id, `N Corto`, Esquema, cambios FROM `Historial Programacion` a "
                ."LEFT JOIN Asesores b ON a.asesor=b.id "
                ."LEFT JOIN
                	(
                		SELECT id_asesor as asesor, COUNT(*) as cambios FROM `Cambios de Turno` WHERE tipo IN (1,2) AND id_asesor='$asesor1' AND MONTH(Fecha)=".date('m',strtotime($fecha1))." GROUP BY id_asesor
                	) c ON a.asesor=c.asesor "
                ."WHERE a.`asesor`='$asesor1' AND `Fecha`='$fecha1'";
        break;
}
$result=mysql_query($query);
$num=mysql_numrows($result);
for($i=0;$i<$num;$i++){
    $current[mysql_result($result,$i,'id')][mysql_result($result,$i,'Fecha')]['Asesor']=mysql_result($result,$i,'N Corto');
    $current[mysql_result($result,$i,'id')][mysql_result($result,$i,'Fecha')]['Esquema']=mysql_result($result,$i,'Esquema');
    $current[mysql_result($result,$i,'id')][mysql_result($result,$i,'Fecha')]['idHorario']=mysql_result($result,$i,'idH');
    $current[mysql_result($result,$i,'id')][mysql_result($result,$i,'Fecha')]['jstart']=mysql_result($result,$i,'jornada start');
    $current[mysql_result($result,$i,'id')][mysql_result($result,$i,'Fecha')]['cstart']=mysql_result($result,$i,'comida start');
    $current[mysql_result($result,$i,'id')][mysql_result($result,$i,'Fecha')]['jend']=mysql_result($result,$i,'jornada end');
    $current[mysql_result($result,$i,'id')][mysql_result($result,$i,'Fecha')]['cend']=mysql_result($result,$i,'comida end');
    $current[mysql_result($result,$i,'id')][mysql_result($result,$i,'Fecha')]['cambios']=mysql_result($result,$i,'cambios');

    $fecha[mysql_result($result,$i,'Fecha')]=1;

}

//dates
foreach($fecha as $date => $info){
    $fechas[]=$date;
}
unset($date,$info);

//asesores
foreach($current as $asesor => $info){
    $asesores[]=$asesor;
}
unset($asesor,$info);

//Casos Cambios
$query="SELECT id_asesor as asesor, caso FROM `Cambios de Turno` WHERE tipo IN (1,2) AND id_asesor IN ('$asesor1','$asesor2') AND MONTH(Fecha)=".date('m',strtotime($fecha1));
$result=mysql_query($query);
$num=mysql_numrows($result);
for($i=0;$i<$num;$i++){
    $ch_cases[mysql_result($result,$i,'asesor')][]=mysql_result($result,$i,'caso');
}




//include("../common/menu.php");
?>
<link rel="stylesheet" href="/js/tpicker2/jquery.ui.timepicker.css">
<script type="text/javascript" src="/js/tpicker2/jquery.ui.timepicker.js"></script>
<script>

$(function(){
    //Set Datepicker
    $('#fecha1, #fecha2').datepicker();

    //Init format
    pcrc_change($('#pcrc_select').val(),1);
    type_change($('#tipo').val(),1);

    //Tooltip for current changes
    $( ".total_changes" ).tooltip({
        track: true,
        show: {
            effect: "slideDown",
            delay: 250
        }
    });

    //Asesores depending on Departamento
    function pcrc_change(value, inicio = 0){
        var departamento=value;
        if(inicio==0){
            $('#asesor1, #asesor2').val("");
        }
        $('.asesor').hide();
        $('.dep'+departamento).show();
    }
    $('#pcrc_select').change(function(){
        pcrc_change($(this).val());
    });

    //number of options depending on type selection
    function type_change(value, inicio = 0){
        var tipo=value;
        switch(tipo){
            case '1':
                $('#date2row').show();
                $('#fecha2').hide();
                if(inicio==0){
                    $('#fecha2, #asesor2').val('');
                }
                break;
            case '3':
                $('#date2row').hide();
                if(inicio==0){
                    $('#fecha2, #asesor2').val('');
                }
                break;
            case '4':
                $('#date2row').show();
                $('#fecha2').hide();
                if(inicio==0){
                    $('#fecha2, #asesor2').val('');
                }
                break;
            default:
                $('#date2row').show();
                $('#fecha2').show();
                if(inicio==0){
                    $('#fecha2, #asesor2').val('');
                }
                break;
        }

    }
    $('#tipo').change(function(){
        type_change($(this).val());
    });

    //format timepicker
    $('.timepicker').timepicker();

    //TEST
    $('#testb').click(function(){
        var hora=$('#js_new1').val();
        var h=hora.match("(.*):");
        var m=hora.match(":(.*)");
        var hnew, mnew;
        hnew=parseInt(h[1])+8;
        mnew=parseInt(m[1])+30;
        if(mnew==60){mnew="00";}
        var hm=hnew+":"+mnew;
        alert(hm);
    });

    //Automatic Calculate End Schedule
    $('.startchange').change(function(){
        var esquema=$(this).attr('esquema');
        var indice=$(this).attr('indice');
        changehour(indice,esquema);
    })

    //Change Our Function
    function changehour(i,esq){

        //get and declare
        var hora=$('#js_new'+i).val();
        var h=hora.match("(.*):");
        var m=hora.match(":(.*)");
        var hnew, mnew;

        //Operations vs Esquema
        switch(esq){
            case 8:
                mnew = parseInt(m[1]);
                if(parseInt(h[1])>=16){
        			if(parseInt(h[1])==16 && parseInt(m[1])<30){
        				esq=parseInt(esq)-1;
        				if(parseInt(m[1])>30){
        					mnew=parseInt(m[1])-30;
        				}else{
        					mnew=parseInt(m[1])+30;
        				}
        			}else{
        			esq=parseInt(esq)-1;
        			mnew=parseInt(m[1]);
        			}
        		}else if (parseInt(h[1])>=12){
        			if(parseInt(h[1])!=12){
        				esq=parseInt(esq)-1;
        				if(parseInt(m[1])>=30){
        					mnew=parseInt(m[1])-30;
        				}else{
        					mnew=parseInt(m[1])+30;
        				}
        			} else if (parseInt(m[1])>0){
        				esq=parseInt(esq)-1;
        				if(parseInt(m[1])>=30){
        					mnew=parseInt(m[1])-30;
        				}else{
        					mnew=parseInt(m[1])+30;
        				}
        			}
        		}
                hnew = parseInt(h[1]) + parseInt(esq);
                break;
            default:
                hnew = parseInt(h[1]) + parseInt(esq);
        		mnew = parseInt(m[1]);
                break;
        }

        if (hnew>=24){hnew=hnew-24;}
        if (hnew<10){hnew="0" + hnew;}
        if (mnew<10){mnew="0" + mnew;}

        $("#je_new"+i).val(hnew+":"+mnew);
   }

});

</script>

<style>
    .ui-tooltip {
    width: 60px;
    height: auto;      h
    padding: 10px 20px;
    color: black;
    border-radius: 20px;
    font: bold 14px "Helvetica Neue", Sans-Serif;
    text-transform: uppercase;
    text-align: center;
    box-shadow: 0 0 7px black;
  }

  .timepicker{
    width: 60px;
  }

</style>

<table  class='t2' style='width: 80%; margin: auto;'>
<form method='POST' action='<?php $_SERVER['PHP_SELF']; ?>' name="Cambios" id="cambios">
	<tr class='title'>
		<th colspan=5>Parametros</th>
	</tr>
	<tr>
		<td class='subtitle'>Departamento:</td>
		<td  class='pair'><select name='dep' id="pcrc_select"><option value="">Selecciona...</option>
        <?
            $query="SELECT * FROM PCRCs ORDER BY Departamento";
            $result=mysql_query($query);
            $num=mysql_numrows($result);
            for($i=0;$i<$num;$i++){
                if($dep==mysql_result($result,$i,'id')){$selected="selected";}else{$selected="";}
                echo "<option value='".mysql_result($result,$i,'id')."' $selected>".mysql_result($result,$i,'Departamento')."</option>\n\t";
            }

        ?></select></td>
		<td class='subtitle'>Tipo de Cambio:</td>
		<td class='pair'><select name='tipo' id="tipo"><option value="">Selecciona...</option><option value='1' <? if($tipo==1){ echo "selected"; }?>>Turno</option><option value='2' <? if($tipo==2){ echo "selected"; }?>>Descanso</option><option value='3' <? if($tipo==3){ echo "selected"; }?>>Ajuste</option><option value='4' <? if($tipo==4){ echo "selected"; }?>>Ajuste Turnos</option></select></td>
		<td rowspan=3  class='total'><input type='submit' name='consulta' value='Consultar'></td>
	</tr>
	<tr>
		<td class='subtitle'>Asesor 1</td>
		<td class='odd'><select name='asesor1' id="asesor1"><option value="">Selecciona...</option>
        <?
        $query="SELECT * FROM Asesores WHERE Activo=1 ORDER BY `N Corto`";
            $result=mysql_query($query);
            $num=mysql_numrows($result);
            for($i=0;$i<$num;$i++){
                if($asesor1==mysql_result($result,$i,'id')){$selected="selected";}else{$selected="";}
                echo "<option class='asesor dep".mysql_result($result,$i,'id Departamento')."' value='".mysql_result($result,$i,'id')."' $selected>".mysql_result($result,$i,'N Corto')."</option>\n\t";
            }

        ?></select></td>
		<td class='subtitle'>Fecha 1:</td>
		<td class='odd'><input type='text' name='fecha1' id='fecha1' value='<? echo $fecha1; ?>'/></td>
	</tr>
    <tr id='date2row'>
		<td class='subtitle'>Asesor 2</td>
		<td class='odd'><select name='asesor2' id="asesor2"><option value="">Selecciona...</option>
        <?
        $query="SELECT * FROM Asesores WHERE Activo=1 ORDER BY `N Corto`";
            $result=mysql_query($query);
            $num=mysql_numrows($result);
            for($i=0;$i<$num;$i++){
                if($asesor2==mysql_result($result,$i,'id')){$selected="selected";}else{$selected="";}
                echo "<option class='asesor dep".mysql_result($result,$i,'id Departamento')."' value='".mysql_result($result,$i,'id')."' $selected>".mysql_result($result,$i,'N Corto')."</option>\n\t";
            }

        ?></select></td>
		<td class='subtitle'>Fecha 2:</td>
		<td class='odd'><input type='text' name='fecha2' id='fecha2' value='<? echo $fecha2; ?>'/></td>
	</tr>

</form>
</table>

<?php

if(isset($_POST['Aplicar'])){
    if(count($error)>0){
        echo "<div style='background: red; color: white; font-size:20px; text-align: center; margin: auto; width: 80%;'>";
            foreach($error as $index => $info){
                echo "$info";
            }
        echo "</div>;";
        exit;
    }else{
        echo "<br><br><div style='background: green; color: white; font-size:20px; text-align: center; margin: auto; width: 80%; height: 200px;'>";
        echo "Cambios Aplicados Correctamente!";
        echo "</div>;";
        exit;
    }
}elseif(!isset($_POST['consulta'])){
    exit;
}

?>
<br>

<table  class='t2' style='width: 80%; margin: auto;'>
	<tr class='title' >
		<th  colspan=9>Horario Actual</th>
	</tr>
	<tr class='subtitle'>
		<td>Id Horario</td>
        <td>Id Asesor</td>
		<td>Asesor</td>
		<td>Esquema</td>
		<td>Fecha</td>
		<td>Horario</td>
        <td>Comida</td>
		<td>Cambios en<br>el mes</td>
    </tr>
    <?php
        $i=0;
        foreach($current as $idasesor => $info){
            $x=1;
            foreach($info as $date => $info2){
                if($date=='Asesor'){continue;}

                //Class selector
                if($i % 2 == 0){$class='pair';}else{$class='odd';}

                //Format number of changes
                if($info2['cambios']==NULL){$cambios=0;}else{$cambios=$info2['cambios'];}

                //Tooltip casos
                $tooltip_tmp="";
                foreach($ch_cases[$idasesor] as $index => $infocaso){
                    $tooltip_tmp.=$infocaso." ";
                }
                $tooltip="title='$tooltip_tmp'";
                unset($index,$infocaso);

                //Format Descansos
                if($info2['jstart']==$info2['jend']){
                    $jornada="Descanso";
                }else{
                    $jornada= $info2['jstart']." - ".$info2['jend'];
                }

                //Format Comida
                if($info2['cstart']==$info2['cend']){
                    $comida="NA";
                }else{
                    $comida= $info2['cstart']." - ".$info2['cend'];
                }

                //Print Table
                echo "<tr class='$class' id='current".($x)."'>\n\t";
                echo "<td>".$info2['idHorario']."</td>\n\t"
                    ."<td>$idasesor</td>\n\t"
                    ."<td>".$info2['Asesor']."</td>\n\t"
                    ."<td>".$info2['Esquema']."</td>\n\t"
                    ."<td>".$date."</td>\n\t"
                    ."<td>".$jornada."</td>\n\t"
                    ."<td>".$comida."</td>\n\t"
                    ."<td class='total total_changes' $tooltip>".$cambios."</td>";
                echo "</tr>";
                $i++;
            }
            unset($date,$info2);
            $x++;
        }
        unset($idasesor,$info);
    ?>
</table>
<br>
<table  class='t2' style='width: 80%; margin: auto;'>
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
    <form name='cambio' action='' method='POST' action='<?php $_SERVER['PHP_SELF']; ?>'>
    <?php
        $i=1;
        foreach($current as $asesor => $info){
            foreach($info as $date => $info2){

                //Asesor Change
                if($asesor==$asesores[0]){
                    $asesor_change=$asesores[1];
                }else{
                    $asesor_change=$asesores[0];
                }

                //Date Change
                if($date==$fechas[0]){
                    $fecha_change=$fechas[1];
                }else{
                    $fecha_change=$fechas[0];
                }

                if($tipo==3){
                     $fecha_change=$date;
                     $asesor_change=$asesor;
                }
                echo "<tr class='pair'>\n\t";
                    echo "<td>".$asesor."</td>\n\t";
                    echo "<td>".$info2['Asesor']."</td>\n\t";
                    echo "<td>".$info2['Esquema']."</td>\n\t";
                    echo "<td>".$date."</td>\n\t";
                    echo "<td><input type='text' indice='$i' esquema='".$info2['Esquema']."' name='js_new$i' id='js_new$i' value='".date('H:i',strtotime($current[$asesor_change][$date]['jstart']))."' class='timepicker startchange'></td>
                    		<td><input type='text' name='je_new$i' id='je_new$i' value='".date('H:i',strtotime($current[$asesor_change][$date]['jend']))."' class='timepicker'></td>
                    		<td><input type='text' name='cs_new$i' id='cs_new$i' value='".date('H:i',strtotime($current[$asesor_change][$date]['cstart']))."' class='timepicker'></td>
                    		<td><input type='text' name='ce_new$i' id='ce_new$i' value='".date('H:i',strtotime($current[$asesor_change][$date]['cend']))."' class='timepicker'></td>";
                echo "</tr>\n";

                //Hidden fields
                echo "<input type='hidden' name='asesor$i' id='asesor_ch$i' value='$asesor'>\n\t";
                echo "<input type='hidden' name='asesorch$i' id='asesor_ch2$i' value='$asesor_change'>\n\t";
                echo "<input type='hidden' name='fecha$i' id='fecha_ch$i' value='$date'>\n\t";
                echo "<input type='hidden' name='idHorario$i' id='idHorario_ch$i' value='".$current[$asesor][$date]['idHorario']."'>\n\t";
                echo "<input type='hidden' name='js_old$i' id='js_old$i' value='".$current[$asesor][$date]['jstart']."'>\n\t";
                echo "<input type='hidden' name='je_old$i' id='je_old$i' value='".$current[$asesor][$date]['jend']."'>\n\t";
                echo "<input type='hidden' name='cs_old$i' id='cs_old$i' value='".$current[$asesor][$date]['cstart']."'>\n\t";
                echo "<input type='hidden' name='ce_old$i' id='ce_old$i' value='".$current[$asesor][$date]['cend']."'>\n\t";

                $i++;
            }
        }
        unset($asesor,$info);

        //Hidden fields
        echo "<input type='hidden' name='dep' id='dep_ch' value='$dep'>\n\t";
        echo "<input type='hidden' name='tipo' id='tipo_ch' value='$tipo'>\n\t";
    ?>
    <tr class='total'>
		<td colspan=4>Caso: <input type='text' name='caso' size=7 required></td>
		<td colspan=4><input type='submit' value='Aplicar' name='Aplicar'></td>
	</tr>
    </form>
</table>


