<?php

session_start();

$this_page=$_SERVER['PHP_SELF'];

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}

date_default_timezone_set('America/Mexico_City');

$credential="reportes_aleatoriedad";

$menu_programaciones="class='active'";



include("../connectDB.php");



//GET Variables

if(isset($_POST['inicio'])){$inicio=date('Y-m-d',strtotime($_POST['inicio']));}else{$inicio=date('Y-m-d', strtotime('-14 days'));}

if(isset($_POST['fin'])){$final=date('Y-m-d',strtotime($_POST['fin']));}else{$final=date('Y-m-d');}

$tipo=$_POST['tipo'];

$tx=$_POST['tx'];

$nivel=$_POST['nivel'];

$cant=$_POST['cantidad'];

$nivel_opt=$_POST['nivel_opt'];



function listOps($variable){



    $queryProgs="SELECT * FROM PCRCs WHERE Parent=1 ORDER BY Departamento";

    $querySups="SELECT * FROM Asesores WHERE `id Departamento`=12 ORDER BY `N Corto`";

    $resultProgs=mysql_query($queryProgs);

    $numProgs=mysql_numrows($resultProgs);

    echo "<option value'' title='Programa'>Selecciona...</option>";



    $i=0;

    while($i<$numProgs){

        if($variable==mysql_result($resultProgs,$i,'id')){$selected="selected";}else{$selected="";}

        echo "<option value='".mysql_result($resultProgs,$i,'id')."' title='Programa' $selected>".mysql_result($resultProgs,$i,'Departamento')."</option>";



    $i++;

    }

    $resultSups=mysql_query($querySups);

    $numSups=mysql_numrows($resultSups);

    echo "<option value'' title='Supervisor'>Selecciona...</option>";



    $i=0;

    while($i<$numSups){

        if($variable==mysql_result($resultSups,$i,'id')){$selected="selected";}else{$selected="";}

        echo "<option value='".mysql_result($resultSups,$i,'id')."' title='Supervisor' $selected>".mysql_result($resultSups,$i,'N Corto')."</option>";



    $i++;

    }

}





include("../common/scripts.php");

?>

<script>

$(function(){



   $( "#inicio" ).datepicker({

      defaultDate: "-1w",

      changeMonth: true,

      numberOfMonths: 3,

      onClose: function( selectedDate ) {

        $( "#fin" ).datepicker( "option", "minDate", selectedDate );

      }

    });

    $( "#fin" ).datepicker({

      defaultDate: "-1w",

      changeMonth: true,

      numberOfMonths: 3,

      onClose: function( selectedDate ) {

        $( "#inicio" ).datepicker( "option", "maxDate", selectedDate );

      }

    });



    $('#nivel').change(function(){

         if($(this).data('options') == undefined){

            /*Taking an array of all options-2 and kind of embedding it on the select1*/

            $(this).data('options',$('#nivel_opt option').clone());

            }

            var act_id = $(this).val();

        var options = $(this).data('options').filter('[title=' + act_id + ']');

        $('#nivel_opt').html(options);

        $('#nivopt').show();

        $('#nivopttitle').show();





    });



});

</script>

<?php



include("../common/menu.php");

?>

