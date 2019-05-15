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
date_default_timezone_set('America/Bogota');
//List Actividades
$query="SELECT * FROM bo_actividades WHERE area=4 ORDER BY actividad";
$result=mysql_query($query);
$num=mysql_numrows($result);
$i=0;
while($i<$num){
    $act_id[$i]=mysql_result($result,$i,'bo_act_id');
    $actividad[$i]=mysql_result($result,$i,'actividad');
$i++;
}

//List Seguimientos
$query="SELECT * FROM bo_seguimiento WHERE area=4 ORDER BY actividad, seguimiento";
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

<style>

#wrapper {
  margin-right: 500px;
}
#content {
  float: left;
  width: 100%;
}
#sidebar {
  float: right;
  width: 500px;
  margin-right: -500px;
}
#cleared {
  clear: both;
}
#inner-right {
    height: 300px;
    max-height: 300px;
    overflow-y: a;
}

</style>

<script>

$(document).ready(function()
    {



$('table').tablesorter({

    // *** APPEARANCE ***
    // Add a theme - try 'blackice', 'blue', 'dark', 'default'
    theme: 'blue',

    // fix the column widths
    widthFixed: true,

    // include zebra and any other widgets, options:
    // 'columns', 'filter', 'stickyHeaders' & 'resizable'
    // 'uitheme' is another widget, but requires loading
    // a different skin and a jQuery UI theme.
    widgets: ['zebra', 'filter', ],

    widgetOptions: {

        // zebra widget: adding zebra striping, using content and
        // default styles - the ui css removes the background
        // from default even and odd class names included for this
        // demo to allow switching themes
        // [ "even", "odd" ]
        zebra: [
            "ui-widget-content even",
            "ui-state-default odd"
            ],

        // uitheme widget: * Updated! in tablesorter v2.4 **
        // Instead of the array of icon class names, this option now
        // contains the name of the theme. Currently jQuery UI ("jui")
        // and Bootstrap ("bootstrap") themes are supported. To modify
        // the class names used, extend from the themes variable
        // look for the "$.extend($.tablesorter.themes.jui" code below
        uitheme: 'jui',

        // columns widget: change the default column class names
        // primary is the 1st column sorted, secondary is the 2nd, etc
        columns: [
            "primary",
            "secondary",
            "tertiary"
            ],

        // columns widget: If true, the class names from the columns
        // option will also be added to the table tfoot.
        columns_tfoot: true,

        // columns widget: If true, the class names from the columns
        // option will also be added to the table thead.
        columns_thead: true,

        // filter widget: If there are child rows in the table (rows with
        // class name from "cssChildRow" option) and this option is true
        // and a match is found anywhere in the child row, then it will make
        // that row visible; default is false
        filter_childRows: false,

        // filter widget: If true, a filter will be added to the top of
        // each table column.
        filter_columnFilters: true,

        // filter widget: css class applied to the table row containing the
        // filters & the inputs within that row
        filter_cssFilter: "tablesorter-filter",

        // filter widget: Customize the filter widget by adding a select
        // dropdown with content, custom options or custom filter functions
        // see http://goo.gl/HQQLW for more details
        filter_functions: null,

        // filter widget: Set this option to true to hide the filter row
        // initially. The rows is revealed by hovering over the filter
        // row or giving any filter input/select focus.
        filter_hideFilters: false,

        // filter widget: Set this option to false to keep the searches
        // case sensitive
        filter_ignoreCase: true,

        // filter widget: jQuery selector string of an element used to
        // reset the filters.
        filter_reset: null,

        // Delay in milliseconds before the filter widget starts searching;
        // This option prevents searching for every character while typing
        // and should make searching large tables faster.
        filter_searchDelay: 300,

        // filter widget: Set this option to true to use the filter to find
        // text from the start of the column. So typing in "a" will find
        // "albert" but not "frank", both have a's; default is false
        filter_startsWith: false,

        // filter widget: If true, ALL filter searches will only use parsed
        // data. To only use parsed data in specific columns, set this option
        // to false and add class name "filter-parsed" to the header
        filter_useParsedData: false,

        // Resizable widget: If this option is set to false, resized column
        // widths will not be saved. Previous saved values will be restored
        // on page reload
        resizable: true,

        // saveSort widget: If this option is set to false, new sorts will
        // not be saved. Any previous saved sort will be restored on page
        // reload.
        saveSort: true,

        // stickyHeaders widget: css class name applied to the sticky header
        stickyHeaders: "tablesorter-stickyHeader"

    }

});



    }
);

