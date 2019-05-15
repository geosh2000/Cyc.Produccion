<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="asesor_formulario_mt";
$menu_asesores="class='active'";

?>

<?php
include("../connectDB.php");
header("Content-Type: text/html;charset=utf-8");
//GET variables
$sel_area=1;
$sel_actividad=$_POST['actividad'];
if(isset($_POST['seguimiento'])){$sel_seguimiento="'".$_POST['seguimiento']."'";}else{$sel_seguimiento="NULL";}
if($_POST['em']!=""){$sel_caso="'".$_POST['em']."'";}else{$sel_caso="NULL";}
if($_POST['tipo']!=""){$sel_tipo="'".$_POST['tipo']."'";}else{$sel_tipo="NULL";}
if($_POST['pax']!=""){$sel_pax="'".$_POST['pax']."'";}else{$sel_pax="NULL";}
if($_POST['pnr']!=""){$sel_pnr="'".$_POST['pnr']."'";}else{$sel_pnr="NULL";}
if($_POST['aerolinea']!=""){$sel_aerolinea="'".$_POST['aerolinea']."'";}else{$sel_aerolinea="NULL";}
if($_POST['gds']!=""){$sel_gds="'".$_POST['gds']."'";}else{$sel_gds="NULL";}
if($_POST['queue']!=""){$sel_queue="'".$_POST['queue']."'";}else{$sel_queue="NULL";}
if($_POST['agencia']!=""){$sel_agencia="'".$_POST['agencia']."'";}else{$sel_agencia="NULL";}
if($_POST['observaciones']!=""){$sel_observaciones="'".$_POST['observaciones']."'";}else{$sel_observaciones="NULL";}
$sel_user=$_SESSION['id'];


//List Actividades
$query="SELECT * FROM trf_actividades WHERE area=$sel_area ORDER BY actividad";
$result=mysql_query($query);
$num=mysql_numrows($result);
$i=0;
while($i<$num){
    $act_id[$i]=mysql_result($result,$i,'trf_act_id');
    $actividad[$i]=mysql_result($result,$i,'actividad');
$i++;
}

//List Seguimientos
$query="SELECT * FROM trf_seguimiento WHERE area=$sel_area ORDER BY actividad, seguimiento";
$result=mysql_query($query);
$num=mysql_numrows($result);
$i=0;
while($i<$num){
    $seg_id[$i]=mysql_result($result,$i,'trf_seguimiento_id');
    $seguimiento[$i]=mysql_result($result,$i,'seguimiento');
    $seg_act[$i]=mysql_result($result,$i,'actividad');
$i++;
}

//List GDS
$query="SELECT * FROM trf_gds ORDER BY gds";
$result=mysql_query($query);
$num=mysql_numrows($result);
$i=0;
while($i<$num){
    $gds_id[$i]=mysql_result($result,$i,'trf_gds_id');
    $gds[$i]=mysql_result($result,$i,'gds');
$i++;
}


include("../common/scripts.php");

?>

