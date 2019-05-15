<?php
session_start();
include("../connectDB.php");
date_default_timezone_set('America/Bogota');

$departamento=$_SESSION['dep'];
$url=$_GET['url'];
$language=$_GET['languaje'];
$caller=$_GET['caller'];

$caller=str_replace("(","",$caller);
$caller=str_replace(")","",$caller);
$caller=str_replace(" ","",$caller);

if($caller==""){$caller='unknown';}

$url.="&language=$language&caller=$caller";

$created=date('Y-m-d H:i:s');


if($departamento!=4){
    header("Location: $url");
}

$createReg="INSERT INTO fcr (date_created, asesor,departamento) VALUE ('$created', '".$_SESSION['asesor_id']."','".$_SESSION['dep']."')";
mysql_query($createReg);

$regid=mysql_insert_id();

//List Motivos
$query="SELECT * FROM fcr_motivos WHERE area=1 ORDER BY resuelto, motivo";
$result=mysql_query($query);
$num=mysql_numrows($result);
$i=0;
while($i<$num){
    $motivo_id[$i]=mysql_result($result,$i,'fcr_motivo_id');
    $motivo[$i]=mysql_result($result,$i,'motivo');
    $motivo_res[$i]=mysql_result($result,$i,'resuelto');
$i++;
}
if(mysql_error()){echo mysql_error()."<br>";}

include("../common/scripts.php");
//echo $url;

?>

<script>

$(function(){

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

function sendRequest(campo,valor){
        var urlsend= "/json/formularios/fcr_update.php?&id=<?php echo $regid; ?>&field="+campo+"&newVal="+valor;
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
                    $('#ic_'+campo).attr('src','../images/ok.png');
                }else{
                    if(status[1]=='DISC'){
                        tipo_noti='error';
                        startlogin='ok';
                        $('#ic_'+campo).attr('src','../images/wrong.png');
                    }else{
                        tipo_noti='error';
                        $('#ic_'+campo).attr('src','../images/wrong.png');
                    }
                }
                new noty({
                    text: notif_msg[1],
                    type: tipo_noti,
                    timeout: 3000,
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

$('#form-fcr').hide();

function fcr(){
    if($('.fcr').height()==350){
        $('.fcr').animate({
            height: '50'
        });
        $('#form-fcr').hide();
        $('#fcrclick').html('&#9650<br>FCR');
    }else{
        $('.fcr').animate({
            height: '350'
        });
        $('#form-fcr').show();
        $('#fcrclick').html('&#9660<br>FCR');
    }
}

fcr();

$('#fcr_opt_no,#fcr_opt_si,#fcr_opt_na').change(function(){
        if($('input[name=fcr_opt]:checked').val()==3){
            $('.motivo').hide();
        }else{
            $('.motivo').show();
        }
        sendRequest('fcr',$('input[name=fcr_opt]:checked').val());
        $('#motivo option[value=""]').prop('selected', true);
        $('#ic_motivo').attr('src','../images/pending.png');
        var act_id;
        if($(this).data('options') == undefined){
            /*Taking an array of all options-2 and kind of embedding it on the select1*/
            $(this).data('options',$('#motivo_hid option').clone());
            }
        if($('#fcr_opt_si').is(':checked')){
            act_id=1;
        }else{
            act_id=0;
        }
        var options = $(this).data('options').filter('[title=' + act_id + ']');
        $('#motivo').html(options);

});

$('#localizador').change(function(){
    sendRequest('localizador',$(this).val());
});

$('#motivo').change(function(){
    sendRequest('motivo',$(this).val());
});

$('#callerid').change(function(){
    sendRequest('callerid',$(this).val());
});

$('#fcrclick').click(function(){
    fcr();
});

});

</script>
<style>
.fcr{
    float:left;
    width: 250;
    height: 42;
    background: #E5E9EF;
    position: absolute;
    left: 0;
    bottom: 0;
    z-index: 100;
    margin: 0 0 11 8;
    border-radius: 10px 10px 0px 0px;
    text-align: center;

}

</style>



<iframe id='cc' style='width:100%; height:98%; border:0px; margin:0' src="<?php echo $url; ?>">
  <p>Your browser does not support iframes.</p>
</iframe>

<div class='fcr'>
<p style='margin: 2px;cursor:pointer;' id='fcrclick'>&#9650<br>FCR</p>
<div id='form-fcr' style='width:100%; height:307px;background:#E5E9EF;'>
<?php echo "$created // $regid"; ?><br>  <br>
Caller id  <img id='ic_callerid' src="../images/ok.png" alt="ok" height="10" width="10"><br>
<input style='width:115' type='text' id='callerid' value='<?php echo $caller; ?>' name='caller'>
<br><br>Localizador  <img id='ic_localizador' src="../images/pending.png" alt="ok" height="10" width="10"><br>
<input style='width:115' type='text' id='localizador' value='' name='localizador'>
<br><br>Resolucion en primer llamada  <img id='ic_fcr' src="../images/pending.png" alt="ok" height="10" width="10"><br>
Si <input type='radio' id='fcr_opt_si' value='1' name='fcr_opt'> No <input type='radio' id='fcr_opt_no' value='0' name='fcr_opt'> No Aplica <input type='radio' id='fcr_opt_na' value='3' name='fcr_opt'>
<br><br>Motivo  <img id='ic_motivo' src="../images/pending.png" alt="ok" height="10" width="10"><br>
<select name="motivo_hid" id="motivo_hid" hidden><option value="" title='100'>Selecciona...</option><?php
            foreach($motivo as $key => $mot){
                if($key==0){echo "<option value='' title='$motivo_res[$key]'>Selecciona...</option>"; $tmp_mot=$motivo_res[$key];}
                if($tmp_mot!=$motivo_res[$key]){echo "<option value='' title='$motivo_res[$key]'>Selecciona...</option>"; $tmp_mot=$motivo_res[$key];}
                echo "<option value='$motivo_id[$key]' title='$motivo_res[$key]'>$mot</option>";
                $tmp_mot=$motivo_res[$key];
            }
            unset($key, $mot);
        ?></select>
        <select name="motivo" id="motivo" style='max-width:80%;'><option value="" title='100'>Selecciona...</option></select>
<br><br>
</div>
</div>