<?php

include_once('../modules/modules.php');

initSettings::start(true,'hc_detalle_asesores');
initSettings::printTitle('Detalle por asesor');

$id_asesor=$_POST['asesor'];
$name_asesor=$_POST['text'];

$tbody="<td><input style='color: black' type='text' id='name' name='text' placeholder='Nombre del asesor' value='$name_asesor' size=50><input type='hidden' value='$id_asesor' id='asesorSelected' name='asesor'></td>";

Filters::showFilter('','POST','search','Consultar',$tbody);

class Asesor{
    public $nombre;
    public $corto;
    public $numcol;
    public $tel;
    public $tel2;
    public $correo;
    public $correoPersonal;
    public $puesto;
    public $departamento;
    public $ingreso;
    public $egreso;
    public $status;
    public $contrato_tipo;
    public $contrato_status;
    public $contrato_fin;
    public $cxc_total;
    public $cxc_adeudo;
    public $cxc_quincena;
    public $histo_puestos;
    public $sanc;
    public $cxc_pend;
    public $recontratable;
    public $idAsesor;
    public $solPendiente=0;
    
    
    
    public function __construct($id){
        $connectdb=Connection::mysqliDB('CC');
        
        //Get General Data
        $query="SELECT 
                    a.id,
                    Nombre,
                    `N Corto`,
                    num_colaborador,
                    Telefono1,
                    Telefono2,
                    Usuario,
                    correo_personal as correo,
                    Departamento,
                    d.Puesto,
                    Ingreso,
                    Egreso,
                    IF(Egreso>CURDATE(),'Activo','Inactivo') as Status
                FROM
                    Asesores a
                        LEFT JOIN
                    daily_dep b ON a.id = b.asesor
                        LEFT JOIN
                    PCRCs c ON b.dep = c.id
                        LEFT JOIN
                    PCRCs_puestos d ON b.puesto = d.id
                WHERE
                    a.id = $id";
        if($result=$connectdb->query($query)){
            $fila=$result->fetch_assoc();
            $this->idAsesor=$fila['id'];
            $this->nombre=utf8_encode($fila['Nombre']);
            $this->corto=utf8_encode($fila['N Corto']." (id: ".$fila['id'].")");
            $this->tel=utf8_encode($fila['Telefono1']);
            $this->tel1=utf8_encode($fila['Telefono2']);
            $this->correo=utf8_encode($fila['Usuario']."@pricetravel.com");
            $this->correoPersonal=utf8_encode($fila['correo']);
            $this->puesto=utf8_encode($fila['Puesto']);
            $this->departamento=utf8_encode($fila['Departamento']);
            $this->ingreso=utf8_encode($fila['Ingreso']);
            
            if(date('Y',strtotime($fila['Egreso']))>=2030){
                $this->egreso=utf8_encode('NA');
            }else{
                $this->egreso=utf8_encode($fila['Egreso']);
                
                $query="SELECT * FROM asesores_recontratable WHERE asesor=$id";
                if($result=$connectdb->query($query)){
                    $fila=$result->fetch_assoc();
                    $this->recontratable=$fila['recontratable'];
                }
            }
            
            
            if($fila['Status']=='Activo'){
                $this->status=utf8_encode("<span class='label label-success'>Activo</span>");
            }else{
                $this->status=utf8_encode("<span class='label label-danger'>Inactivo</span>");
            }
            $this->numcol=utf8_encode($fila['num_colaborador']);
        }
        
        //Get Vacantes
        $query="SELECT 
                    a.id,
                    fecha_in,
                    c.Departamento,
                    d.Puesto,
                    e.Ciudad,
                    f.PDV AS Oficina
                FROM
                    asesores_movimiento_vacantes a
                        LEFT JOIN
                    asesores_plazas b ON a.vacante = b.id
                        LEFT JOIN
                    PCRCs c ON b.departamento = c.id
                        LEFT JOIN
                    PCRCs_puestos d ON b.puesto = d.id
                        LEFT JOIN
                    db_municipios e ON b.ciudad = e.id
                        LEFT JOIN
                    PDVs f ON b.oficina = f.id
                WHERE
                    asesor_in = $id
                ORDER BY Fecha_in";
        if($result=$connectdb->query($query)){
            while($fila=$result->fetch_assoc()){
                $this->histo_puestos[$fila['id']]=utf8_encode(date('d-m-Y',strtotime($fila['fecha_in'])).": <span style='color: #008cba'><strong>".$fila['Departamento']." -> ".$fila['Puesto']."</strong></span><br><span style='color: #ef0063'>".$fila['Oficina']."</span><br>".$fila['Ciudad']);
            }
        }
        
        //Solicitudes Pendientes
        $query="SELECT 
                    a.id,
                    fecha_solicitud,
                    NOMBREASESOR(solicitado_por, 1) solicitado,
                    fecha,
                    a.tipo,
                    c.Departamento,
                    d.Puesto,
                    e.Ciudad,
                    PDV AS Oficina,
                    a.comentarios,
                    a.comentariosRRHH,
                    CASE
                        WHEN a.status=0 THEN 'En espera'
                        WHEN a.status=1 THEN 'Aprobada'
                        WHEN a.status=2 THEN 'En proceso de revision'
                        WHEN a.status=3 THEN 'Declinada'
                        WHEN a.status=4 THEN 'Cancelada'
                    END as statusOK,
                    a.status
                FROM
                    rrhh_solicitudesCambioBaja a
                        LEFT JOIN
                    asesores_plazas b ON a.vacante = b.id
                        LEFT JOIN
                    PCRCs c ON b.departamento = c.id
                        LEFT JOIN
                    PCRCs_puestos d ON b.puesto = d.id
                        LEFT JOIN
                    db_municipios e ON b.ciudad = e.id
                        LEFT JOIN
                    PDVs f ON b.oficina = f.id
                WHERE
                    asesor =$id
                ORDER BY a.id";
        if($result=$connectdb->query($query)){
            while($fila=$result->fetch_assoc()){
                
                if(($fila['status']==0 || $fila['status']==2) && $_SESSION['hc_cambios_solicitud']==1){
                    $button="<br><button type='button' class='cxlSol btn btn-warning btn-sm' idSol='".$fila['id']."'>Cancelar</button>";
                }else{
                    $button="";
                }
                
                switch($fila['status']){
                    case 0:
                        $btnclass="<span class='label label-warning'>En Espera</span>";
                        $this->solPendiente=1;
                        break;
                    case 1:
                        $btnclass="<span class='label label-success'>Aprobada</span>";
                        break;
                    case 2:
                        $btnclass="<span class='label label-info'>En proceso de Revision</span>";
                        $this->solPendiente=1;
                        break;
                    case 3:
                        $btnclass="<span class='label label-danger'>Declinada</span>";
                        break;
                    case 4:
                        $btnclass="<span class='label label-default'>Cancelada</span>";
                        break;
                }
                
                if($fila['comentariosRRHH']!=NULL){
                    $comRH="RRHH: \"".$fila['comentariosRRHH']."\"<br>";
                }else{
                    $comRH="";
                }
                
                if($fila['tipo']==1){
                    $this->histo_puestos[$fila['id']]=utf8_encode("Solicitudes: Status -> $btnclass<br>$comRH<span style='color: #ef0063'>Solicitud hecha el ".date('d-m-Y',strtotime($fila['fecha_solicitud']))." por ".$fila['solicitado']."</span><br>".date('d-m-Y',strtotime($fila['fecha'])).": <span style='color: #008cba'><strong>".$fila['Departamento']." -> ".$fila['Puesto']."</strong></span><br><span style='color: #ef0063'>".$fila['Oficina']."</span><br>".$fila['Ciudad']."$button");
                }else{
                   $this->histo_puestos[$fila['id']]=utf8_encode("Baja Pendiente por Aprobar -> $btnclass<br>$comRH<span style='color: #ef0063'>Solicitud hecha el ".date('d-m-Y',strtotime($fila['fecha_solicitud']))." por ".$fila['solicitado']."</span><br>".date('d-m-Y',strtotime($fila['fecha'])).": <span style='color: #008cba'><strong>BAJA ASESOR</strong></span><br><span style='color: #ef0063'>".$fila['comentarios']."</span>$button");   
                }
            }
        }
        
          
        //Get Sanciones
        $query="SELECT 
                    CASE
                        WHEN tipo = 1 THEN 'Acta'
                        WHEN tipo = 2 THEN 'Accion'
                        WHEN tipo = 3 THEN 'PPerformance'
                        ELSE 'Otro'
                    END AS Tipo,
                    COUNT(*) AS total,
                    SUM(Vigente) AS Vigentes
                FROM
                    (SELECT 
                        tipo,
                            IF(CURDATE() BETWEEN fecha_afectacion_inicio AND fecha_afectacion_fin, 1, 0) AS Vigente
                    FROM
                        Sanciones
                    WHERE
                        asesor = $id) a
                GROUP BY tipo";
        if($result=$connectdb->query($query)){
            while($fila=$result->fetch_assoc()){
                $this->sanc[$fila['Tipo']]['Total']=$fila['total'];
                $this->sanc[$fila['Tipo']]['Vigentes']=$fila['Vigentes'];
            }
        }
        
        //CxCs
        $query="SET @thisnom=(SELECT id FROM rrhh_calendarioNomina WHERE CURDATE() BETWEEN inicio AND fin);

                DROP TEMPORARY TABLE IF EXISTS cxcs;
                
                CREATE TEMPORARY TABLE cxcs SELECT b.*, IF(quincena=@thisnom,1,0) as thisNom FROM asesores_cxc a LEFT JOIN rrhh_pagoCxC b ON a.id=b.cxc WHERE asesor=$id;";
        $i=0;
        if($connectdb->multi_query($query)){

          do{
            //echo $i."<br>";
            $i++;
          } while (@$connectdb->next_result());
        }else{
          echo "ERROR Multi! -> ".$connectdb->error;
        }
        
        $query="SELECT SUM(monto) as Monto, SUM(IF(cobrado=0,monto,0)) as Adeudo, SUM(IF(thisNom=1,monto,0)) as siguiente FROM cxcs";
        if($result=$connectdb->query($query)){
            while($fila=$result->fetch_assoc()){
                $this->cxc_total=$fila['Monto'];
                $this->cxc_adeudo=$fila['Adeudo'];
                $this->cxc_quincena=$fila['siguiente'];
            }
        }
        
        //Pagos Pendientes
        $query="SELECT 
                    b.id, Localizador, n_pago, pago, b.monto
                FROM
                    asesores_cxc a
                        LEFT JOIN
                    rrhh_pagoCxC b ON a.id = b.cxc
                        LEFT JOIN
                    rrhh_calendarioNomina c ON b.quincena = c.id
                WHERE
                    asesor = $id AND cobrado = 0
                        AND activo = 1";
        if($result=$connectdb->query($query)){
            while($fila=$result->fetch_assoc()){
                $this->cxc_pend[$fila['id']]['Localizador']=$fila['Localizador'];
                $this->cxc_pend[$fila['id']]['n_pago']=$fila['n_pago'];
                $this->cxc_pend[$fila['id']]['Fecha']=$fila['pago'];
                $this->cxc_pend[$fila['id']]['Monto']=$fila['monto'];
            }
        }
        
        //Contratos
        $query="SELECT * FROM asesores_contratos WHERE asesor=$id";
        if($result=$connectdb->query($query)){
            while($fila=$result->fetch_assoc()){
                if($fila['tipo']==1){
                    $this->contrato_fin=$fila['vecimiento'];  
                    $this->contrato_tipo='Temporal';
                }else{
                    $this->contrato_fin='NA';  
                    $this->contrato_tipo='Indefinido';
                }
                
                if($fila['status']==1){
                    $this->contrato_status='Terminado';
                }else{
                    if($fila['tipo']==1){
                        if(date('Y-m-d')>date('Y-m-d', strtotime($fila['vencimiento']))){
                            $this->contrato_status='Vencido';
                        }
                    }else{
                        if($this->egreso=='NA'){
                            $this->contrato_status='Vigente';
                        }else{
                            $this->contrato_status='Terminado';
                        }
                        
                    }
                }
             
            }
        }
        
        switch($this->contrato_status){
            case "Vigente":
                $this->contrato_status=utf8_encode("<span class='label label-success'>Vigente</span>");
                break;
            case "Vencido":
                $this->contrato_status=utf8_encode("<span class='label label-warning'>Vencido</span>");
                break;
            case "Terminado":
                $this->contrato_status=utf8_encode("<span class='label label-danger'>Terminado</span>");
                break;
        }
        
        
        $connectdb->close();
    }
}

