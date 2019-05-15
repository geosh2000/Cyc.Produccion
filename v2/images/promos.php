<?php

include_once("../modules/modules.php");

initSettings::start(false);

$pantalla=$_GET['pantalla'];

if(!isset($_GET['pantalla'])){$pantalla='Ventas1';}

?>

<style>
body{
    margin-right: 0px;
    margin-top: 0px;
    margin-left: 0px;
    margin-bottom: 0px;
}

.container{
    background: #C8EEEF;
    height: 1920px;
    width: 1080px;

}

</style>

<script>
$(function(){

    setInterval(function(){
       sendRequest();

    },1000);

    $('#addDiv').click(function(){
        $('#all').prepend("<div class='container'><img src='/images/pantallas/Incentivo - Me Cancun.jpg' width='1080' height='1920'></div>");
    });

    flag=0;

    setInterval(function(){
       elements=$( ".container" ).length;
       if(flag<elements){
            $( ".container" ).fadeOut(1000);
                setTimeout(function(){$( ".container" ).eq(flag).fadeIn(1000);
                flag++;
            },1000);

       }else{
           flag=0;
       }

    },10000);



    function sendRequest(){

    	$('.container').addClass('inactive');

    	$.ajax({
	            url: "query_pantallas.php",
	            type: 'POST',
	            data: { pantalla: "<?php echo $pantalla; ?>"},
	            dataType: 'json', // will automatically convert array to JavaScript
	            success: function(array) {
	                data=array;

	                index=parseInt(data['result']);

	                for( x=0; x<index; x++){

	                    if($('#'+data[x]['id']).length==0){
	                        $('#all').prepend("<div id='"+data[x]['id']+"' class='container'><img src='"+data[x]['src']+"' width='1080' height='1920'></div>");
	                    }else{
	                        $('#'+data[x]['id']).removeClass('inactive');
	                    }
	                }
	            	$('.inactive').remove();
				}

	    });

     }
});
</script>

<div id='all'>

</div>