<script>
$(function() {
    tips = $( ".validateTips" );
    $( "#tabSeg1" ).hide();
    $( "#tabEM1" ).hide();
    $( "#tabGds1" ).hide();
    $( "#tabAer1" ).hide();
    $( "#tabQue1" ).hide();
    $( "#tabPnr1" ).hide();
    $( "#tabAge1" ).hide();
    $( "#tabTs1" ).hide();
    $( "#tabPax1" ).hide();
    $( "#tabObs1" ).hide();


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

    function checkSel( o, n) {
      if ( o.val()=="" ) {
        o.addClass( "ui-state-error" );
        updateTips( "Please select an option from " + n);
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
        $( "#tabEM1" ).hide();
        $( "#tabGds1" ).hide();
        $( "#tabAer1" ).hide();
        $( "#tabQue1" ).hide();
        $( "#tabPnr1" ).hide();
        $( "#tabAge1" ).hide();
        $( "#tabTs1" ).hide();
        $( "#tabPax1" ).hide();
        $( "#seguimiento" ).attr('required',false);
        $( "#em" ).attr('required',false);
        $( "#gds" ).attr('required',false);
        $( "#aerolinea" ).attr('required',false);
        $( "#queue" ).attr('required',false);
        $( "#pnr" ).attr('required',false);
        $( "#agencia" ).attr('required',false);
        $( "#tipo" ).attr('required',false);
        $( "#pax" ).attr('required',false);
        document.getElementById('seguimiento').value="";
        document.getElementById('em').value="";
        document.getElementById('gds').value="";
        document.getElementById('aerolinea').value="";
        document.getElementById('queue').value="";
        document.getElementById('pnr').value="";
        document.getElementById('agencia').value="";
        document.getElementById('tipo').value="";
        document.getElementById('pax').value="";
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
            activate('tabSeg','seguimiento',1);
            activate('tabPnr','pnr',1);
            activate('tabGds','gds',1);
            if(act_id=='1'){
                activate('tabQue','queue',1);
            }else{
                deactivate('tabQue','queue');
            }
            if(act_id=='2' || act_id=='3' || act_id=='4'){
                activate('tabAge','agencia',1);
                if(act_id=='2'){
                    activate('tabEM','em',1);
                    activate('tabTs','tipo',1);
                }else{
                    deactivate('tabEM','em');
                    deactivate('tabTs','tipo');
                }
                if(act_id=='3'){
                    deactivate('tabGds','gds');
                }else{
                    activate('tabGds','gds',1);
                }
                if(act_id=='4'){
                    activate('tabObs','observaciones');
                }else{
                    deactivate('tabObs','observaciones');
                }

            }else{
                deactivate('tabAge','agencia');
            }
        }else{
            deactivate('tabSeg','seguimiento');
            deactivate('tabPnr','pnr');
            deactivate('tabGds','gds');
        }
    });

    $('#seguimiento').change(function(){
        var sel_id = $(this).val();
        var act2_id = $('#actividad').val();
        if(sel_id=='6'  || sel_id=='15'  || sel_id=='22'){
                    activate('tabPax','pax',1);
        }else{
            deactivate('tabPax','pax');
        }
        if(act2_id!='1' && act2_id!='3'){
            if(sel_id=='11' || sel_id=='20'){
                deactivate('tabPnr','pnr');
                deactivate('tabGds','gds');
                deactivate('tabAge','agencia');
                deactivate('tabGds','gds');
                deactivate('tabTs','tipo');
                deactivate('tabAer','aerolinea');

            }else{
                activate('tabPnr','pnr',1);
                activate('tabGds','gds',1);
                activate('tabAge','agencia',1);
                activate('tabGds','gds',1);
                activate('tabTs','tipo',1);
                if(sel_id=='10'  || sel_id=='19'){
                    deactivate('tabGds','gds');
                }else{
                    activate('tabGds','gds',1);
                }
                if(sel_id=='8'  || sel_id=='17'){
                    activate('tabAer','aerolinea',1);
                }else{
                    deactivate('tabAer','aerolinea');
                }

            }
        }
    });

    function submitForm(){
        var submit_em=$('#em').attr('required');
        var submit_act=$('#actividad').attr('required');
        var submit_seg=$('#seguimiento').attr('required');
        var submit_gds=$('#gds').attr('required');
        var submit_aer=$('#aerolinea').attr('required');
        var submit_que=$('#queue').attr('required');
        var submit_pnr=$('#pnr').attr('required');
        var submit_age=$('#agencia').attr('required');
        var submit_tip=$('#tipo').attr('required');
        var submit_pax=$('#pax').attr('required');
        var field_em=$('#em');
        var field_act=$('#actividad');
        var field_seg=$('#seguimiento');
        var field_gds=$('#gds');
        var field_aer=$('#aerolinea');
        var field_que=$('#queue');
        var field_pnr=$('#pnr');
        var field_age=$('#agencia');
        var field_tip=$('#tipo');
        var field_pax=$('#pax');
        var field_val=true;
        if(submit_act=='required'){
            field_val= field_val && checkSel( field_act, "Actividad");
        }
         if(submit_em=='required' || field_em.val()!=""){
            field_val= field_val && checkLength( field_em, "EM", 6, 7 );
            field_val= field_val && checkRegexp( field_em, /([0-9])+$/, "El formato solicitado para EM acepta solo numeros (0 al 9)" );
        }
        if(submit_seg=='required'){
            field_val= field_val && checkSel( field_seg, "Seguimiento");
        }
         if(submit_gds=='required'){
            field_val= field_val && checkSel( field_gds, "GDS");
        }
         if(submit_tip=='required'){
            field_val= field_val && checkSel( field_tip, "Nuevo/Seguimiento");
        }
        if(submit_aer=='required'){
            field_val= field_val && checkLength( field_aer, "Aerolinea", 1, 10);
        }
        if(submit_pnr=='required'){
            field_val= field_val && checkLength( field_pnr, "PNR", 4, 10);
        }
        if(submit_age=='required'){
            field_val= field_val && checkLength( field_age, "Agencia", 3, 20);
        }
        if(submit_que=='required'){
            field_val= field_val && checkLength( field_que, "Queue", 1, 10);
        }
        if(submit_pax=='required'){
            field_val= field_val && checkLength( field_pax, "Pax", 1, 3);
            field_val= field_val && checkRegexp( field_pax, /([0-9])+$/, "El campo de Pax solo acepta numeros" );
        }


        var form_conf=$('#tmt');
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
    $query="INSERT INTO trf_forms
    (actividad, seguimiento, tipo_seguimiento, caso, aerolinea, gds, queue, pnr,pax,agencia,user,area, comments)
    VALUES
    ($sel_actividad,$sel_seguimiento,$sel_tipo,$sel_caso,$sel_aerolinea,
    $sel_gds,$sel_queue,$sel_pnr,$sel_pax,$sel_agencia,'$sel_user',$sel_area, $sel_observaciones)";
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
        <td colspan=100>Registro Exitoso!<br>PNR: $sel_pnr</td>
    </tr>
</table>
<br>";}
}
?>
<table width='100%' class='t2'>
    <tr class='title'>
        <td colspan=100>Formulario Tráfico Marcas Terceros</td>
    </tr>
</table>
<br>
<div style='width:40%; margin: auto'>
<p class="validateTips">Fill the required Fields.</p>
</div>

<table style='width: 40%; margin: auto' class='t2'>
<form method='POST' action='<?php echo $_SERVER['PHP_SELF']; ?>' name="tmt" id="tmt">
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
        <th>Tipo de seguimiento</th>
        <td class='pair'><select name="seguimiento" id="seguimiento"><option value="" title='100'>Selecciona...</option><?php
            foreach($seguimiento as $key => $seg){
                if($key==0){echo "<option value='' title='$seg_act[$key]'>Selecciona...</option>"; $tmp_act=$seg_act[$key];}
                if($tmp_act!=$seg_act[$key]){echo "<option value='' title='$seg_act[$key]' selected>Selecciona...</option>"; $tmp_act=$seg_act[$key];}
                echo "<option value='$seg_id[$key]' title='$seg_act[$key]'>$seg</option>";
                $tmp_act=$seg_act[$key];
            }
            unset($key, $seg);
        ?></select></td>
    </tr>
    <tr class='title'  id='tabTs1'>
        <th>Nuevo / Seguimiento</th>
        <td class='pair'><select name='tipo' id='tipo'>
        <option value=''>Selecciona...</option>
        <option value='nuevo'>Nuevo</option>
        <option value='seguimiento'>Seguimiento</option>
        </select></td>
    </tr>
    <tr class='title' id='tabGds1'>
        <th>GDS</th>
        <td  class='pair'><select name="gds" id="gds" required>
        <option value="" selected>Selecciona...</option>
        <?php
            foreach($gds as $key => $globalizador){
                echo "\t<option value='$gds_id[$key]'>$globalizador</option>\n";
            }
            unset($key, $globalizador);
        ?></select></td>
    </tr>
    <tr class='title'  id='tabEM1'>
        <th>Caso EM (######)</th>
        <td class='pair'><input type="text" name='em' id='em' value=''></td>
    </tr>
    <tr class='title' id='tabAer1'>
        <th>Aerolínea</th>
        <td class='pair'><input type="text" name='aerolinea' id='aerolinea' value=''></td>
    </tr>
    <tr class='title' id='tabQue1'>
        <th>Queue</th>
        <td class='pair'><input type="text" name='queue' id='queue' value=''></td>
    </tr>
    <tr class='title' id='tabPnr1'>
        <th>PNR</th>
        <td class='pair'><input type="text" name='pnr' id='pnr' value=''></td>
    </tr>
    <tr class='title' id='tabPax1'>
        <th>Pax</th>
        <td class='pair'><input type="text" name='pax' id='pax' value=''></td>
    </tr>
    <tr class='title' id='tabAge1'>
        <th>Agencia</th>
        <td class='pair'><input type="text" name='agencia' id='agencia' value=''></td>
    </tr>
    <tr class='title' id='tabObs1'>
        <th>Observaciones</th>
        <td class='pair'><textarea rows="10" name='observaciones' id='observaciones' value=''></textarea></td>
    </tr>
    <tr class='total'>
        <td colspan=2 ><input type="button" name='enviar' id='enviar' value='Enviar' /></td>
    </tr>
</form>
</table>
