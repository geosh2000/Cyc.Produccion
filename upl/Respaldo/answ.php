<?php
include("../connectDB.php");
$start=$_POST['start'];
$end=$_POST['end'];
$datestart=strtotime($start);
$dateend=strtotime($end);
$err_count=0;



$tipo=$_POST['tipo'];
switch($tipo){
	case "ans":
		$db="t_Answered_Calls";
		$astid=13;
		$answ=1;
        $answered=1;
        $titles= array(
            "Date",
            "Caller",
            "Queue",
            "IVR",
            "Wait",
            "Duration",
            "Pos.",
            "Disconnection",
            "Handled by",
            "Attempts",
            "Code",
            "Stints",
            "Srv",
            "Asterisk UID",
            "MOH events",
            "MOH duration",
            "IVR duration",
            "IVR path",
            "DNIS",
            "Tag",
            "Feat",
            "Vars"
        );
		break;
	case "abn":
		$db="t_Answered_Calls";
		$astid=14;
		$answ=0;
        $answered=0;
        $titles= array(
            "Date",
            "Handled by",
            "Caller",
            "Queue",
            "Disconnection",
            "Position",
            "IVR",
            "Wait",
            "Pos.",
            "Attempts",
            "Code",
            "Key",
            "Stints",
            "Srv",
            "Asterisk UID",
            "IVR duration",
            "IVR path",
            "DNIS",
            "Tag",
            "Feat",
            "Vars"
        );
		break;
}

//Upload CSV File
	$target_dir = "../uploads/";
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$uploadOK = 1;
	$FileType = pathinfo($target_file,PATHINFO_EXTENSION);
	$filename = $target_dir . "tmp." . $FileType;

	if($FileType!='csv'){
		$uploadOK=0;

	}

	if($uploadOK==0){
			$result= "Ivalid File! // Ext: $FileType";
		}else{
			if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $filename)) {
		        $result= "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
		    } else {
		        $result= "Sorry, there was an error uploading your file.";
	    	}


	}


//functions
function addTime($timeB, $timeA) {

    $timeAinSeconds = intval(date('H', strtotime($timeA)))*60*60 + intval(date('i', strtotime($timeA)))*60 + intval(date('s', strtotime($timeA)));
    $timeBinSeconds = intval(date('H', strtotime($timeB)))*60*60 + intval(date('i', strtotime($timeB)))*60 + intval(date('s', strtotime($timeB)));



    $timeABinSeconds = $timeAinSeconds + $timeBinSeconds;

    $timeABsec = $timeABinSeconds % 60;
    $timeABmin = (($timeABinSeconds - $timeABsec) / 60) % 60;
    $timeABh = ($timeABinSeconds - $timeABsec - $timeABmin*60) / 60 / 60;

    return str_pad((int) $timeABh,2,"0",STR_PAD_LEFT).":"
          .str_pad((int) $timeABmin,2,"0",STR_PAD_LEFT).":"
          .str_pad((int) $timeABsec,2,"0",STR_PAD_LEFT);
}

//Read CSV
if (($gestor = fopen("../uploads/tmp.csv", "r")) !== FALSE) {
    $fila=1;
    $i=0;
    while (($datos = fgetcsv($gestor, 1000, ";")) !== FALSE) {
        if($fila==1){
            foreach($datos as $key => $title){
                $data_title[$key]=$title;
            }
            unset($key,$title);
        }else{
            foreach($datos as $key => $info){
                $data[$i][$titles[$key]]=$info;
            }
            unset($key,$info);
            $i++;
        }
        $data[$i]['Answered']=$answered;
        $fila++;
    }

    switch ($tipo){
        case "ans":
            if(count($data_title)-2!=count($titles)){echo "El archivo seleccionado no coincide con el formato para llamadas contestadas"; exit;}
            break;
        case "abn":
            if(count($data_title)-2!=count($titles)){echo "El archivo seleccionado no coincide con el formato para llamadas no contestadas"; exit;}
            break;
    }

    fclose($gestor);
    unlink("../uploads/tmp.csv");

}

?>

<?php

    include("../common/scripts.php");
    include("../common/menu.php");

?>

<table class='t2' style="width:100%">
	<tr class='title'>
		<th colspan=2>Resultados de archivo subido</th>
	</tr>

</table>

<?php

ob_start();
echo "Start ".count($data)." Regs...<br>";

