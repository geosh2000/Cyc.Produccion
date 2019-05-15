<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){echo "status- DISC -status msg- Tu sesion ha expirado. Por favor da click en el menu para volver a loguearte. -msg"; exit;}

include("../connectDB.php");
header("Content-Type: text/html;charset=utf-8");

//Variables

function createPost($name){
    global $data;
    if($_POST[$name]==''){
        $data[$name]='NULL';
    }else{
        $data[$name]="'".utf8_decode($_POST[$name])."'";
    }
}

createPost('area');
createPost('caso');
createPost('localizador');
createPost('datec');
createPost('tipo');
$data['datec']="'".date('Y-m-d H:i:s',strtotime($_POST['datec'].':00'))."'";
createPost('asesor');

//Range Mailing
IF($_POST['area']==2){
	$tmp_cases=str_replace("Caso",'',$data['localizador']);
	$tmp_cases=str_replace("'",'',$tmp_cases);
	//$tmp_cases=str_replace("Case",'',$tmp_cases);
    $casos=explode(' ',$tmp_cases);
    $flag=1;
}
?>
<pre>
	<?php print_r($casos); ?>
</pre>

<?php
//Query
function runQuery($localizador, $tipo){
	global $data, $error;
	
	switch($tipo){
		case "mailing":
			$thisCaso=$localizador;
			break;
		case "reembolsos":
			$thisCaso=$localizador;
			break;
		default:
			$thisCaso=$data['caso'];
			break;
	}

	$thisLoc=$localizador;
	
	
	$query="INSERT INTO "
				."bo_tipificacion "
				."(area,asesor,fecha_recepcion,em,localizador,status,internal_id) VALUES ("
				.$data['area'].","
				.$data['asesor'].","
				.$data['datec'].","
				.$thisCaso.","
				.$thisLoc.","
				.$data['tipo'].","
				."insertBOID(".$thisLoc.",".$data['area']."))";
	mysql_query($query);
	
	if(mysql_errno()){
		$error[]=$thisLoc;
		echo "<br> Error $thisLoc: ".mysql_error();
		echo "<br> on $query<br>";
	}	
}

if($_POST['area']==2){
	foreach($casos as $index => $qcaso){
		$qcaso_tmp=str_replace(" " , "", $qcaso);
		if($qcaso_tmp==""){continue;}
		$qcaso_tmp="'$qcaso_tmp'";
		runQuery($qcaso_tmp,'mailing');
	}
}else{
	runQuery($data['localizador'],'else');
}

if(count($error)>0){
	foreach($error as $index => $caso_error){
		$msgerror.=" $caso_error ";
	}
	echo "status- ERROR -status msg- Error al Guardar Registro(s) $msgerror -msg";
}else{
    echo "status- OK -status msg- Registro(s) Exitoso(s) -msg";
}




?>