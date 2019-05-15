<?php
include("../connectDB.php");
include("../common/scripts.php");
?>

<script>
function closeDialog(){
     $('#dialog-test').dialog('close');
}

$(function(){
    $('#dialog-test')
        .html('<iframe style="border: 0px; " src="nweUser.php" width="100%" height="100%"></iframe>')
        .dialog({
            title: "Login",
            autoOpen: true,
            dialogClass: 'dialog_fixed,ui-widget-header',
            modal: true,
            height: 829,
            minWidth: 1155,
            minHeight: 400,
            draggable:true,
            /*close: function () { $(this).remove(); },*/
            buttons: { "Ok": function () {         $(this).dialog("close"); } }
    });
})
</script>

<div id='dialog-test'></div>

<input type='hidden' id='testval' value="Hola Mundo!">