$(function() {


    var actividad_name='TESTING',
    tips = $( ".validateTips" );

    $("#login").hide();

    function loginPop(variable) {
        if(variable=='ok'){
          var page="/common/login.php?modal=on";
          var $dialog = $('#login')
          .html('<iframe style="border: 0px; " src="' + page + '" width="100%" height="100%"></iframe>')
          .dialog({
            title: "Login",
            autoOpen: false,
            dialogClass: 'dialog_fixed,ui-widget-header',
            modal: true,
            height: 500,
            minWidth: 600,
            minHeight: 400,
            draggable:true,
            /*close: function () { $(this).remove(); },*/
            buttons: { "Ok": function () {         $(this).dialog("close"); } }
          });
          $dialog.dialog('open');
        }
    }

    function sendRequest(variables,hora,act,fecha,em){
        var urlsend= "/json/formularios/bo_reembolsos.php?"+variables;
        var xmlhttp;
        var text;

        if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        } else { // code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

        xmlhttp.onreadystatechange=function(){
            if (xmlhttp.readyState==4 && xmlhttp.status==200){
                text= xmlhttp.responseText;
                var status = text.match("status- (.*) -status");
                 var startlogin='no';
                var notif_msg = text.match("msg- (.*) -msg");
                if(status[1]=='OK'){
                    tipo_noti='success';
                    hideall();
                    document.getElementById('actividad').value="";
                    $( "#hor-minimalist-a tbody" ).prepend( "<tr>" +
                      "<td width='25%'>" + hora + "</td>" +
                      "<td width='25%'>" + act + "</td>" +
                      "<td width='25%'>" + fecha + "</td>" +
                      "<td width='25%'>" + em + "</td>" +
                    "</tr>" );

                }else{
                    if(status[1]=='DISC'){
                        tipo_noti='error';
                        startlogin='ok';
                    }else{
                        tipo_noti='error';
                    }
                }
                new noty({
                    text: notif_msg[1],
                    type: tipo_noti,
                    timeout: 10000,
                    animation: {
                        open: {height: 'toggle'}, // jQuery animate function property object
                        close: {height: 'toggle'}, // jQuery animate function property object
                        easing: 'swing', // easing
                        speed: 500 // opening & closing animation speed
                    },
                    callback: {
                        onShow: function(){
                            loginPop(startlogin);
                        }
                        }
                });

            }
        }
        xmlhttp.open("GET",urlsend,true);
        xmlhttp.send();

    }



    $( "#picker" ).datepicker({
        altField: "#fecha_asignacion"
    });
    $( "#tabSeg1" ).hide();
    $( "#tabSeg2" ).hide();
    $( "#tabEM1" ).hide();
    $( "#tabEM2" ).hide();
    $( "#tabLoc1" ).hide();
    $( "#tabLoc2" ).hide();
    $( "#tabAsig1" ).hide();
    $( "#tabAsig2" ).hide();
    $( "#tabAsig3" ).hide();
    $( "#tabFcc1" ).hide();
    $( "#tabFcc2" ).hide();

      function updateTips( t ) {
      if(t==""){
            tips
            .show()
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
        $( "#tabEM2" ).hide();
        $( "#tabLoc1" ).hide();
        $( "#tabLoc2" ).hide();
        $( "#tabAsig1" ).hide();
        $( "#tabAsig2" ).hide();
        $( "#tabAsig3" ).hide();
        $( "#tabFcc1" ).hide();
        $( "#tabFcc2" ).hide();
        $( "#seguimiento" ).attr('required',false);
        document.getElementById('seguimiento').value="";
        document.getElementById('hr_asignacion').value="";
        document.getElementById('min_asignacion').value="";
        $( "#em" ).attr('required',false);
         $( "#fecha_asignacion" ).attr('required',false);
          $( "#hr_asignacion" ).attr('required',false);
           $( "#min_asignacion" ).attr('required',false);
        document.getElementById('em').value="";
        $( "#localizador" ).attr('required',false);
        document.getElementById('localizador').value="";
        $( "#fcc_si" ).attr('required',false);
        $( "#fcc_si" ).attr('checked',false);
        $( "#fcc_no" ).attr('checked',false);
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
        if($(this).val()==11){
            activate('tabEM','em',1);
            deactivate('tabLoc','localizador');
        }else{
            activate('tabLoc','localizador',1);
            deactivate('tabEM','em');
        }
        activate('tabAsig','fecha_asignacion',1);
        actividad_name= $(this).children(':selected').text();
    });

    $('#seguimiento').change(function(){
        var sel_id = $(this).val();
        if(sel_id == '1' || sel_id == '2'){
            activate('tabEM','em',1);
                if(sel_id=='1'){
                    activate('tabAsig','fecha_asignacion',1);
                }else{
                    deactivate('tabAsig','fecha_asignacion');
                }
        }else{
            deactivate('tabEM','em');
            $( "#em" ).attr('required',false);
            if(sel_id=='3' || sel_id=='4' || sel_id=='5' || sel_id=='6'){
                activate('tabEM','em',0);
                activate('tabLoc','localizador',0);
                if(sel_id=='6'){
                    activate('tabEM','em',1);
                    activate('tabAsig','fecha_asignacion',1);
                    deactivate('tabLoc','localizador');
                }else{
                    $( "#em" ).attr('required',false);
                    deactivate('tabAsig','fecha_asignacion');
                }
            }else{
               deactivate('tabEM','em');
               deactivate('tabLoc','localizador');
            }
        }


    });

    function submitForm(){
        var submit_em=$('#em').attr('required');
        var submit_seg=$('#seguimiento').attr('required');
        var submit_loc=$('#localizador').attr('required');
        var submit_fa=$('#fecha_asignacion').attr('required');
        var submit_ha=$('#hr_asignacion').attr('required');
        var submit_ma=$('#min_asignacion').attr('required');
        var submit_fcc=$('#fcc').attr('required');
        var act_name=$('#actividad').attr('title');
        var field_em=$('#em');
        var field_act=$('#actividad');
        var field_seg=$('#seguimiento');
        var field_loc=$('#localizador');
        var field_fa=$('#fecha_asignacion');
        var field_ha=$('#hr_asignacion');
        var field_ma=$('#min_asignacion');
        var field_fcc=$('#fcc');
        var field_val=true;
        if(field_act.val()==""){
            field_val=false;
             $('#actividad').addClass( "ui-state-error" );
             updateTips("An option from \"Actividad\" must be selected" );
        }else{
            $('#actividad').removeClass( "ui-state-error" );
             updateTips("");
        }
        if(submit_em=='required' || field_em.val()!=""){
            field_val= field_val && checkLength( field_em, "EM", 6, 7 );
            field_val= field_val && checkRegexp( field_em, /([0-9])+$/, "El formato solicitado para EM acepta solo numeros (0 al 9)" );
        }
        if(submit_loc=='required' || field_loc.val()!=""){
            field_val= field_val && checkLength( field_loc, "Localizadior", 9, 10 );
            field_val= field_val && checkRegexp( field_loc, /[0-9][0-9][0-9][0-9][0-9][0-9][0-9]-([0-9])+$/, "El formato solicitado para localizadores es 1234567-12" );
        }
        if(submit_fa=='required'){
            field_val= field_val && checkLength( field_fa, "Fecha", 6, 11 );
            field_val= field_val && checkLength( field_ha, "Hora", 1, 2 );
            field_val= field_val && checkLength( field_ma, "Minutos", 1, 2 );
        }



        var form_conf=$('#confirming');
        if(field_val){
            var currentdate= new Date();
            if(currentdate.getHours()<10){timenowh="0"+currentdate.getHours();}else{timenowh=currentdate.getHours();}
            if(currentdate.getMinutes()<10){timenowm="0"+currentdate.getMinutes();}else{timenowm=currentdate.getMinutes();}
            if(currentdate.getSeconds()<10){timenows="0"+currentdate.getSeconds();}else{timenows=currentdate.getSeconds();}
            var timenow=timenowh + ":"
                        + timenowm + ":"
                        + timenows;
            var urlok="localizador="+field_loc.val()+"&actividad="+field_act.val()+"&em="+field_em.val()+"&fecha_asignacion="+field_fa.val()+"&hr_asignacion="+field_ha.val()+"&min_asignacion="+field_ma.val();
            var switchval;
            if(field_em.val()==""){switchval=field_loc.val();}else{switchval=field_em.val();}
            sendRequest(urlok,timenow,actividad_name,field_fa.val(),switchval);
        }






    }

    $('#enviar').click(function(){
       submitForm();
    });

  });