<table width='100%' class='t2'><form action="<?php $_SERVER['PHP_SELF']; ?>" method='POST'>

    <tr class='title'>

        <th colspan=100>Reporte de Aleatoriedad</th>

    </tr>

    <tr class='title'>

        <td>Inicio</td>

        <td>Final</td>

        <td>Tipo</td>

        <td>Nivel</td>

        <td  id='nivopttitle'>Sup/Prog</td>

        <td>Cantidad</td>

        <td rowspan=2 class='total'><input type="submit" name='consulta' id='consulta' value='Consultar'/></td>

    </tr>

    <tr class='title'>

        <td class='pair'><input type='text' name='inicio' id='inicio' value='<?php echo $inicio; ?>' required/></td>

        <td class='pair'><input type='text' name='fin' id='fin' value='<?php echo $final; ?>' required /></td>

        <td class='pair'><select name="tipo" id="tipo" required>

            <option value="">Selecciona...</option>

            <option value="1" selected>Calidad</option>



        </select></td>

        <td class='pair'><select name="nivel" id="nivel" required>

        <option value="">Selecciona...</option>

        <option value="Programa" <?php if($nivel=='Programa'){echo "selected";} ?>>Programa</option>

        <option value="Supervisor" <?php if($nivel=='Supervisor'){echo "selected";} ?>>Supervisor</option>

        </select></td>

        <td class='pair' id='nivopt'><select name="nivel_opt" id="nivel_opt"><?php listOps($nivel_opt); ?></select></td>

        <td class='pair'><input type="number" name='cantidad' id='cantidad' value='<?php echo $cant; ?>' required/></td>



    </tr>

</form></table>

<br><br>

<?php if(!isset($_POST['consulta'])){exit;}



