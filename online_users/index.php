<?php
session_start();

$this_page=$_SERVER['PHP_SELF'];

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}

$credential="monitor_pya";

$menu_monitores="class='active'";

include("../connectDB.php");
date_default_timezone_set('America/Bogota');

$timenow=date('H:i:s', strtotime('now -7 minutes'));
$datenow=date('Y-m-d');

if(isset($_POST['fecha'])){$datenow=date('Y-m-d',strtotime($_POST['fecha']));}
if(isset($_POST['hora'])){
    $time=intval($_POST['hora']).":".(($_POST['hora']-intval($_POST['hora']))*60);
    $timenow=date('H:i:s',strtotime($time));
}

$hora=date('H',strtotime($timenow))+date('i',strtotime($timenow))/60;

$query="SELECT
	id, Nombre, Departamento, Inicio, Fin, C_Inicio, C_End,
	x1_Inicio, x1_Fin, x2_Inicio, x2_Fin,
	tipo_ausentismo, Hora_in, Hora_out
	FROM
		(
			SELECT
				a.id, `N Corto` as Nombre, Departamento
			FROM
				Asesores a
			LEFT JOIN
				PCRCs b
			ON
				a.`id Departamento`=b.id
			WHERE
				Activo=1
		) Asesores
    LEFT JOIN
		(
			SELECT
				asesor as Sesiones_asesor, ADDTIME(Hora, (IF(Verano=1,'00:00:00','01:00:00'))) as Hora_in, ADDTIME(MAX(Hora_out), (IF(Verano=1,'00:00:00','01:00:00'))) as Hora_out
			FROM
				Sesiones a
			LEFT JOIN
				Fechas b
			ON
				a.Fecha=b.Fecha
			WHERE
				a.Fecha='$datenow'
			GROUP BY
				asesor
		) Sesiones
	ON
		id=Sesiones_asesor
	LEFT JOIN
		(
			SELECT
				asesor as  Programacion_asesor, `jornada start` as Inicio, `jornada end` as Fin, `comida start` as C_Inicio, `comida end` as C_End,
				`extra1 start` as x1_Inicio, `extra1 end` as x1_Fin,`extra2 start` as x2_Inicio, `extra2 end` as x2_Fin
			FROM
				`Historial Programacion`
			WHERE
				Fecha='$datenow' AND
				('$timenow' BETWEEN `jornada start` AND if(`jornada end`<'04:00:00', ADDTIME(`jornada end`,'24:00:00'),`jornada end`) OR
				'$timenow' BETWEEN `extra1 start` AND if(`extra1 end`<'04:00:00', ADDTIME(`extra1 end`,'24:00:00'),`extra1 end`) OR
				'$timenow' BETWEEN `extra2 start` AND if(`extra2 end`<'04:00:00', ADDTIME(`extra2 end`,'24:00:00'),`extra2 end`))
		) Programacion
	ON
		id=Programacion_asesor
	LEFT JOIN
		(
			SELECT
				asesor as Ausentismos_asesor, tipo_ausentismo
			FROM
				Ausentismos
			WHERE
				'$datenow' BETWEEN Inicio AND Fin
		) Ausentismos
	ON
		id=Ausentismos_asesor
	HAVING
		Inicio!=Fin AND
		tipo_ausentismo IS NULL
	ORDER BY
		Departamento, Nombre";
$result=mysql_query($query);
$num=mysql_numrows($result);
//echo "$query<br>";
$i=0;
while($i<$num){
    $asesor[mysql_result($result,$i,'Departamento')][$i]=mysql_result($result,$i,'Nombre');
    $inicio[mysql_result($result,$i,'Departamento')][$i]=mysql_result($result,$i,'Inicio');
    $fin[mysql_result($result,$i,'Departamento')][$i]=mysql_result($result,$i,'Fin');
    $c_inicio[mysql_result($result,$i,'Departamento')][$i]=mysql_result($result,$i,'C_Inicio');
    $c_fin[mysql_result($result,$i,'Departamento')][$i]=mysql_result($result,$i,'C_End');
    $x1_inicio[mysql_result($result,$i,'Departamento')][$i]=mysql_result($result,$i,'x1_Inicio');
    $x1_fin[mysql_result($result,$i,'Departamento')][$i]=mysql_result($result,$i,'x1_Fin');
    $x2_inicio[mysql_result($result,$i,'Departamento')][$i]=mysql_result($result,$i,'x2_Inicio');
    $x2_fin[mysql_result($result,$i,'Departamento')][$i]=mysql_result($result,$i,'x2_Fin');
    $hora_out[mysql_result($result,$i,'Departamento')][$i]=mysql_result($result,$i,'Hora_out');
    if($timenow<date('H:i:s', strtotime($hora_out[mysql_result($result,$i,'Departamento')][$i])) AND $hora_out[mysql_result($result,$i,'Departamento')][$i]!=NULL){$online[mysql_result($result,$i,'Departamento')]++;}
$i++;
}

include("../common/scripts.php");
?>
<style>
.title{
    width:1523px;
    height:auto;
    margin:auto;
    background:#3280cd;
    color:white;
    text-align:center;
    font-size:14px;
    padding-top:10px;
    padding-bottom:10px;
    font-weight: bold;
}

