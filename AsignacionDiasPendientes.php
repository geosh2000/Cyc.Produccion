<?php
include("DBAsesores.php");
include("DBDiasPendientes.php");


//Sort Names Alphabetically
asort($ASNCorto);
    $i=0;
    foreach($ASNCorto as $key => $val){
    $Name[$i]=$val;
    $Id[$i]=$ASid[$key];
    $i++;
    }
    



?>

<script>

function getID(id) {
    
    if (id == "") {
        document.getElementById("showIn").innerHTML = "";
        return;
    } else { 
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("showIn").innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET","diaspendientes/showInfoDiasPendientes.php?id="+id,true);
        xmlhttp.send();
        
        
    }
}

	
</script>


<div style="align:center;">Asignaci&oacuten de D&iacuteas Pendientes</div>
<form><select name="Nombre" onchange="getID(this.value)">
    <option value="">Select...</option>
    <?php
    $i=0;
    while ($i<$ASnum){
    	echo "<option value=\"".$Id[$i]."\">".$Name[$i]."</option>";
    $i++;
    }
    
    ?>
    </select></form>
<div id="showIn"></div>