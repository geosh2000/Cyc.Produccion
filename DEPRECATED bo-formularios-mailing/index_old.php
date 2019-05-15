<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="asesor_formularios_bo";
$menu_asesores="class='active'";

?>

<?php
include("../connectDB.php");
header("Content-Type: text/html;charset=utf-8");
//GET variables
$err_count=0;
$sel_actividad=$_POST['actividad'];
if(isset($_POST['seguimiento'])){$sel_seguimiento="'".$_POST['seguimiento']."'";}else{$sel_seguimiento="NULL";}

if($_POST['rango']!=""){
    $tmp_cases=str_replace("Caso",'',$_POST['rango']);
    //$tmp_cases=str_replace("Case",'',$tmp_cases);
    $casos=explode(' ',$tmp_cases);
    $flag=1;

}else{
    if($_POST['em']!=""){$sel_caso="'".$_POST['em']."'";}else{$sel_caso="NULL";}
}
if($_POST['localizador']!=""){$sel_loc="'".$_POST['localizador']."'";}else{$sel_loc="NULL";}
if($_POST['fcc']!=""){$sel_fcc="'".$_POST['fcc']."'";}else{$sel_fcc="NULL";}
if($_POST['fecha_asignacion']!=""){$sel_fecha="'".date('Y-m-d',strtotime($_POST['fecha_asignacion']))."'";}else{$sel_fecha="NULL";}
$sel_hora=$_POST['hr_asignacion'];
$sel_minuto=$_POST['min_asignacion'];
$hora=str_pad((int) $sel_hora,2,"0",STR_PAD_LEFT).":"
          .str_pad((int) $sel_minuto,2,"0",STR_PAD_LEFT).":00";

if($_POST['hr_asignacion']!=""){$sel_hora="'".date('H:i:s',strtotime($hora))."'";}else{$sel_hora="NULL";}

$sel_user=$_SESSION['id'];


//List Actividades
$query="SELECT * FROM bo_actividades WHERE area=2 ORDER BY actividad";
$result=mysql_query($query);
$num=mysql_numrows($result);
$i=0;
while($i<$num){
    $act_id[$i]=mysql_result($result,$i,'bo_act_id');
    $actividad[$i]=mysql_result($result,$i,'actividad');
$i++;
}

//List Seguimientos
$query="SELECT * FROM bo_seguimiento WHERE area=2 ORDER BY actividad, seguimiento";
$result=mysql_query($query);
$num=mysql_numrows($result);
$i=0;
while($i<$num){
    $seg_id[$i]=mysql_result($result,$i,'bo_seguimiento_id');
    $seguimiento[$i]=mysql_result($result,$i,'seguimiento');
    $seg_act[$i]=mysql_result($result,$i,'actividad');
$i++;
}


include("../common/scripts.php");

?>

