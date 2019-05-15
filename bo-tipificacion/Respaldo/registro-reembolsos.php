<?php
include_once("../modules/modules.php");

initSettings::start(true,'asesor_formularios_bo');
initSettings::printTitle('SAC BO - Tipificación Reembolsos');

timeAndRegion::setRegion('Cun');


$asesor=$_SESSION['asesor_id'];
$area=4;
?>

<link rel="stylesheet" href="/js/periodpicker/build/jquery.timepicker.min.css">
<script src="/js/periodpicker/build/jquery.timepicker.min.js"></script>
<script>
  maxDate='<?php echo date('Y-m-d', strtotime('+1 days')); ?>';
  todayDate='<?php echo date('Y-m-d H:i:s'); ?>';
  Datemonth = <?php echo date('m'); ?> ;
  Dateyear = <?php echo date('Y'); ?>;
  Dateday = <?php echo date('d'); ?>;
</script>
<script src="query.js"></script>
<script>

$(function(){

  flag=true;

    function start_fields(){
        $('#f_localizador, #f_tipo, #date-in, #dateout').val('');
        $('#contain-localizador, #contain-tipo, #contain-datein, #contain-dateout').attr('req',1);

    }

    start_fields();

    function validate(){
        //Datec
        if($('#contain-datein').attr('req')==1 && $('#date-in').val()==''){
            flag_datein=false;
            $('#contain-datein div p').addClass('error');
        }else{
            flag_datein=true;
            $('#contain-datein div p').removeClass('error');
        }


        //FC
        if($('#contain-localizador').attr('req')==1 && $('#f_localizador').val()==''){
            flag_localizador=false;
            $('#f_localizador').addClass('error');
        }else{
            flag_localizador=true;
            $('#f_localizador').removeClass('error');
        }

		//Tipo
        if($('#contain-tipo').attr('req')==1 && $('#f_status').val()==''){
            flag_tipo=false;
            $('#f_tipo').addClass('error');
        }else{
            flag_tipo=true;
            $('#f_tipo').removeClass('error');
        }

        if(flag_localizador && flag_datein &&  flag_tipo){flag=true;}else{flag=false; $('#submit_form').prop('disabled',false);}


    }


    //BUTTON Submit
    $('#submit_form').click(function(){
    	$('#submit_form').prop('disabled',true);
        validate();
        if(flag){
        	var variables="asesor=<?php echo $asesor; ?>&area=<?php echo $area; ?>&localizador=" + $('#f_localizador').val() + "&datec=" +  $('#date-in').val() + "&tipo=" +  $('#f_tipo').val();
            //alert(variables);
            sendRequest(variables);
        }
    });

    //Change to UPPERCASE
    $('#f_pnr').keyup(function(){
        var name=$(this).val();
        var newname=name.toUpperCase();
        $(this).val(newname);
    });

});

</script>

<style>
.formulario{
    width: 800px;
    height: 100%;
    margin: auto;
    overflow: auto;
}

.titulo{
    width: 800px;
    height: 65px;
    font-size: 24px;
    font-weight: bold;
    text-align: center;
    margin: auto;
    margin-top: -19px;
    border-radius: 15px;
    background: #008CBA
}

.campo{
    width: 520px;
    height: 80px;
    margin: auto;
    margin-top: 20px;
    border-radius: 15px;
}

.campo .name{
    float: left;
    height: 100%;
    width: 40%;
    background: #008CBA;
    border-radius: 15px 0 0 15px;
    color: white;
    font-size: 20px;
    font-weight: bold;
    text-align: center;
}

.campo .name p{
    padding-top:12px;
}

.campo .opcion{
    float: left;
    height: 100%;
    width: 60%;
    background: #E7F5FE;
    border-radius: 0 15px 15px 0;
    color: black;
    font-size: 20px;
    text-align: center;
}

.campo .opcion .seleccion{
    padding-top:5px;
}

.seleccion select, .seleccion input{
    width: 200px;
}

.error{
    background: #FFE8E0;
    color: black;
}



</style>
<?php


?>
<div style='float: left; width:60%; margin: auto;'>
<div class='formulario'>
    <div class='titulo'>
        <p style='padding-top: 13px; color: white;'>BackOffice - Reembolsos</p>
        <p style='padding-top: 0px; color: white; font-size:16px; margin-top: -14px; font-weight: normal;'><?php echo $_SESSION['name'];?></p>
    </div>
    <div id='contain-localizador' class='campo'>
        <div class='name'>
            <p>Localizador</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><input type='text' id='f_localizador' class='input' value=''  req='1'>*</p>
        </div>
    </div>
    <div id='contain-datein' class='campo'>
        <div class='name'>
            <p>Fecha Recepcion</p>
        </div>
        <div class='opcion'>
            <br><input type='text' class='seleccion' id='date-in' value=''></p>
        </div>
    </div>
    <div id='contain-tipo' class='campo'>
        <div class='name'>
            <p>Status</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><select id='f_tipo' class='input' req='1'><option value=''>Selecciona...</option>
            <?php
                $query="SELECT * FROM bo_status WHERE area=$area ORDER BY status";
                if($result=Queries::query($query)){
                  while($fila=$result->fetch_assoc()){
                    echo "<option value='".$fila['id']."'>".$fila['status']."</option>";
                  }
                }
            ?></select>*</p>
        </div>
    </div>
    <div id='contain-submit' class='campo' style='text-align: right;'>
        <button class='button button_red_w' id='submit_form'>Guardar</button>
    </div>

</div>
</div>
<div style='float:right; width: 30%; margin: auto;'>
<div id="sidebar">
   <iframe id='regs' width='100%' height='100%' style='border: 0;' src='registros.php?area=<?php echo $area; ?>'></iframe>
 </div>
 </div>
<div id='login'></div>
<div id='error'></div>