.inside{
    width:1459px;
    height:auto;
    margin:auto;
    background:#80aaff;
    color:white;
    font-size:14px;
    padding:10px;
}

.line{
    width:1445px;
    height:42px;
    margin: 5px;
    padding: 10 0 0 10;
    border: dotted 1px white;
    background: #7ED8FC
}

.concept{
    width: 100px;
    height:25px;
    float:left;
    border-radius:6px;
    padding: 7 0 0 0;
    margin: 0 10 0 10;
    text-align: center;
}

.name{
    width: 210px;
    text-align: left;
    padding-left: 15px;
    background: #526894;
}

.nametitle{
    width: 210px;
    text-align: left;
    padding-left: 15px;
}

.outofschedule{
    background: #A71313;
}

.inactive{
    background: #D2D2D2;
    color: #929292;
}

.now{
    background: #F3840E;
}

.active{
    background: #33CC33;

}

</style>

<script>

$(function(){

    $("#accordion").accordion({
        heightStyle: 'content',
        collapsible: true,
        active: false
    });

});

</script>


<?php
include("../common/menu.php");
?>

<table class='t2' style='width:1523px; margin:auto;'><form action="<?php $_SERVER['PHP SELF']; ?>" method='POST'>
<tr class='title'>
    <th colspan=100>Asesores Conectados (<?php echo $timenow; ?>)</th>
</tr>
<tr class='title'>
    <td>Fecha</td>
    <td class='pair'><input type="text" value='<?php echo $datenow; ?>' name='fecha' required/></td>
    <td>Hora</td>
    <td class='pair'><input type="number" value='<?php echo $hora; ?>' name='hora' step='0.5' max='23.5' min='0' required/></td>
    <td><input type="submit" value="consultar"/></td>
</tr>
</form></table>
<br><br>
<div id="accordion" style='width:1523px; margin:auto;'>
<?php

foreach($asesor as $key => $data){
    if($online[$key]==NULL){$online[$key]=0;}
    echo "<h3>$key ($online[$key]/".count($data).")</h3>\n<div class='inside'>\n";
    echo "<div class='line' style='color: black'>
                <div class='concept nametitle' style='background-color: none;'>Nombre</div>
                <div class='concept'>Status</div>
                <div class='concept'>Last Log</div>
                <div class='concept'>Entrada</div>
                <div class='concept'>Salida</div>
                <div class='concept'>Extra 1 In</div>
                <div class='concept'>Extra 1 Out</div>
                <div class='concept'>Extra 2 In</div>
                <div class='concept'>Extra 2 Out</div>
                <div class='concept'>Comida In</div>
                <div class='concept'>Comida Out</div>
            </div>";
    foreach($data as $key2 => $info){
        if($fin[$key][$key2]<date('H:i:s',strtotime('04:00:00'))){
            $out=date('H:i:s',strtotime("23:59:59"));
        }else{
            $out=$fin[$key][$key2];
        }
        $timeout=date('H:i:s', strtotime($hora_out[$key][$key2]));
        if($hora_out[$key][$key2]!=NULL){

            if($timenow>$timeout){
                $status="offline";
                $class="outofschedule";
            }else{
                $status="online";
                $class="active";
            }
        }else{
            $status="offline";
            $class="outofschedule";
            $hora_out[$key][$key2]="No Login";
        }

        if($timenow>=$inicio[$key][$key2] AND $timenow<$out){
            $jornada_class="now";
            $x1_class="inactive";
            $x2_class="inactive";
        }elseif($timenow>=$x1_inicio[$key][$key2] AND $timenow<$x1_fin[$key][$key2]){
            $jornada_class="inactive";
            $x1_class="now";
            $x2_class="inactive";
        }elseif($timenow>=$x2_inicio[$key][$key2] AND $timenow<$x2_fin[$key][$key2]){
            $jornada_class="inactive";
            $x1_class="inactive";
            $x2_class="now";
        }else{
            $jornada_class="inactive";
            $x1_class="inactive";
            $x2_class="inactive";
        }
        
 if($timenow>=$c_inicio[$key][$key2] AND $timenow<$c_fin[$key][$key2]){
            $comida_class="now";
        }else{
            $comida_class="inactive";
        }

        echo "<div class='line'>
                <div class='concept name'>$info</div>
                <div class='concept $class'>$status</div>
                <div class='concept $class'>".$hora_out[$key][$key2]."</div>
                <div class='concept $jornada_class'>".$inicio[$key][$key2]."</div>
                <div class='concept $jornada_class'>".$fin[$key][$key2]."</div>
                <div class='concept $x1_class'>".$x1_inicio[$key][$key2]."</div>
                <div class='concept $x1_class'>".$x1_fin[$key][$key2]."</div>
                <div class='concept $x2_class'>".$x2_inicio[$key][$key2]."</div>
                <div class='concept $x2_class'>".$x2_fin[$key][$key2]."</div>
                <div class='concept $comida_class'>".$c_inicio[$key][$key2]."</div>
                <div class='concept $comida_class'>".$c_fin[$key][$key2]."</div>
            </div>";
    }
    echo "</div>\n";
}

?>
</div>