<script>
$(function() {
    tips = $( ".validateTips" );
    $( "#picker" ).datepicker({
        altField: "#fecha_asignacion"
    });
    $( "#tabSeg1" ).hide();
    $( "#tabSeg2" ).hide();
    $( "#tabEM1" ).hide();
    $( "#tabRango1" ).hide();
    $( "#tabLoc1" ).hide();
    $( "#tabLoc2" ).hide();
    $( "#tabAsig1" ).hide();
    $( "#tabAsig2" ).hide();
    $( "#tabAsig3" ).hide();

      function updateTips( t ) {
      if(t==""){
            tips
            .text( t )
            .removeClass( "ui-state-highlight");
        } else{
      tips
        .text( t )
        .addClass( "ui-state-highlight" );
      setTimeout(function() {
        tips.removeClass( "ui-state-highlight", 1500 );
      }, 500 );}
    }

    function checkLength( o, n, min, max ) {
      if ( o.val().length > max || o.val().length < min ) {
        o.addClass( "ui-state-error" );
        updateTips( "Length of " + n + " must be between " +
          min + " and " + max + "." );
        return false;
      } else {
          updateTips( "" );
          o.removeClass( "ui-state-error" );

          return true;
      }
    }

    function checkRegexp( o, regexp, n ) {
      if ( !( regexp.test( o.val() ) ) ) {
        o.addClass( "ui-state-error" );
        updateTips( n );
        return false;
      } else {
        o.removeClass( "ui-state-error" );

        updateTips("");
        return true;
      }
    }

    function hideall(){
        $( "#tabSeg1" ).hide();
        $( "#tabSeg2" ).hide();
        $( "#tabEM1" ).hide();
        $( "#tabRango1" ).hide();
        $( "#tabLoc1" ).hide();
        $( "#tabLoc2" ).hide();
        $( "#tabAsig1" ).hide();
        $( "#tabAsig2" ).hide();
        $( "#tabAsig3" ).hide();
        $( "#seguimiento" ).attr('required',false);
        document.getElementById('seguimiento').value="";
        document.getElementById('fecha_asignacion').value="";
        document.getElementById('hr_asignacion').value="";
        document.getElementById('min_asignacion').value="";
        $( "#em" ).attr('required',false);
         $( "#fecha_asignacion" ).attr('required',false);
          $( "#hr_asignacion" ).attr('required',false);
           $( "#min_asignacion" ).attr('required',false);
        document.getElementById('em').value="";
        $( "#localizador" ).attr('required',false);
        document.getElementById('localizador').value="";
        $( "#rango" ).attr('required',false);
        document.getElementById('rango').value="";

    }

    function activate(Tabid, id, required){
        $("#"+Tabid+"1").show();
        $("#"+Tabid+"2").show();
        $("#"+Tabid+"3").show();
        if(required==1){
            $( "#"+id ).attr('required',true);
            if(Tabid=='TabAsign'){
                $( "#fecha_asignacion" ).attr('required',true);
                $( "#hr_asignacion" ).attr('required',true);
                $( "#min_asignacion" ).attr('required',true);
            }
        }


    }

    function deactivate(Tabid, id){
        $("#"+Tabid+"1").hide();
        $("#"+Tabid+"2").hide();
        $("#"+Tabid+"3").hide();
        $( "#"+id ).attr('required',false);
        if(Tabid=='TabAsign'){
            $( "#fecha_asignacion" ).attr('required',false);
            $( "#hr_asignacion" ).attr('required',false);
            $( "#min_asignacion" ).attr('required',false);
        }else{
            document.getElementById(id).value="";
        }

    }

    $('#actividad').change(function(){
        hideall();
        if($(this).data('options') == undefined){
            /*Taking an array of all options-2 and kind of embedding it on the select1*/
            $(this).data('options',$('#seguimiento option').clone());
            }
        var act_id = $(this).val();
        var options = $(this).data('options').filter('[title=' + act_id + ']');
        $('#seguimiento').html(options);
        if(act_id!=''){
            activate('tabAsig','fecha_asignacion',1);
            activate('tabEM','em',1);
            if(act_id=='4' || act_id=='8'){
                deactivate('tabEM','em');
                activate('tabRango','rango',1);
            }else{
                activate('tabEM','em',1);
                deactivate('tabRango','rango');
            }
        }else{
            deactivate('tabAsig','fecha_asignacion');
            deactivate('tabEM','em');
        }
    });

    function submitForm(){
        var submit_em=$('#em').attr('required');
        var submit_rango=$('#rango').attr('required');
        var submit_fa=$('#fecha_asignacion').attr('required');
        var submit_ha=$('#hr_asignacion').attr('required');
        var submit_ma=$('#min_asignacion').attr('required');
        var field_em=$('#em');
        var field_act=$('#actividad');
        var field_rango=$('#rango');
        var field_fa=$('#fecha_asignacion');
        var field_ha=$('#hr_asignacion');
        var field_ma=$('#min_asignacion');
         var field_val=true;
        if(submit_em=='required' || field_em.val()!=""){
            field_val= field_val && checkLength( field_em, "EM", 6, 7 );
            field_val= field_val && checkRegexp( field_em, /[0-9][0-9][0-9][0-9][0-9][0-9]/, "El formato solicitado para EM acepta solo numeros (0 al 9)" );
        }
        if(submit_rango=='required' || field_rango.val()!=""){
            field_val= field_val && checkLength( field_rango, "EM Rango", 6, 100000 );
        }
        if(submit_fa=='required'){
            field_val= field_val && checkLength( field_fa, "Fecha", 6, 11 );
            field_val= field_val && checkLength( field_ha, "Hora", 1, 2 );
            field_val= field_val && checkLength( field_ma, "Minutos", 1, 2 );
        }
        if(field_act.val()==""){
            field_val=false;
             $('#actividad').addClass( "ui-state-error" );
             updateTips("An option from \"Actividad\" must be selected" );
        }else{
            $('#actividad').removeClass( "ui-state-error" );
             updateTips("" );
        }

        var form_conf=$('#mailing');
        if(field_val){
            form_conf.submit();
        }






    }

    $('#enviar').click(function(){
       submitForm();
    });

  });
</script>

<?php
include("../common/menu.php");

