
<?php

include_once('../modules/modules.php');

$connectdb_excep=Connection::mysqliDB('CC');

if($_SESSION['monitor_pya_exceptions']!=1){exit;} //Check permissions

function printOptions(){
	global $connectdb_excep;
   	echo "<option value='0'>Selecciona ...</option>";
		$query="SELECT exc_type_id, Excepcion FROM `Tipos Excepciones` ORDER BY Excepcion";
		if($result=$connectdb_excep->query($query)){
			while($fila=$result->fetch_assoc()){
				echo "<option value='".$fila['exc_type_id']."'>".$fila['Excepcion']."</option>";
			}
		}else{
			echo "Error: ".$connectdb_excep->error;
		}

}


?>
  <script>
  $(function() {
    var dialog, form,

      // From http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#e-mail-state-%28type=email%29
      emailRegex = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/,
      name = $( "#name" ),
      email = $( "#email" ),
      password = $( "#password" ),
      allFields = $( [] ).add( name ).add( email ).add( password ),
      tips = $( ".validateTips" );

    function updateTips( t ) {
      tips
        .text( t )
        .addClass( "ui-state-highlight" );
      setTimeout(function() {
        tips.removeClass( "ui-state-highlight", 1500 );
      }, 500 );
    }

    function checkLength( o, n, min, max ) {
      if ( o.val().length > max || o.val().length < min ) {
        o.addClass( "ui-state-error" );
        updateTips( "Length of " + n + " must be between " +
          min + " and " + max + "." );
        return false;
      } else {
        return true;
      }
    }

    function checkRegexp( o, regexp, n ) {
      if ( !( regexp.test( o.val() ) ) ) {
        o.addClass( "ui-state-error" );
        updateTips( n );
        return false;
      } else {
        return true;
      }
    }

    function addUser() {
    	var ncorto=$('#name').val();
    var str=$("#excep");
     var id=$("#a_id");
     var caso=$("#case");
     var ok_target=$("#target");
     var notes=$("#notes");
     var date=$("#date");
     var target=ok_target.val();
     var hid_ok=$("#hid");
     var flag=$("#flag").val();
     var reg=$("#reg").val();
     var user=$("#user").val();
     var iden=$("#iden").val();
        var valid = true;
      allFields.removeClass( "ui-state-error" );

      switch(str.val()){
           case '3':
           case '8':
           case '12':
            valid = valid && checkLength( caso, "Caso", 6, 8 );
            valid = valid && checkRegexp( caso, /^([0-9])+$/, "Case only accepts 0-9" );
            break;
           default:
            $("#case").attr("readonly",true);
            $("#case").attr("required",false);
            break;


       }






	      if ( valid ) {

	     var ok_url="/pya-monitor/exceptions.php?excep="+str.val()+"&asesor="+id.val()+"&h="+hid_ok.val()+"&caso1="+caso.val()+"&notes="+notes.val()+"&fecha="+date.val();

			 $.ajax({
				 url: '/pya-monitor/exceptions.php',
				 type: 'POST',
				 data: {excep: str.val(), asesor: id.val(), h: hid_ok.val(), caso1: caso.val(), notes: notes.val(), fecha: date.val()},
				 dataType: 'html',
				 success: function(data){
					 if(flag==1){

					 }else{
							$('#'+target).html(ncorto+"<br>"+data).removeClass('flashredpya');
							$('#h_'+target).html(ncorto+"<br>"+data);
					 }
				 	}
			 });

	      flag_reload=true;
	      dialog.dialog( "close" );
	      //sendRequest(iden,reg,2);
	    }

      return valid;
  }

    dialog = $( "#dialog-form" ).dialog({
      autoOpen: false,
      height: 400,
      width: 620,
      modal: true,

      buttons: {
        "Enviar": addUser,
        Cancel: function() {
            flag_reload=true;
            dialog.dialog( "close" );
        }
      },
      close: function() {
            flag_reload=true;
            form[ 0 ].reset();
            allFields.removeClass( "ui-state-error" );
      }
    });

    form = dialog.find( "form" ).on( "submit", function( event ) {
      event.preventDefault();
      addUser();
    });

    $( '.block, .blockhora' ).click(function() {
      $('#name').val($(this).attr("ncorto"));
      $('#a_id').val($(this).attr("asesorid"));
      $('#date').val($(this).attr('fecha'));
      $('#target').val($(this).attr("asesorid"));
      $('#hid').val($(this).attr('horaid'));
      flag_reload=false;

      dialog.dialog( "open" );
    });



    $("#excep").change(function(){
       switch($(this).val()){
           case '3':
           case '8':
           case '12':
            $("#case").attr("readonly",false);
            $("#case").attr("required",true);
            break;
           default:
            $("#case").attr("readonly",true);
            $("#case").attr("required",false);
            break;


       }
    });

    $( '.cancel' ).click(function() {
        if($(this).attr('type')==1 || $(this).attr('type')==2){
          var iden=$(this).attr('title');
          var tipo=$(this).attr('type');
          var reg=$(this).attr('reg');
          var user=$(this).attr('user');
          var asesor=$(this).attr('asesor');
          var iden=$(this).attr('title');
          var x=reg;
          var name_id=asesor;
          var name_corto=$('#nombre_'+x).attr('title');
          var fecha=$('#fecha_'+x).text();
          var hid=$(this).attr('hid');
          var f_name=document.getElementById('name');
          var f_id=document.getElementById('a_id');
          var f_fecha=document.getElementById('date');
          var f_div=document.getElementById('target');
          var f_hid=document.getElementById('hid');
          var f_flag=document.getElementById('flag');
          var f_reg=document.getElementById('reg');
          var f_user=document.getElementById('user');
          var f_iden=document.getElementById('iden');
          f_name.value=name_corto;
          f_id.value=name_id;
          f_fecha.value=fecha;
          f_div.value=x;
          f_hid.value=hid;
          f_flag.value="1";
          f_reg.value=reg;
          f_user.value=user;
          f_iden.value=iden;
          flag_reload=false;
          $('#dialog-form').dialog( "open" );
        }
    });

  });


  </script>
 <div id="dialog-form" title="Nueva Excepcion">
  <p class="validateTips">Fill the required Fields.</p>

  <form>
    <fieldset>
        <table width='550px'>
            <tr>
                <td width='30%'><label for="date">Fecha</label></td>
                <td><input type="text" name="date" id="date" value="" class="text ui-widget-content ui-corner-all" readonly>
                <input type="text" name="target" id="target" value="" hidden />
                <input type="text" name="hid" id="hid" value="" hidden /></td>
            </tr>
            <tr>
                <td width='30%'><label for="a_id">ID</label></td>
                <td><input type="text" name="a_id" id="a_id" value="" class="text ui-widget-content ui-corner-all" readonly></td>
            </tr>
            <tr>
                <td width='30%'><label for="name">Asesor</label></td>
                <td><input type="text" name="name" id="name" value="" class="text ui-widget-content ui-corner-all" readonly></td>
            </tr>
      <tr><td width='30%'><label for="excep">Excepcion</label></td>
      <td><select  class="option ui-widget-content ui-corner-all" name="excep" id="excep" required><?php printOptions(); ?></select></td></tr>
      <tr><td width='30%'><label for="case">Caso</label></td>
      <td><input type="text" name="case" id="case" value="" class="text ui-widget-content ui-corner-all" required='true' readonly></td></tr>
      <tr><td width='30%'><label for="notes">Notas</label></td>
      <td><input type="text" name="notes" id="notes" value="" class="text ui-widget-content ui-corner-all" required='true'>
      <input type="text" name='flag' value='' id='flag' hidden />
      <input type="text" name='reg' value='' id='reg' hidden />
      <input type="text" name='user' value='' id='user' hidden />
      <input type="text" name='iden' value='' id='iden' hidden />
      </td></tr>
      </table>
      <!-- Allow form submission with keyboard without duplicating the dialog button -->
      <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
    </fieldset>
  </form>
</div>
<?php $connectdb_excep->close(); ?>
