<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="asesor_formulario_fcr";
$menu_asesores="class='active'";

?>

<?php
include("../connectDB.php");
header("Content-Type: text/html;charset=utf-8");
//GET variables
$sel_area=1;
if($_POST['fcr']!=""){$sel_resolucion="'".$_POST['fcr']."'";}else{$sel_resolucion="NULL";}
if($_POST['telefono']!=""){$sel_telefono="'".$_POST['telefono']."'";}else{$sel_telefono="NULL";}
if($_POST['localizador']!=""){$sel_localizador="'".$_POST['localizador']."'";}else{$sel_localizador="NULL";}
if($_POST['motivo']!=""){$sel_motivo="'".$_POST['motivo']."'";}else{$sel_motivo="NULL";}
$sel_user=$_SESSION['id'];


//List Motivos
$query="SELECT * FROM fcr_motivos WHERE area=$sel_area ORDER BY resuelto, motivo";
$result=mysql_query($query);
$num=mysql_numrows($result);
$i=0;
while($i<$num){
    $motivo_id[$i]=mysql_result($result,$i,'fcr_motivo_id');
    $motivo[$i]=mysql_result($result,$i,'motivo');
    $motivo_res[$i]=mysql_result($result,$i,'resuelto');
$i++;
}


include("../common/scripts.php");

?>

<script>
$(function() {
    tips = $( ".validateTips" );

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

    $('#fcr_si,#fcr_no').change(function(){
        var act_id;
        if($(this).data('options') == undefined){
            /*Taking an array of all options-2 and kind of embedding it on the select1*/
            $(this).data('options',$('#motivo_hid option').clone());
            }
        if($('#fcr_si').is(':checked')){
            act_id=1;
        }else{
            act_id=0;
        }
        var options = $(this).data('options').filter('[title=' + act_id + ']');
        $('#motivo').html(options);



    });

    function submitForm(){
        var submit_fcr=$('#fcr_si').attr('required');
        var submit_motivo=$('#motivo').attr('required');
        var field_fcr=$('#fcr_si');
        var field_motivo=$('#motivo');
        var field_tel=$('#telefono');
        var field_loc=$('#localizador');
        var field_val=true;

        if($('#fcr_si').is(':checked') || $('#fcr_no').is(':checked')) {

        }else{
            field_val=false;
             $('#fcr_si').addClass( "ui-state-error" );
             updateTips("An option from \"Confirmacion en primer llamada\" must be selected" );
        }
        if(field_motivo.val()==""){
            field_val=false;
             $('#motivo').addClass( "ui-state-error" );
             updateTips("An option from \"Motivo\" must be selected" );
        }else{
            $('#actividad').removeClass( "ui-state-error" );
             updateTips("" );
        }
        if(field_loc.val()!=""){
            field_val= field_val && checkLength( field_loc, "Localizadior", 7, 7 );
            field_val= field_val && checkRegexp( field_loc, /[0-9][0-9][0-9][0-9][0-9][0-9][0-9]+$/, "El formato solicitado para localizadores es 1234567" );
        }
        if(field_tel.val()!=""){
            field_val= field_val && checkLength( field_tel, "Tel", 3, 17 );
            field_val= field_val && checkRegexp( field_tel, /([0-9])/, "El formato solicitado para Tel acepta solo numeros (0 al 9)" );
        }

        var form_conf=$('#fcr_sac');
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
if(isset($_POST['motivo'])){
   $query="INSERT INTO fcr
    (departamento, fcr, caller,localizador, motivo,asesor)
    VALUES
    (4,$sel_resolucion,$sel_telefono,$sel_localizador,$sel_motivo,'".$_SESSION['asesor_id']."')";
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
                           <td colspan=100>Registro Exitoso</td>
                        </tr>
                        </table>
                        <br>";
            }
    }

?>
<table width='100%' class='t2'>
    <tr class='title'>
        <td colspan=100>Formulario FCR SAC</td>
    </tr>
</table>
<br>
<div style='width:40%; margin: auto'>
<p class="validateTips">Fill the required Fields.</p>
</div>

<table style='width: 40%; margin: auto' class='t2'>
<form method='POST' action='<?php echo $_SERVER['PHP_SELF']; ?>' name="fcr_sac" id="fcr_sac">
<tr class='title'>
    <th colspan=2>Informacion</th>
    </tr>
    <tr class='title' id='tabFcr1'>
        <th>Resolucion en primer llamada</th>
        <td class='pair'>Si: <input type="radio" name='fcr' id='fcr_si' value='1' required>No: <input type="radio" name='fcr' id='fcr_no' value='0'></td>
    </tr>
    <tr class='title' id='tabTel1'>
        <th>Telefono<br>(incluye 045 o 01)</th>
        <td class='pair'><input type="text" name='telefono' id='telefono' value=''></td>
    </tr>
    <tr class='title' id='tabLoc1'>
        <th>Localizador (#######)</th>
        <td class='pair'><input type="text" name='localizador' id='localizador' value=''></td>
    </tr>
    <tr class='title'  id='tabMot1'>
        <th>Motivo</th>
        <td class='pair'><select name="motivo_hid" id="motivo_hid" hidden><option value="" title='100'>Selecciona...</option><?php
            foreach($motivo as $key => $mot){
                if($key==0){echo "<option value='' title='$motivo_res[$key]'>Selecciona...</option>"; $tmp_mot=$motivo_res[$key];}
                if($tmp_mot!=$motivo_res[$key]){echo "<option value='' title='$motivo_res[$key]'>Selecciona...</option>"; $tmp_mot=$motivo_res[$key];}
                echo "<option value='$motivo_id[$key]' title='$motivo_res[$key]'>$mot</option>";
                $tmp_mot=$motivo_res[$key];
            }
            unset($key, $mot);
        ?></select>
        <select name="motivo" id="motivo"><option value="" title='100'>Selecciona...</option></select></td>
    </tr>
    <tr class='total'>
        <td colspan=2 ><input type="button" name='enviar' id='enviar' value='Enviar' /></td>

    </tr>
</form>
</table>