if($tipo==1){



    switch($nivel){

        case 'Programa':

            $query="SELECT * FROM PCRCs WHERE id=$nivel_opt";
            $programa=mysql_result(mysql_query($query),0,'Departamento');
            if($nivel_opt==6){
                $queryCalls="SELECT localizador as Fecha FROM bo_reembolsos
            	WHERE date_created>='$inicio' AND date_created<=DATE_ADD('$final', INTERVAL 1 DAY)";
            }else{
                $queryCalls="SELECT Fecha, Llamante, Hora, AsteriskID FROM t_Answered_Calls a, Cola_Skill b, Asesores c
                WHERE a.Cola=b.Cola AND a.asesor=c.id AND Fecha>='$inicio' AND Fecha<='$final'
                AND b.Skill=$nivel_opt";
            }
            $resultCalls=mysql_query($queryCalls);
            $numCalls=mysql_numrows($resultCalls);
            $i=0;

            while($i<$numCalls){

                $call[$i]=mysql_result($resultCalls,$i,'Fecha');

                $call_caller[$i]=mysql_result($resultCalls,$i,'Llamante');

                $call_asterisk[$i]=mysql_result($resultCalls,$i,'AsteriskID');

                $call_hora[$i]=mysql_result($resultCalls,$i,'Hora');

            $i++;

            }

            $queryCasos="SELECT caso FROM bo_casos

            WHERE Fecha_registro>='$inicio' AND Fecha_registro<='$final' AND programa='$nivel_opt'";

            $resultCasos=mysql_query($queryCasos);

            $numCasos=mysql_numrows($resultCasos);

            $i=0;

            while($i<$numCasos){

                $caso[$i]=mysql_result($resultCasos,$i,'caso');

            $i++;

            }

            $tabla="<tr class='pair'><td width='10%'>$programa</td>";

            $tablacaso="<tr class='pair'><td width='10%'>$programa</td>";

            $i=1;

            while($i<=$cant){

                $temp=rand(0,$numCasos-1);

                $tablacaso.="<td>$caso[$temp]</td>";

            $i++;

            }

            $i=1;

            while($i<=$cant){

                $temp=rand(0,$numCalls-1);

                $tabla.="<td>$call[$temp] $call_hora[$temp]<br>$call_caller[$temp]<br>$call_asterisk[$temp]</td>";

            $i++;

            }

            $tabla.="</tr>";

            $tablacaso.="</tr>";

            break;

        case 'Supervisor':

            $query="SELECT * FROM Asesores WHERE id=$nivel_opt";

            $super=mysql_result(mysql_query($query),0,'N Corto');

            $queryCalls="SELECT * FROM (SELECT asesor, `N Corto`, AsteriskID, Fecha, Llamante, Hora, FindSuper(".date('m',strtotime($final)).",".date('Y',strtotime($final)).",a.asesor) as Super

            FROM t_Answered_Calls a, Cola_Skill b, Asesores c

            WHERE a.Cola=b.Cola AND a.asesor=c.id AND Fecha>='$inicio' AND Fecha<='$final' AND a.asesor=c.id) as calls WHERE Super='$super' ORDER BY `N Corto`";

            $resultCalls=mysql_query($queryCalls);

            $numCalls=mysql_numrows($resultCalls);

            $i=0;

            $flag=0;

            while($i<$numCalls){


                if($ases[$flag]!=mysql_result($resultCalls,$i,'N Corto')){
                    $maxcall[$flag]=$i-1;
                    $flag++;
                    $ases[$flag]=mysql_result($resultCalls,$i,'N Corto');
                    $mincall[$flag]=$i;
                }

                $call[$i]=mysql_result($resultCalls,$i,'Fecha');
                $call_caller[$i]=mysql_result($resultCalls,$i,'Llamante');
                $call_asterisk[$i]=mysql_result($resultCalls,$i,'AsteriskID');
                $call_hora[$i]=mysql_result($resultCalls,$i,'Hora');

            $i++;
            }

            $queryCasos="SELECT * FROM (SELECT caso, asesor, FindSuper(".date('m',strtotime($final)).",".date('Y',strtotime($final)).",asesor) as Super, `N Corto` FROM

        	(SELECT caso, c.id as asesor, `N Corto` FROM bo_casos a, userDB b, Asesores c

            WHERE a.`user`=b.userid AND b.username=c.Usuario AND Fecha_registro>='$inicio' AND Fecha_registro<='$final'

            ) as casos) as temp WHERE Super='$super' ORDER BY `N Corto`";

            $resultCasos=mysql_query($queryCasos);

            $numCasos=mysql_numrows($resultCasos);

            $i=0;

            $flag=0;

            while($i<$numCasos){

                if($i==0){$asescaso[0]=mysql_result($resultCasos,$i,'N Corto'); $mincaso[$flag]=$i;}

                if($asescaso[$flag]!=mysql_result($resultCasos,$i,'N Corto')){



                    $maxcaso[$flag]=$i-1;

                    $flag++;

                    $asescaso[$flag]=mysql_result($resultCasos,$i,'N Corto');

                    $mincaso[$flag]=$i;

                }

                if($i==$numCasos-1){$maxcaso[$flag]=$i;}

                $caso[$i]=mysql_result($resultCasos,$i,'caso');

            $i++;

            }

            foreach($ases as $key => $as){

                $tabla.="<tr class='pair'><td width='10%'>$as</td>";

                $i=1;

                while($i<=$cant){

                    $temp=rand($mincall[$key],$maxcall[$key]);

                    $tabla.="<td>$call[$temp] $call_hora[$temp]<br>$call_caller[$temp]<br>$call_asterisk[$temp]</td>";

                $i++;

                }



                $tabla.="</tr>";

            }

            unset($key,$as);

            foreach($asescaso as $key => $as){

                $tablacaso.="<tr class='pair'><td width='10%'>$as</td>";

                $i=1;

                while($i<=$cant){

                    $temp=rand($mincaso[$key],$maxcaso[$key]);

                    $tablacaso.="<td>$caso[$temp]</td>";

                $i++;

                }



                $tablacaso.="</tr>";

            }





            break;

    }

}



//echo "$queryCasos<br>";

?>



<table width='100%' class='t2'>

    <tr class='title'>

        <td>Programa /<br>Asesor</td>

        <?php

        $i=1;

        while($i<=$cant){
            if($nivel=='Programa' && $nivel_opt==6){$title="Localizador";}else{$title="Llamada";}
            echo "<td>$title $i</td>";

        $i++;

        }

        ?>

    </tr>

    <?php

        echo $tabla;

    ?>

</table>

<br><br>

<table width='100%' class='t2'>

    <tr class='title'>

        <td>Programa /<br>Asesor</td>

        <?php

        $i=1;

        while($i<=$cant){

            echo "<td>Caso $i</td>";

        $i++;

        }

        ?>

    </tr>

    <?php

        echo $tablacaso;

    ?>

</table>