?>
<style>
    .nav{
        padding-top: 11px;
    }
.ui-autocomplete-category {
    font-weight: bold;
    padding: .2em .4em;
    margin: .8em 0 .2em;
    line-height: 1.5;
    z-index: -100
}
.contact-box {background-color: #ffffff;border: 1px solid #e7eaec;padding:15px 0;margin-bottom: 20px; width: 575px; margin-left: 20px}
.contact-box .label {font-size: 12px;margin-right: 6px;font-style: italic;}
.label-cont {margin-bottom: 10px;}
.contact-box h3 {margin-top: 0;font-weight: normal;font-style: italic;}
h5.contact-title {    font-size: 15px;margin-bottom: -7px;min-height: 50px;}

</style>
<script>
$(function(){
    $.widget( "custom.catcomplete", $.ui.autocomplete, {
      _create: function() {
        this._super();
        this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
      },
      _renderMenu: function( ul, items ) {
        var that = this,
          currentCategory = "";
        $.each( items, function( index, item ) {
          var li;
          if ( item.category != currentCategory ) {
            ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
            currentCategory = item.category;
          }
          li = that._renderItemData( ul, item );
          if ( item.category ) {
            li.attr( "aria-label", item.category + " : " + item.label );
          }
        });
      }
    });

    $( "#name" ).catcomplete({
      delay: 0,
      minLenght: 3,
      source: '/config/search_name.php',
      select: function(ev, ui){
        $('#asesorSelected').val(ui.item.id);
      }
    });
    
    $( "#new_asesor" ).catcomplete({
        delay: 0,
        minLenght: 3,
        source: '../config/search_name.php',
        appendTo: '#cxcForm',
        select: function(ev, ui){
          asesorSelected=ui.item.id;
          $('#new_asesor_hidden').val(ui.item.id);
          asesorSelected_text = ui.item.label;
          
        }
      });
    
    $( "#f_selasesor" ).catcomplete({
      delay: 0,
      minLenght: 3,
      source: '../config/search_name.php',
      appendTo: '#newAct',
        select: function(ev, ui){
        $('#f_asesor').val(ui.item.id);
        asesorSelected_text = ui.item.label;
        $('#baja_name').text(asesorSelected_text);
        //console.log("id asesor seleccionado: "+asesorSelected);
      }
    });

});
</script>
<?php
if(!isset($_POST['asesor'])){exit;}

$asesor=new Asesor($id_asesor);
?>
<script>
   
$(function(){
    
    $('#d_ap, #new_fechaCxC, #new_fechaAp, #d_inicio ').periodpicker({
      lang: 'en',
      animation: true,
      norange: true
    });
    
    $('#f_fecha_aplicacion, #f_fecha_incidencia').periodpicker({
		norange: true, // use only one value
		cells: [1, 1], // show only one month
		maxDate: '<?php echo date('Y-m-d',strtotime('+1 days')); ?>'
	});

	$('#f_fecha_afectacion_inicio').periodpicker({
		end: '#f_fecha_afectacion_fin',
		minDate: '<?php echo date('Y-m-d'); ?>'
	});
    
    $('#ch_fecha').periodpicker({
        norange: true,
        clearButtonInButton: true,
        todayButton: true,
        onAfterHide: function () {
            populateDeps('ciudad');
         },
         formatDate: 'YYYY-MM-DD'
     });
    
    $('#newinicio, #baja_inicio, #baja_fecha').periodpicker({
        norange: true,
        clearButtonInButton: true,
        todayButton: true,
        formatDate: 'YYYY-MM-DD'
     });

    
    //CXC Form
    dialogCxC = $('#cxcForm').dialog({
      autoOpen: false,
      modal: true,
      height:  "auto",
      width: 400,
      buttons: {
        "Asignar": function(){
            addCxC();
            },
        Cancel: function(){
            form[ 0 ].reset();
            $('#d_ap, #new_fechaCxC, #new_fechaAp, #d_inicio').periodpicker('clear');
            dialogCxC.dialog('close');
        }
      }
    });
    
    $('#addCxC').click(function(){
       dialogCxC.dialog('open'); 
    });
    
    $('#payCxC').click(function(){
       dialogPayCxC.dialog('open'); 
    });
    
    function addCxC(){
      showLoader('Guardando CxC');

      $.ajax({
        url: 'saveCxC.php',
        type: 'POST',
        data: {asesor: $('#new_asesor_hidden').val(), loc: $('#new_loc').val(), monto: $('#new_monto').val(), f_cxc: $('#new_fechaCxC').val(), f_ap: $('#new_fechaAp').val(), firmado: $('#new_firmado').prop('checked'), comments: $('#new_comments').val(), tipo: $('#new_tipo').val(), status: 0},
        dataType: 'json',
        success: function(array){
            data=array;

            dialogLoad.dialog('close');

            if(data['status']==1){
              showNoty('success','CxC Guardado Correctamente', 4000);
                location.reload();
              notSave();
            }else{
              showNoty('error',data['msg'],6000);
            }

          },
        error: function(){
          dialogLoad.dialog('close');
          showNoty('error', 'Error de conexión',4000);
        }

      });
    }
    
    form = dialogCxC.find( "form" ).on( "submit", function( event ) {
      event.preventDefault();
      addCxC();
    });
    
    dialogPayCxC = $('#cxcPay').dialog({
      autoOpen: false,
      modal: true,
      height:  "auto",
      width: 400,
      buttons: {
        "Asignar": function(){
            payCxC();
            },
        Cancel: function(){
            formPay[ 0 ].reset();
            $('#d_ap, #new_fechaCxC, #new_fechaAp, #d_inicio').periodpicker('clear');
            dialogPayCxC.dialog('close');
        }
      }
    });
    
    formPay = dialogPayCxC.find( "form" ).on( "submit", function( event ) {
      event.preventDefault();
      payCxC();
    });
    
    function payCxC(){
        
        showLoader('Asignando Pagos');
        
        checkPay="";
        
        $('.cxcPayment').each(function(){
            if($(this).prop('checked')){
                checkPay=checkPay+","+$(this).attr('idcxc');
            }
            
            console.log($(this).attr('idcxc')+": "+$(this).prop('checked')+"|"+$(this).attr('idcxc'));
        });
        
        $.ajax({
           url: 'payCxC.php',
            type: 'POST',
            data: {ids: checkPay},
            dataType: 'json',
            success: function(array){
                data=array;
            
                dialogLoad.dialog('close');
            
                if(data['status']==1){
                    showNoty('success','Pagos Aplicados',4000);
                    
                    location.reload();
                }else{
                    showNoty('error',data['msg'],4000);
                }
            },
            error: function( jqXHR, textStatus, errorThrown ) {
                dialogLoad.dialog('close');
                if (jqXHR.status === 0) {

                  showNoty('error','Not connect: Verify Network.',4000);

                } else if (jqXHR.status == 404) {

                  showNoty('error','Requested page not found [404]',4000);

                } else if (jqXHR.status == 500) {

                  showNoty('error','Internal Server Error [500].',4000);

                } else if (textStatus === 'parsererror') {

                  showNoty('error','Requested JSON parse failed.',4000);

                } else if (textStatus === 'timeout') {

                  showNoty('error','Time out error.',4000);

                } else if (textStatus === 'abort') {

                  showNoty('error','Ajax request aborted.',4000);

                } else {

                  showNoty('error','Uncaught Error: ' + jqXHR.responseText,4000);

                }

              }
        });
    }
    
    //Forma Cambio de Puesto
    dialogCambio = $('#newAssign').dialog({
      autoOpen: false,
      modal: true,
      height:  "auto",
      width: 400,
      buttons: {
        "Asignar": function(){
            sendCambio();
            },
        Cancel: function(){
            formCambio[ 0 ].reset();
            $('#ch_fecha, #newinicio').periodpicker('clear');
            dialogCambio.dialog('close');
        }
      }
    });
    
    formCambio = dialogCambio.find( "form" ).on( "submit", function( event ) {
      event.preventDefault();
      sendChange();
    });
    
    //Forma Baja
    dialogBaja = $('#newBaja').dialog({
      autoOpen: false,
      modal: true,
      height:  "auto",
      width: 400,
      buttons: {
        "Asignar": function(){
            sendBaja();
            },
        Cancel: function(){
            formBaja[ 0 ].reset();
            $('#ch_fecha, #newinicio').periodpicker('clear');
            dialogBaja.dialog('close');
        }
      }
    });
    
    formBaja = dialogBaja.find( "form" ).on( "submit", function( event ) {
      event.preventDefault();
      sendBaja();
    });
    
    $('#cambio').click(function(){
       dialogCambio.dialog('open'); 
    });
    
    $('#baja').click(function(){
       dialogBaja.dialog('open'); 
    });
    
    //Populate Deps
    function populateDeps(tipo){
        switch(tipo){
          case 'dep':
            listElement = $('#new');
            msgLoader = "Buscando departamentos vacantes";
            break;
          case 'puesto':
            listElement = $('#newpuesto');
            msgLoader = "Buscando puestos vacantes";
            break;
          case 'ciudad':
            listElement = $('#new_ciudad');
            msgLoader = "Buscando ciudades con vacantes";
            break;
          case 'pdv':
            listElement = $('#new_pdv');
            msgLoader = "Buscando PDVs con vacantes";
            break;
        }



        showLoader(msgLoader);

        $.ajax({
          url: '/config/vacantes_listPopulate.php',
          type: 'POST',
          data: {ingreso: $('#ch_fecha').val(), dep: $('#new').val(), ciudad: $('#new_ciudad').val(), oficina: $('#new_pdv').val(), tipo: tipo},
          dataType: 'json',
          success: function(array){
              data=array;

              dialogLoad.dialog('close');

              switch(tipo){
                case 'ciudad':
                  $('#new_ciudad').val('').empty();
                  $('#new_pdv').val('').empty();
                  $('#new').val('').empty();
                  $('#newpuesto').val('').empty();
                  break;
                case 'pdv':
                  $('#new_pdv').val('').empty();
                  $('#new').val('').empty();
                  $('#newpuesto').val('').empty();
                  break;
                case 'dep':
                  $('#new').val('').empty();
                  $('#newpuesto').val('').empty();
                  break;
                case 'puesto':
                  $('#newpuesto').val('').empty();
                  break;
              }

              if(data['error']==1){

                showNoty('error', data['msg'],4000);

              }else{

                listElement.append('<option value="">Selecciona...</option>');

                $.each(data['vac'], function(i,info){
                  if(tipo=='puesto'){
                    listElement.append('<option value="' + info.id + '" esquema="'+ info.esquema +'" plaza="'+info.plaza+'">' + info.desc + '</option>');
                  }else{
                    listElement.append('<option value="' + info.id + '">' + info.desc + '</option>');
                  }

                });


              }

              

            },
          error: function(){
            dialogLoad.dialog('close');
            showNoty('error', 'Error de conexión',4000);
          }

        });

      }
    
    $('#new_ciudad').change(function(){
        populateDeps('pdv');
      });
    
    $('#new_pdv').change(function(){
        populateDeps('dep');
      });
    
    $('#new').change(function(){
        populateDeps('puesto');
      });
    
    $('#newreplace').change(function(){
        if($(this).prop('checked')){
            $('#newinicio').prop('required',true).periodpicker('enable');
            
            if($('#ch_fecha').val()!=''){
              $('#newinicio').val($('#inicio').val()).periodpicker('change');
            }
          }else{
            $('#newinicio').prop('required',false).val('').periodpicker('clear').periodpicker('disable');
          }
    });
    
    $('#baja_replace').change(function(){
        if($(this).prop('checked')){
            $('#baja_inicio').prop('required',true).periodpicker('enable');
            
            if($('#baja_fecha').val()!=''){
              $('#baja_inicio').val($('#inicio').val()).periodpicker('change');
            }
          }else{
            $('#baja_inicio').prop('required',false).val('').periodpicker('clear').periodpicker('disable');
          }
    });
    
    function sendCambio(){
        showLoader('Registrando Solicitud');
        
        $.ajax({
            url: 'solicitudBajaCambio.php',
            type: 'POST',
            data: {id: <?php echo $asesor->idAsesor; ?>, tipo: 1, fecha: $('#ch_fecha').val(), vacante: $('#newpuesto option:selected').attr('plaza'), replace: $('#newreplace').prop('checked'), f_replace: $('#newinicio').val(),commentarios: $('#ch_comments').val()},
            dataType: 'json',
            success: function(array){
                data=array;
                
                dialogLoad.dialog('close');
                
                if(data['status']==1){
                    showNoty('success','Solicitud Enviada',4000);
                    dialogCambio.dialog('close');
                    formCambio[ 0 ].reset();
                    $('#ch_fecha, #newinicio').periodpicker('clear');
                    location.reload();
                }else{
                    showNoty('error',data['msg'],4000);
                }
            },
            error: function( jqXHR, textStatus, errorThrown ) {
                dialogLoad.dialog('close');
                if (jqXHR.status === 0) {

                  showNoty('error','Not connect: Verify Network.',4000);

                } else if (jqXHR.status == 404) {

                  showNoty('error','Requested page not found [404]',4000);

                } else if (jqXHR.status == 500) {

                  showNoty('error','Internal Server Error [500].',4000);

                } else if (textStatus === 'parsererror') {

                  showNoty('error','Requested JSON parse failed.',4000);

                } else if (textStatus === 'timeout') {

                  showNoty('error','Time out error.',4000);

                } else if (textStatus === 'abort') {

                  showNoty('error','Ajax request aborted.',4000);

                } else {

                  showNoty('error','Uncaught Error: ' + jqXHR.responseText,4000);

                }

              }
        });
        
    }
    
    function sendBaja(){
        showLoader('Registrando Solicitud');
        
        $.ajax({
            url: 'solicitudBajaCambio.php',
            type: 'POST',
            data: {id: <?php echo $asesor->idAsesor; ?>, tipo: 2, fecha: $('#baja_fecha').val(), replace: $('#baja_replace').prop('checked'), f_replace: $('#baja_inicio').val(),commentarios: $('#baja_comments').val()},
            dataType: 'json',
            success: function(array){
                data=array;
                
                dialogLoad.dialog('close');
                
                if(data['status']==1){
                    showNoty('success','Solicitud Enviada',4000);
                    dialogBaja.dialog('close');
                    formBaja[ 0 ].reset();
                    $('#ch_fecha, #newinicio').periodpicker('clear');
                    location.reload();
                }else{
                    showNoty('error',data['msg'],4000);
                }
            },
            error: function( jqXHR, textStatus, errorThrown ) {
                dialogLoad.dialog('close');
                if (jqXHR.status === 0) {

                  showNoty('error','Not connect: Verify Network.',4000);

                } else if (jqXHR.status == 404) {

                  showNoty('error','Requested page not found [404]',4000);

                } else if (jqXHR.status == 500) {

                  showNoty('error','Internal Server Error [500].',4000);

                } else if (textStatus === 'parsererror') {

                  showNoty('error','Requested JSON parse failed.',4000);

                } else if (textStatus === 'timeout') {

                  showNoty('error','Time out error.',4000);

                } else if (textStatus === 'abort') {

                  showNoty('error','Ajax request aborted.',4000);

                } else {

                  showNoty('error','Uncaught Error: ' + jqXHR.responseText,4000);

                }

              }
        });
        
    }
    
    function cxlSol(sol){
        showLoader('Cancelando Solicitud');
        
        $.ajax({
            url: 'solicitudBajaCambio.php',
            type: 'POST',
            data: {solicitud: sol, tipo: 3},
            dataType: 'json',
            success: function(array){
                data=array;
                
                dialogLoad.dialog('close');
                
                if(data['status']==1){
                    showNoty('success','Solicitud Enviada',4000);
                    dialogBaja.dialog('close');
                    formBaja[ 0 ].reset();
                    $('#ch_fecha, #newinicio').periodpicker('clear');
                    location.reload();
                }else{
                    showNoty('error',data['msg'],4000);
                }
            },
            error: function( jqXHR, textStatus, errorThrown ) {
                dialogLoad.dialog('close');
                if (jqXHR.status === 0) {

                  showNoty('error','Not connect: Verify Network.',4000);

                } else if (jqXHR.status == 404) {

                  showNoty('error','Requested page not found [404]',4000);

                } else if (jqXHR.status == 500) {

                  showNoty('error','Internal Server Error [500].',4000);

                } else if (textStatus === 'parsererror') {

                  showNoty('error','Requested JSON parse failed.',4000);

                } else if (textStatus === 'timeout') {

                  showNoty('error','Time out error.',4000);

                } else if (textStatus === 'abort') {

                  showNoty('error','Ajax request aborted.',4000);

                } else {

                  showNoty('error','Uncaught Error: ' + jqXHR.responseText,4000);

                }

              }
        });
        
    }
    
    $('.cxlSol').click(function(){
         cxlSol($(this).attr('idSol'));
    })
});
</script>


<!-- Latest compiled and minified CSS -->
<link rel='stylesheet' href='/styles/bootstrap/css/bootstrap.min.css' integrity='sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u' crossorigin='anonymous'>

<!-- Optional theme -->
<link rel='stylesheet' href='/styles/bootstrap/css/bootstrap-theme.min.css' integrity='sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp' crossorigin='anonymous'>

<!-- Latest compiled and minified JavaScript -->
<script src='/styles/bootstrap/js/bootstrap.min.js' integrity='sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa' crossorigin='anonymous'></script>
</div>
<div class='clearfix'></div>


<br>


<div class='container'>
    <div class='row'>
        <div class='col-md-6'>
            <div class='container'>
                <div class='row'>
                    
<!-- GENERALES -->                         
                    <div class='contact-box .col-sm-12'>
                            <div class='col-sm-4'>
                                <div class='text-center'>
                                    <img alt='image' class='img-circle img-responsive' style='margin: auto;' src='/images/no-image.png'>
                                    <h5 class='contact-title ng-binding'><?php echo $asesor->puesto; ?><br><?php echo $asesor->departamento; ?></h5>
                                </div>
                            </div>
                            <div class='col-sm-8'>
                                <h4><strong class='ng-binding'><?php echo $asesor->nombre; ?></strong></h4>
                                <p class='ng-binding'><?php echo $asesor->corto; ?></p>
                                <p class='ng-binding'><span class='glyphicon glyphicon-user'></span>   NC: <?php echo $asesor->numcol; ?></p>
                                <p class='ng-binding'><span class='glyphicon glyphicon-earphone'></span>  <?php echo $asesor->tel; ?></p>
                                <p class='ng-binding'><span class='glyphicon glyphicon-earphone'></span>  <?php echo $asesor->tel2; ?></p>
                                <a href='mailto:<?php echo $asesor->correo; ?>' class='ng-binding'><span class='glyphicon glyphicon-envelope'></span> <?php echo $asesor->correo; ?></a><br>
                                <a href='mailto:<?php echo $asesor->correoPersonal; ?>' class='ng-binding'><span class='glyphicon glyphicon-envelope'></span> <?php echo $asesor->correoPersonal; ?></a>
                            </div>
                            <div class='clearfix'></div>
                    </div>
                </div>
                <div class='row'>
<!-- HISTORIAL -->                         
                    <div class='contact-box .col-sm-12'>
                            
                            <div class='col-sm-8'>
                                <h2 class='text-left bg-primary'><span class='glyphicon glyphicon-th-list'></span> Historial</h2>
                                <?php foreach($asesor->histo_puestos as $id => $info){
                                    echo "<p>$info</p>\n";
                                }?>
                                
                            </div>
                            <div class='col-sm-4 text-center'>
                                <h2></h2>
                                <?php
                                    if($_SESSION['hc_cambios_solicitud']==1){
                                        if(isset($asesor->recontratable)){
                                            if($asesor->recontratable==1){
                                                echo "<h1><br><span class='label label-success'>Recontrable</span></h1>";
                                            }else{
                                                echo "<h1><br><span class='label label-danger'>NO Recontrable</span></h1>";
                                            }

                                        }else{
                                            echo "<p><button type='button' style='width: 125px;' class='btn btn-info btn-lg' id='cambio' ";
                                               if($asesor->egreso!='NA' || $asesor->solPendiente==1){ echo "disabled";} 
                                            echo ">Cambio de<br>Puesto</button></p>
                                                    <p><button type='button' style='width: 125px;' class='btn btn-danger btn-lg' id='baja' ";
                                               if($asesor->egreso!='NA'){ echo "disabled";} 
                                            echo ">Baja de<br>Asesor</button></p>";
                                        }
                                    }
                                ?>
                                
                            </div>
                            
                            <div class='clearfix'></div>
                    </div>
                </div>
            </div>
        </div>
        <div class='col-md-6'>
            <div class='contact-box col-lg-12' style='padding-left: 20px;'>
                
<!-- CONTRATACION -->               
                <div class='row'>
                <h2 class='text-left bg-primary'><span class='glyphicon glyphicon-time'></span> Contratación</h2>
                
                    <div class='col-sm-4 text-center'>
                        <h3>Ingreso</h3>
                        <p class='text-center'><?php echo $asesor->ingreso; ?></p>

                    </div>
                    <div class='col-sm-4 text-center'>
                        <h3>Status</h3>
                        <p class='text-center'><?php echo $asesor->status; ?></p>
                    </div>
                    <div class='col-sm-4 text-center'>
                        <h3>Egreso</h3>
                        <p class='text-center'><?php echo $asesor->egreso; ?></p>
                    </div>
                </div>
                
<!-- CONTRATO -->                   
                <div class='row'>
                <h2 class='text-left bg-primary'><span class='glyphicon glyphicon-list-alt'></span> Contrato</h2>
                    <div class='col-sm-4 text-center'>
                        <h3>Tipo</h3>
                        <p class='text-center'><?php echo $asesor->contrato_tipo; ?></p>

                    </div>
                    <div class='col-sm-4 text-center'>
                        <h3>Status</h3>
                        <p class='text-center'><?php echo $asesor->contrato_status; ?></p>
                    </div>
                    <div class='col-sm-4 text-center'>
                        <h3>Fin</h3>
                        <p class='text-center'><?php echo $asesor->contrato_fin; ?></p>
                    </div>
                
                </div>
                
<!-- CXC -->                   
                <div class='row'>
                <h2 class='text-left bg-primary'><span class='glyphicon glyphicon-usd'></span> CXC
                    <?php if($_SESSION['cxc_registro']==1){ echo " -> 
                    <button type='button' class='btn btn-info btn-sm' id='addCxC'>Agregar CxC</button>"; }
                    
                    if($_SESSION['cxc_apply']==1){ echo "<button type='button' class='btn btn-success btn-sm' id='payCxC'>Saldar CxC</button>"; }
                    ?>
                    </h2>
                    <div class='col-sm-4 text-center'>
                        <h3>Total</h3>
                        <p class='text-center'>$<?php echo number_format($asesor->cxc_total,2); ?></p>

                    </div>
                    <div class='col-sm-4 text-center'>
                        <h3>Adeudo</h3>
                        <p class='text-center'>$<?php echo number_format($asesor->cxc_adeudo,2); ?></p>
                    </div>
                    <div class='col-sm-4 text-center'>
                        <h3>Sig. Quincena</h3>
                        <p class='text-center'>$<?php echo number_format($asesor->cxc_quincena,2); ?></p>
                    </div>
                </div>

<!-- SANCIONES -->             
                <div class='row'>
                <h2 class='text-left bg-primary'><span class='glyphicon glyphicon-warning-sign'></span> Sanciones</h2>
                    <div class='col-sm-4 text-center'>
                        <h3>Actas</h3>
                        <p class='text-center'><?php echo $asesor->sanc['Acta']['Total']." | ".$asesor->sanc['Acta']['Vigentes']; ?></p>

                    </div>
                    <div class='col-sm-4 text-center'>
                        <h3>Acciones</h3>
                        <p class='text-center'><?php echo $asesor->sanc['Accion']['Total']." | ".$asesor->sanc['Accion']['Vigentes']; ?></p>
                    </div>
                    <div class='col-sm-4 text-center'>
                        <h3>P.Performance</h3>
                        <p class='text-center'><?php echo $asesor->sanc['PPerformance']['Total']." | ".$asesor->sanc['PPerformance']['Vigentes']; ?></p>
                    </div>
                </div>
                
            </div>
        </div>
       
    </div>
</div>

<div class='clearfix'></div>

<!-- CXC FORM -->  
<div id='cxcForm' style='display: block; width: 300px; margin: auto'>
  <div id="accordion-cxcCreate">
    <h3>Agregar CxC</h3>
    <div class='form-group'>
        <form>
          <fieldset id='fieldset_CXC'>
            <label for="new_asesor">Asesor</label>
            <input class='form-control' title='Asesor' type='text' id='new_asesor' name="new_asesor" placeholder='Nombre del asesor' required><input type='hidden' id='new_asesor_hidden' name="new_asesor_hidden"><br>
            <label for="new_loc">Localizador</label>
            <input class='form-control' title='Localizador' type='text' id='new_loc' name="new_loc" placeholder='Localizador' required><br>
            <label for="new_monto">Monto</label>
            <div class="input-group">  
                <span class="input-group-addon">$</span>
                <input class='form-control' title='Monto' type='text' id='new_monto' name="new_monto" placeholder='Monto' required><br>
            </div><br>
            <label for="new_tipo">Tipo</label>
            <select title='Tipo' id='new_tipo' name="new_tipo" required>
                <option value=''>Selecciona...</option>  
                <option value=0>Responsabilidad</option>
                <option value=1>Colaborador</option>
            </select><br><br>
            <label for="new_fechaCxC">Fecha CxC</label>
            <input class='form-control' title='Fecha CxC' type="text" name="new_fechaCxC" id="new_fechaCxC" value='' required><br><br>
            <label for="new_fechaAp">Fecha Aplicación</label>
            <input class='form-control' type="text" name="new_fechaAp" id="new_fechaAp" value=''><br><br>
            <label for="new_firmado">Firmado</label>
            <input type="checkbox" name="new_firmado" id="new_firmado" value=''><br><br>
            <label for="new_comments">Comentarios</label>
            <input class='form-control' type="text" name="new_comments" id="new_comments" value=''><br>
            
          </fieldset>
        </form>
    </div>
  </div>
</div>

<!-- CXC Payment -->  
<div id='cxcPay' style='display: block; width: 300px; margin: auto'>
  <div id="accordion-cxcCreate">
    <h3>Saldar CxC</h3>
    <div class='form-group'>
        <form>
          <fieldset id='fieldset_PayCXC'>
            <?php
              $i=0;
              foreach($asesor->cxc_pend as $id => $info){
                  //echo "<label for='pay_$i'>".$info['Localizador']." -> ".$info['Fecha']." ($".number_format($info['Monto'],2).") => </label><input type='checkbox' name='pay_$i' id='pay_$i' value='' class='cxcPayment'><br><br>";
                  echo "<div class='input-group'>
                          <span class='input-group-addon'>
                          <input type='checkbox' name='pay_$i' id='pay_$i' idcxc='$id' value='' class='cxcPayment'>
                          </span>
                          <input type='text' class='form-control' value='".$info['Localizador']." -> ".$info['Fecha']." ($".number_format($info['Monto'],2).")' readonly> 
                        </div>";
                  $i++;
              }
            ?>
            
          </fieldset>
        </form>
    </div>
  </div>
</div>

<div id='newAssign' style='width: auto; margin: auto'>
    <div class='form-group'>
        <form>
            <fieldset id='fieldset_CXC'>
                <div class="input-group"> 
                    Fecha de Cambio<br>
                    <input title='Fecha de Cambio' type="text" name="ch_fecha" id="ch_fecha" value='' required><br><br>
                </div><br>
                <div class="input-group"> 
                    <label for="new_ciudad">Ciudad</label>
                    <select class='form-control' id="new_ciudad" name="new_ciudad" title="Ciudad" required><option value=''>Selecciona...</option></select>
                </div><br>
                <div class="input-group"> 
                    <label for="new_pdv">Oficina</label>
                    <select class='form-control' id="new_pdv" name="new_pdv" title="PDV" required><option value=''>Selecciona...</option></select>
                </div><br>
                <div class="input-group"> 
                    <label for="new">Departamento</label>
                    <select class='form-control' id="new" name="new_pdv" title="Departamento" required><option value=''>Selecciona...</option></select>
                </div><br>
                <div class="input-group"> 
                    <label for="newpuesto">Puesto</label>
                    <select class='form-control' id="newpuesto" name="newpuesto" title="Puesto" required><option value=''>Selecciona...</option></select>
                </div><br>
                <div class="input-group"> 
                    <label for="ch_comments">Comentarios</label>
                    <input class='form-control' type="text" name="ch_comments" id="ch_comments" value=''>
                </div>
                <div class="checkbox"> 
                    <label>
                    <input type='checkbox' name='newreplace' id='newreplace' checked>Reemplazable</label>
                </div><br>
                <div class="input-group"> 
                    Liberacion Vacante<br>
                    <input  type='text' id='newinicio'>
                </div><br>
            </fieldset>
        </form>
    </div>
</div>

<div id='newBaja' style='width: auto; margin: auto'>
    <div class='form-group'>
        <form>
            <fieldset id='fieldset_CXC'>
                <div class="input-group"> 
                    Fecha de Baja<br>
                    <input title='Fecha de Baja' type="text" name="baja_fecha" id="baja_fecha" value='' required><br><br>
                </div>
                <div class="input-group"> 
                    <label for="baja_comments">Comentarios</label>
                    <input class='form-control' type="text" name="baja_comments" id="baja_comments" value=''>
                </div>
                <div class="checkbox"> 
                    <label>
                    <input type='checkbox' name='baja_replace' id='baja_replace' checked>Reemplazable</label>
                </div>
                <div class="input-group"> 
                    Liberacion Vacante<br>
                    <input  type='text' id='baja_inicio'>
                </div><br>
            </fieldset>
        </form>
    </div>
</div>