//Upload to DB
foreach($data as $key => $printinfo){
    if($printinfo['Caller']!='*'){
    	$i=1;
        //get userid
        $query="SELECT `id` FROM `Asesores` WHERE `N Corto`='".substr($printinfo['Handled by'],0,-6)."'";
        $result=mysql_query($query);
        if(mysql_numrows($result)!=0){
            $userid=mysql_result($result,0,'id');
        }else{
            $userid=$printinfo['Handled by'];
        }
        //echo "$query<br>info: ".$printinfo['Handled by']."<br>Search: ".substr($printinfo['Handled by'],0,-6)."<br>Result: $userid<br>";

        //Format Dates
        $hora_inicio=date('H:i:s', strtotime(substr($printinfo['Date'],8,8)));
    	$fecha_inicio='2016-'.substr($printinfo['Date'],0,2).'-'.substr($printinfo['Date'],3,2);

        //Date End Call
        if($printinfo['IVR duration']==NULL || $printinfo['IVR duration']==""){$data[$key]['IVR duration']="00:00:00";}
        if($tipo=="ans"){
            $hora_fin=addtime($hora_inicio,addtime($data[$key]['Wait'],addtime($data[$key]['Duration'],$data[$key]['IVR'])));
            $dr=$data[$key]['Duration'];
        }else{
            $hora_fin=addtime($hora_inicio,addtime($data[$key]['IVR'],$data[$key]['Wait']));
            $dr="";
        }
		
		//Format INT Vals
		if($printinfo['MOH Events']== NULL){
			$data[$key]['MOH Events']=0;
		}
		

        //Format Caller  and DNIS
        $caller=str_replace("'","",$printinfo['Caller']);
        $dnis=str_replace("'","",$printinfo['DNIS']);

        //Check and Upload
        $query_check="SELECT `ac_id` FROM `$db` WHERE AsteriskID=".$data[$key]['Asterisk UID']." AND Fecha='$fecha_inicio'";
        $result_check=mysql_query($query_check);
        $rows_check=mysql_numrows($result_check);

        if($rows_check==0){
            $query="INSERT INTO `$db` VALUES (NULL,'$fecha_inicio','$hora_inicio','$caller','"
                .$printinfo['Queue']."','".$printinfo['IVR']."','".$printinfo['Wait']."','".$printinfo['Duration']."','"
                .$printinfo['Pos.']."','".$printinfo['Disconnection']."','$userid','"
                .$printinfo['Attempts']."','".$printinfo['Code']."','".$printinfo['Stints']."','".$printinfo['Srv']."','"
                .$printinfo['Asterisk UID']."','".$printinfo['MOH events']."','".$printinfo['MOH duration']."','"
                .$printinfo['IVR  duration']."','".$printinfo['IVR path']."','$dnis','','','$hora_fin','"
                .$printinfo['Position']."',NULL,$answered,'$dr',NULL)";
        }else{
            $qid=mysql_result($result_check,0,'ac_id');
            $query="UPDATE `$db` SET"
                    ." Fecha='$fecha_inicio',"
                    ." Hora='$hora_inicio',"
                    ." Llamante='$caller',"
                    ." Cola='".$printinfo['Queue']."',"
                    ." IVR='".$printinfo['IVR']."',"
                    ." Espera='".$printinfo['Wait']."',"
                    ." Duracion='".$printinfo['Duration']."',"
                    ." Pos='".$printinfo['Pos.']."',"
                    ." Desconexion='".$printinfo['Disconnection']."',"
                    ." asesor='$userid',"
                    ." Intentos='".$printinfo['Attempts']."',"
                    ." Codigo='".$printinfo['Code']."',"
                    ." Stints='".$printinfo['Stints']."',"
                    ." Srv='".$printinfo['Srv']."',"
                    ." AsteriskID='".$printinfo['Asterisk UID']."',"
                    ." MOH__events='".$printinfo['MOH events']."',"
                    ." MOH_duration='".$printinfo['MOH duration']."',"
                    ." IVR_duration='".$printinfo['IVR  duration']."',"
                    ." IVR_path='".$printinfo['IVR path']."',"
                    ." DNIS='$dnis',"
                    ." Comodin='',"
                    ." Comodin2='',"
                    ." Hora_fin='$hora_fin',"
                    ." Pos_salida='".$printinfo['Position']."',"
                    ." Clave=NULL,"
                    ." Answered=$answered,"
                    ." Duracion_Real='$dr' "
                    ."WHERE `ac_id`='$qid'";
        }

        mysql_query($query);

        echo "$key:<br>$query<br>";
        if(mysql_errno()){
           echo "$key MySQL error ".mysql_errno().": "
                .mysql_error()."\n<br>When executing <br>\n$query\n<br><br>";
            $err_count++;
        }else{
            echo "OK<br><br>";
        }
       
    }else{
        echo "Llamante = *<br><br>";
    }


ob_flush();
}

echo "Done!...<br>$err_count errors.";
ob_end_flush();


?>