//Query
if(isset($_POST['actividad'])){
    if($flag==1){
        foreach($casos as $key => $case){
            $tmp=str_replace(' ','',$case);
            if($tmp!=""){
            $resultados.="$tmp,";
            $query="INSERT INTO bo_mailing
            (actividad, tipo_seguimiento, em,fecha_recepcion, hora_recepcion,user)
            VALUES
            ($sel_actividad,$sel_seguimiento,$tmp,$sel_fecha,$sel_hora,'$sel_user')";
            mysql_query($query);
            if(mysql_errno()){
                     echo "<table width='100%' class='t2'>
                        <tr class='rojo'>
                        <td colspan=100>MySQL error ".mysql_errno().": "
                    	.mysql_error()."\n<br>When executing <br>\n$query\n<br><br></td>
                        </tr>
                        </table>
                        <br>";
                     $err_count++;
			}
            }
        }
        if($err_count>0){
                echo "<table width='100%' class='t2'>
                    <tr class='rojo'>
                    <td colspan=100>ERROR</tr>
                    </table>
                    <br>";
        }else{
            echo "<table width='100%' class='t2' id='AvisoOk'>
                        <tr class='green'>
                           <td colspan=100>Registro Exitoso $resultados</td>
                        </tr>
                        </table>
                        <br>";

        }
    }else{
    $query="INSERT INTO bo_mailing
    (actividad, tipo_seguimiento, em,fecha_recepcion, hora_recepcion,user)
    VALUES
    ($sel_actividad,$sel_seguimiento,$sel_caso,$sel_fecha,$sel_hora,'$sel_user')";
    mysql_query($query);
    if(mysql_errno()){
			    echo "<table width='100%' class='t2'>
                        <tr class='rojo'>
                        <td colspan=100>MySQL error ".mysql_errno().": "
                    	.mysql_error()."\n<br>When executing <br>\n$query\n<br><br></td>
                        </tr>
                        </table>
                        <br>";
                     $err_count++;
			}else{echo "<table width='100%' class='t2' id='AvisoOk'>
                        <tr class='green'>
                           <td colspan=100>Registro Exitoso caso: $sel_caso</td>
                        </tr>
                        </table>
                        <br>";
            }
    }
}
?>
<table width='100%' class='t2'>
    <tr class='title'>
        <td colspan=100>Formulario Mailing</td>
    </tr>
</table>
<br>
<div style='width:40%; margin: auto'>
<p class="validateTips">Fill the required Fields.</p>
</div>

<table style='width: 40%; margin: auto' class='t2'>
<form method='POST' action='<?php echo $_SERVER['PHP_SELF']; ?>' name="mailing" id="mailing">
<tr class='title'>
    <th colspan=2>Informacion</th>
</tr>
<tr class='title' id='tabAct1'>
        <th>Actividad</th>
        <td  class='pair'><select name="actividad" id="actividad" required>
        <option value="">Selecciona...</option>
        <?php
            foreach($actividad as $key => $act){
                echo "\t<option value='$act_id[$key]' $selected>$act</option>\n";
            }
            unset($key, $act);
        ?></select></td>
    </tr>
    <tr class='title'  id='tabSeg1'>
        <th>Tipo de seguimiento / Transaccion</th>
        <td class='pair'><select name="seguimiento" id="seguimiento"><option value="" title='100'>Selecciona...</option><?php
            foreach($seguimiento as $key => $seg){
                if($key==0){echo "<option value='' title='$seg_act[$key]'>Selecciona...</option>"; $tmp_act=$seg_act[$key];}
                if($tmp_act!=$seg_act[$key]){echo "<option value='' title='$seg_act[$key]'>Selecciona...</option>"; $tmp_act=$seg_act[$key];}
                echo "<option value='$seg_id[$key]' title='$seg_act[$key]' $selected>$seg</option>";
                $tmp_act=$seg_act[$key];
            }
            unset($key, $seg);
        ?></select></td>
    </tr>
    <tr class='title'  id='tabEM1'>
        <th>Caso EM (######)</th>
        <td class='pair'><input type="text" name='em' id='em' value=''></td>
    </tr>
    <tr class='title'  id='tabRango1'>
        <th>Rango EM</th>
        <td class='pair'><textarea name='rango' id='rango' value='' rows='10'></textarea></td>
    </tr>
    <tr class='title' id='tabLoc1'>
        <th>Localizador (#######-#)</th>
        <td class='pair'><input type="text" name='localizador' id='localizador' value=''></td>
    </tr>
    <tr class='title' id='tabAsig1'>
        <th>Fecha Recepcion</th>
        <td class='pair'><input type='date' name='fecha_asignacion' id='fecha_asignacion' value='' hidden><div align="center"  id='picker'></div></td>
    </tr>
    <tr class='title' id='tabAsig2'>
        <th>Hora Recepcion (24 hrs.)</th>
        <td class='pair'><input type='number' name='hr_asignacion' id='hr_asignacion' value='' min=0 max=23 maxlength=2 size=2 step=1> : <input type='number' name='min_asignacion' id='min_asignacion' value='' min=0 max=59 maxlength=2 size=2 step=1> hrs.</td>
    </tr>
    <tr class='total'>
        <td colspan=2 ><input type="button" name='enviar' id='enviar' value='Enviar' /></td>
    </tr>
</form>
</table>