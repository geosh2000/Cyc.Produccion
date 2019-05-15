<?
$registro=$_GET["reg"];
$regexc=$_GET["reg"];
date_default_timezone_set('America/Bogota');

//Parametros para accesar SQL
$r=0;
while ($r <= 200) {
$old=$_GET["old"];
$id[$r]=$_GET["id".$r];

$tiempo[$r]=$_GET["t".$r];
$control[$r]=$_GET["c".$r];
$horario[$r]=$_GET["h".$r];
$exc[$r]=$_GET["e".$r];

$r++;
}
$fecha=$_GET["fecha"];

$id1=$id[1];
$mes=intval(date('m'));
$dia=intval(date('d'));
$rcode1=$exc[1];

$username="comeycom_wfm";
$password="pricetravel2015";
$database="comeycom_SLA";

//Conectar a DB

mysql_connect(localhost,$username,$password);
@mysql_select_db($database) or die( "Unable to select database");



$i=0;
while ($i<=200){
if ($id[$i] === null){
$i++;
}else{
switch ($registro){
	case 1:
		$query="UPDATE `PyA Monitor` SET Tiempo='$tiempo[$i]', Control='$control[$i]', Fecha='$fecha' WHERE id='$id[$i]'";
		
		break;
	case 2:
		$query="UPDATE `PyA Monitor` SET Horario='$horario[$i]' WHERE id='$id[$i]'";
		break;
	case 3:
		$query="UPDATE `PyA Monitor` SET Excepcion='$exc[$i]' WHERE id='$id[$i]'";
		break;
}
mysql_query($query);
$i++;
}
}

$query1 = "SELECT * FROM `Historial PyA` WHERE (idnumber='$id1' AND mes='$mes' AND dia='$dia')";

$result1=mysql_query($query1);
$num1=mysql_numrows($result1);





?>
<script>
function updateStatus(str) {
    if (str == "") {
        document.getElementById("resultado").innerHTML = "";
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
                document.getElementById("resultado").innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET","GlobalTestReturnRetardo.php?id="+str,true);
        xmlhttp.send();
    }
}
</script>

<?

$usermaxuploads = mysql_query("SELECT MAX(indice) as ind FROM `Historial PyA`");
$usermaxuploadsrow = mysql_fetch_array($usermaxuploads);
$maxvar = $usermaxuploadsrow["ind"];


$indice=$maxvar+1;
mysql_query($query2);



if($registro==3){
if($num1!=0){
$query2 = "UPDATE `Historial PyA` SET rcode='$rcode1',tiempo='$old' WHERE (idnumber='$id1' AND mes='$mes' AND dia='$dia')";
}else{

$query2 = "INSERT INTO `Historial PyA` (idnumber,dia,mes,rcode,tiempo,indice) VALUES (".$id1.",".$dia.",".$mes.",".$rcode1.",".$old.",".$indice.")";
}


mysql_query($query2);
switch($exc[1]){
	case 0:
		if($old==NULL){
		echo "Record Updated ";
		}else{
		echo $old;
		}
		break;
	case 1:
		echo "Notificado";
		break;
	case 2:
		echo "Justificado";
		break;
	case 3:
		echo "Regresado";
		break;
	case 4:
		echo "No Aplica";
		break;
	case 5:
		echo "Registrado (".$old.")";
		break;
	
}}else{
echo "Record Updated ".$queryh1;
}

if($registro==1){
$i=0;


while($i<200){
if($control[$i]==1 && $id[$i]!=NULL){
			$queryh = "SELECT * FROM `Historial PyA` WHERE (idnumber='$id[$i]' AND mes='$mes' AND dia='$dia')";
			$resulth=mysql_query($queryh);
			$numh=mysql_numrows($resulth);
			
	
			if($numh==0){
			
			$queryh1 = "INSERT INTO `Historial PyA` (idnumber,dia,mes,rcode,tiempo,indice) VALUES (".$id[$i].",".$dia.",".$mes.",0,".$tiempo[$i].",".$indice.")";
			$indice++;
			mysql_query($queryh1);
			
			}
			
			
			
			
		}
		

$i++;
}
		
}


mysql_close();
?>
<div id="resultado"></div>