$(function(){
	
	$("#login").hide();
/*
    $( "#f_other" ).autocomplete({
       source: 'search_other.php'
    });

    $( "#f_localidad" ).autocomplete({
        source: 'search_localidad.php'
    });

    $( "#f_nombre" ).autocomplete({
        source: 'search_nombre.php'
    });
*/
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

    function sendRequest(variables){

    var urlok='query_func.php';
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
                    $('#error').text("");
                    $('.input').val('');
                    $( '#regs' ).attr( 'src', function ( i, val ) { return val; });
                    startFields();
                }else{
                    if(status[1]=='DISC'){
                        tipo_noti='error';
                        startlogin='ok';
                    }else{
                        $('#error').text(urlok+"?"+variables);
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
				$('#submit_form').prop('disabled',false);
            }
        }
        xmlhttp.open("POST",urlok,true);
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xmlhttp.send(variables);

    }

    function startFields(){
    	$('.containers, .opts, .default').hide();
    	$('.camposel').removeClass('error').val("").attr('req',0);
    	$('#f_actividad').attr('req',1);
    }
    
    startFields();

    //BUTTON Submit
    $('#submit_form').click(function(){
    	$('#submit_form').prop('disabled',true);
        val_flag=true;
        $('.camposel').each(function(){
        	if($(this).attr('req')==1){
        		if($(this).val()==""){
        			val_flag=false;
        			$(this).addClass('error');
        			//alert(this.id)
        		}else{
        			$(this).removeClass('error');
        		}
        	}else{
    			$(this).removeClass('error');
    		}	
        });
        if(val_flag){
            var variables="asesor="+asesor;
            $('.camposel').each(function(){
            	variables=variables+'&' + this.id +'=' + $(this).val();
            });
			//alert(variables);
            sendRequest(variables);
            
        }else{
        	$('#submit_form').prop('disabled',false);
        }
    });
    
    //Change to UPPERCASE
    $('#f_pnr, #f_codigo_aerolinea').keyup(function(){
        var name=$(this).val();
        var newname=name.toUpperCase();
        $(this).val(newname);
    });
    
    $('.containers, .opts, .default').hide();
    
    function setDefaults(){
    	
    	em=$('option:selected', $('#f_actividad')).attr('em');
    	loc=$('option:selected', $('#f_actividad')).attr('loc');
    	pnr=$('option:selected', $('#f_actividad')).attr('pnr');
    	
    	if(em!=1){
    		$('#contain-em').fadeOut('500');
    		$('#f_em').val("").attr('req',0);
        }else{
        	$('#contain-em').fadeIn('500');
    		$('#f_em').attr('req',1);
        }
        if(loc!=1){
    		$('#contain-loc').fadeOut('500');
    		$('#f_loc').val("").attr('req',0);
        }else{
        	$('#contain-loc').fadeIn('500');
    		$('#f_loc').attr('req',1);
        }
        
        if(pnr!=1){
    		$('#contain-pnr').fadeOut('500');
    		$('#f_pnr').val("").attr('req',0);
        }else{
        	$('#contain-pnr').fadeIn('500');
    		$('#f_pnr').attr('req',1);
        }
        
    }
    
    //Check if array exists
    Array.prototype.check = function() {
	    var arr = this, i, max_i;
	    for (i = 0, max_i = arguments.length; i < max_i; i++) {
	        arr = arr[arguments[i]];
	        if (arr === undefined) {
	            return false;
	        }
	    }
	    return true;    
	}
    
    //Sel Actividad
    $('#f_actividad').change(function(){
    	$('.camposel').removeClass('error');
    	actividad=$(this).val();
    	setDefaults();
    	$('.opts, .containers').hide();
    	$('.input').val("");
    	$('.act_'+actividad).show();
    	$('.containers').each(function(){
    			this_id=this.id;
		   		if($('#'+this_id+' > .opcion > .seleccion > .input').children('.act_'+actividad).length>0 || $('#'+this_id+' > .opcion > .seleccion').children('.act_'+actividad).length>0){
		   			if(opciones.check($('#'+this_id).attr('level'),actividad,'titulo')){
		   				$('#'+this_id+' > .name > p').text(opciones[$('#'+this_id).attr('level')][actividad]['titulo']);
		   			}
		   			if($('#'+this_id+' > .opcion > .seleccion > .input > .act_'+actividad).attr('parent')==0 || $('#'+this_id+' > .opcion > .seleccion > .act_'+actividad).attr('parent')==0){
		   				$(this).removeClass('hide_input').removeClass('inactive');
		   				$('#'+this_id).removeClass('hide_input').removeClass('inactive');
			   			$('#'+this_id+' > .opcion > .seleccion > .input').attr('req',1);
			   			$(this).fadeIn('500');	
		   			}else{
		   				$(this).addClass('hide_input').addClass('inactive');	
		   			}
		   			
		   		}else{
		   			//alert('#'+this_id+' > .opcion > .seleccion > .input // .act_'+actividad );
		   			$(this).addClass('hide_input').addClass('inactive');
		   			$('#'+this_id+' > .opcion > .seleccion > .input').attr('req',0);
		   			$(this).fadeOut('500');
		   		}
		   });
		   
    });
    
    //Level Select
    $('.levelSelect').change(function(){
    	$('.camposel').removeClass('error');
    	parent_id=$(this).parent().parent().parent().attr('id');	
    	$('.inactive').fadeOut('500');
    	$('.inactive > .opcion > .seleccion > .input').attr('req',0).val("");
    	var actividad=$('#f_actividad').val();
    	var parent=$(this).val();
    	var trig=$('#input_'+parent).attr('trig');
    	setDefaults();
    	if(trig=='-1'){
    		$('#contain-em').fadeIn('500');
    		$('#f_em').attr('req',1);
    	}
    	if(trig=='-2'){
    		$('#contain-loc').fadeIn('500');
    		$('#f_loc').attr('req',1);
    	}
    	if(trig=='-3'){
    		$('#contain-pnr').fadeIn('500');
    		$('#f_pnr').attr('req',1);
    	}
    	var levelid=this.id;
    	$('.hide_input').each(function(){
	    			this_id=this.id;
			   		if($('#'+this_id+' > .opcion > .seleccion > .input').children('.act_'+actividad).length>0 || $('#'+this_id+' > .opcion > .seleccion').children('.act_'+actividad).length>0){
			   			if(opciones.check($('#'+this_id).attr('level'),actividad,'titulo')){
			   				$('#'+this_id+' > .name > p').text(opciones[$('#'+this_id).attr('level')][actividad]['titulo']);
			   			}
			   			if(this_id!=parent_id){
				   			if($('#'+this_id+' > .opcion > .seleccion > .input > .act_'+actividad).attr('parent')==parent || $('#'+this_id+' > .opcion > .seleccion > .act_'+actividad).attr('parent')==parent){
					   			$('#'+this_id+' > .opcion > .seleccion > .input').attr('req',1);
					   			$(this).fadeIn('500').removeClass('inactive');	
				   			}else{
				   				$('#'+this_id+' > .opcion > .seleccion > .input').attr('req',0);
				   				$(this).fadeOut('500').removeClass('inactive').addClass('inactive');	
				   			}
				   		}
			   		}else{
			   			$('#'+this_id+' > .opcion > .seleccion > .input').attr('req',0);
			   			$(this).fadeOut('500').removeClass('inactive').addClass('inactive');
			   		}
			   });
		
    });
    
});