</script>

<?php include("../common/menu.php");

$query="SELECT confirming_id, b.actividad, if(localizador IS NULL, em, localizador) as em, date_created, fecha_recepcion FROM bo_reembolsos a, bo_actividades b WHERE a.actividad=b.bo_act_id AND date_created>='".date('Y-m-d')."' AND user=".$_SESSION['id']." ORDER BY date_created DESC";
$result=mysql_query($query);
$num=mysql_numrows($result);
$i=0;
while($i<$num){
    $reg_hora[$i]=date('H:i:s', strtotime(mysql_result($result,$i,'date_created')));
    $reg_id[$i]=mysql_result($result,$i,'confirming_id');
    $reg_actividad[$i]=mysql_result($result,$i,'actividad');
    $reg_em[$i]=mysql_result($result,$i,'em');
    $reg_fecha[$i]=mysql_result($result,$i,'fecha_recepcion');
$i++;
}

?>
<table width='100%' class='t2'>
        <tr class='title'>
            <td colspan=100>Formulario Reembolsos</td>
        </tr>
    </table>
    <br>

<div id="wrapper">
  <div id="content">
    <div style='width:90%; margin: auto'>
    <p class="validateTips">Fill the required Fields.</p>
    </div>

    <table style='width: 90%; margin: auto' class='t2'>
    <form method='POST' action='<?php echo $_SERVER['PHP_SELF']; ?>' name="confirming" id="confirming">
    <tr class='title'>
        <th colspan=2>Informacion</th>
    </tr>
    <tr class='title' id='tabAct1'>
            <th>Actividad</th>
            <td  class='pair'><select name="actividad" id="actividad" required>
            <option value="">Selecciona...</option>
            <?php
                foreach($actividad as $key => $act){
                    echo "\t<option value='$act_id[$key]' title='$act' $selected>$act</option>\n";
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
        <tr class='title' id='tabLoc1'>
            <th>Localizador (#######-#)</th>
            <td class='pair'><input type="text" name='localizador' id='localizador' value=''></td>
        </tr>
        <tr class='title' id='tabAsig1'>
            <th>Fecha Asignacion</th>
            <td class='pair'><div align="center"  id='picker'></div><input type='text' name='fecha_asignacion' id='fecha_asignacion' value='' readonly></td>
        </tr>
        <tr class='title' id='tabAsig2'>
            <th>Hora Asignacion (24 hrs.)</th>
            <td class='pair'><input type='number' name='hr_asignacion' id='hr_asignacion' value='' min=0 max=23 maxlength=2 size=2 step=1> : <input type='number' name='min_asignacion' id='min_asignacion' value='' min=0 max=59 maxlength=2 size=2 step=1> hrs.</td>
        </tr>
        <tr class='title' id='tabFcc1'>
            <th>Confirmacion en primer llamada</th>
            <td class='pair'>Si: <input type="radio" name='fcc' id='fcc_si' value='1'>No: <input type="radio" name='fcc' id='fcc_no' value='0'></td>
        </tr>
        <tr class='total'>
            <td colspan=2 ><input type="button" name='enviar' id='enviar' value='Enviar' /></td>
        </tr>
    </form>
    </table>
  </div>
  <div id="sidebar">
   <br><br>

   <table width='100%' class='t2'>
        <tr class='title' colspan=100>
            <th>Registros Exitosos</th>
        </tr>
   </table>
   <table width='100%' id='hor-minimalist-a' class='tablesorter' >
        <thead>
        <tr class='title'>
            <th width='25%'>Hora</th>
            <th width='25%'>Actividad</th>
            <th width='25%'>Fecha</th>
            <th width='25%'>Loc / EM</th>
        </tr>
        </thead>
        <tbody>

        <?php
            unset($key,$iden);
            foreach($reg_id as $key => $iden){
                echo "<tr>\n";
                echo "<td width='25%'>$reg_hora[$key]</td>\n";
                echo "<td width='25%'>$reg_actividad[$key]</td>\n";
                echo "<td width='25%'>$reg_fecha[$key]</td>\n";
                echo "<td width='25%'>$reg_em[$key]</td>\n";
                echo "</tr>\n";
            }

        ?>
        </tbody>
   </table>
  </div>
  <div id="cleared"></div>
</div>
<div id='login'></div>
