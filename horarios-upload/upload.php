<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];
if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}

$menu_programaciones="class='active'";
$credential="schedules_upload";

$mx_zone = new DateTimeZone('America/Mexico_City');

include("../connectDB.php");
$start=$_POST['start'];
$end=$_POST['end'];
$datestart=date('Y-m-d', strtotime($start));
$dateend=date('Y-m-d', strtotime($end));




//Upload File
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

echo "$result<br>";
	
//Read CSV
function printTable(){
	global $data, $regs;
	$fila = 1;
	if (($gestor = fopen("../uploads/tmp.csv", "r")) !== FALSE) {
		echo "\n<table class='t2' style='width:100%'>\n";
	    while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {
	    	$regs=$fila;
	    	$data[]=$datos;
	        if($fila % 2 == 0){$class="pair";}else{$class="odd";}
	        if($fila==1){$class="title";}
	        
	        $numero = count($datos);
	        echo "\t<tr class='$class'>\n";
	        
	        for ($c=0; $c < $numero; $c++) {
	        	//Validate columns
	        	if ($fila==1){
	        		
	        		switch($c){
	        			case 0:
	        				$validate="Comida Lunes";
	        				break;
                        case 1:
	        				$validate="Comida Martes";
	        				break;
                        case 2:
	        				$validate="Comida Miercoles";
	        				break;
                        case 3:
	        				$validate="Comida Jueves";
	        				break;
                        case 4:
	        				$validate="Comida Viernes";
	        				break;
	        			case 5:
	        				$validate="Comida Sabado";
	        				break;
	        			case 6:
	        				$validate="Comida Domingo";
	        				break;
	        			case 7:
	        				$validate="Lunes";
	        				break;
	        			case 8:
	        				$validate="Martes";
	        				break;
	        			case 9:
	        				$validate="Miercoles";
	        				break;
	        			case 10:
	        				$validate="Jueves";
	        				break;
	        			case 11:
	        				$validate="Viernes";
	        				break;
	        			case 12:
	        				$validate="Sabado";
	        				break;
	        			case 13:
	        				$validate="Domingo";
	        				break;
	        			case 14:
	        				$validate="Super";
	        				break;
	        			case 15:
	        				$validate="Usuario";
	        				break;
	        		}
	        	
	        		if($datos[$c]!=$validate){
	        			echo "ERROR! Las columnas no coinciden con el formato necesario";
	        			unlink("../uploads/tmp.csv");
	        			exit();
	        		}
	        		
	        	}
	            echo "\t\t<td>$datos[$c]</td>\n";
	        }
	        $fila++;
	        echo "\t</tr>\n";
	    }
	    	echo "<table>";
	    fclose($gestor);
	}else{
		echo "ERROR!"; exit;
	}
	unlink("../uploads/tmp.csv");
}
?>

<head>
<link rel="stylesheet" type="text/css"
          href="http://comeycome.com/pt/styles/tables1.css"/>
</head>

<?php include("../common/menu.php"); unset($result); ?>

<table class='t2' style="width:100%">
	<tr class='title'>
		<th colspan=2>Resultados de archivo subido</th>
	</tr>
	<tr class='subtitle'>
		<td><?php echo $result; ?></td>
		<td><?php echo "De ".date('d/m/Y', strtotime($start))." a ".date('d/m/Y', strtotime($end)); ?></td>
	</tr>
</table>

<br><br>

<?php printTable();

foreach($data as $id => $info){
	
	foreach($info as $id2 => $info2){
		switch($id2){
			case ($id2 >=7 && $id2 <= 13):
				if ($id2!=0){
					if($info2=="Descanso"){
						$js[$id][$id2]="00:00:00";
						$je[$id][$id2]="00:00:00";
						$cs[$id][$id2]="00:00:00";
						$ce[$id][$id2]="00:00:00";
						
					}else{
						
						$js[$id][$id2]=substr($info2,0,5).":00";
						$je[$id][$id2]=substr($info2,8).":00";
						switch($id2){
							case 7:
								if($data[$id][0]=="NA"){
									$cs[$id][$id2]="00:00:00";
									$ce[$id][$id2]="00:00:00";
									break;
								}
								$cs[$id][$id2]=substr($data[$id][0],0,5).":00";
								$ce[$id][$id2]=substr($data[$id][0],8).":00";
								break;
                            case 8:
								if($data[$id][1]=="NA"){
									$cs[$id][$id2]="00:00:00";
									$ce[$id][$id2]="00:00:00";
									break;
								}
								$cs[$id][$id2]=substr($data[$id][1],0,5).":00";
								$ce[$id][$id2]=substr($data[$id][1],8).":00";
								break;
                            case 9:
								if($data[$id][2]=="NA"){
									$cs[$id][$id2]="00:00:00";
									$ce[$id][$id2]="00:00:00";
									break;
								}
								$cs[$id][$id2]=substr($data[$id][2],0,5).":00";
								$ce[$id][$id2]=substr($data[$id][2],8).":00";
								break;
                            case 10:
								if($data[$id][3]=="NA"){
									$cs[$id][$id2]="00:00:00";
									$ce[$id][$id2]="00:00:00";
									break;
								}
								$cs[$id][$id2]=substr($data[$id][3],0,5).":00";
								$ce[$id][$id2]=substr($data[$id][3],8).":00";
								break;
                            case 11:
								if($data[$id][4]=="NA"){
									$cs[$id][$id2]="00:00:00";
									$ce[$id][$id2]="00:00:00";
									break;
								}
								$cs[$id][$id2]=substr($data[$id][4],0,5).":00";
								$ce[$id][$id2]=substr($data[$id][4],8).":00";
								break;

							case 12:
								if($data[$id][5]=="NA"){
									$cs[$id][$id2]="00:00:00";
									$ce[$id][$id2]="00:00:00";
									break;
								}
								$cs[$id][$id2]=substr($data[$id][5],0,5).":00";
								$ce[$id][$id2]=substr($data[$id][5],8).":00";
								break;
							case 13:
								if($data[$id][6]=="NA"){
									$cs[$id][$id2]="00:00:00";
									$ce[$id][$id2]="00:00:00";
									break;
								}
								$cs[$id][$id2]=substr($data[$id][6],0,5).":00";
								$ce[$id][$id2]=substr($data[$id][6],8).":00";
								break;
						}
						
					}
					
				}
				break;
			}
	}
}
?>
<br>
<table class='t2' style='width:100%'>
	<tr class='title'>
		<th>Asesor</th>
		<th>Id</th>
		<th>Fecha</th>
		<th>Jornada Start</th>
		<th>Jornada End</th>
		<th>Comida Start</th>
		<th>Comida End</th>
	</tr>


<?php
foreach($data as $key => $printinfo){
	if($key!=0){
		$i=1;
		
		//get userid
        if(substr($printinfo[15],0,6)=="bloque"){

            $userid[$key]="9999".substr($printinfo[15],6,4);

        }else{
    		$query="SELECT `id` FROM `Asesores` WHERE `Usuario`='$printinfo[15]'";
    		$result=mysql_query($query);
    		$userid[$key]=mysql_result($result,0,'id');
        }
		
		$x=0;
		while(date('Y-m-d', strtotime($start. "+ $x days"))!=date('Y-m-d', strtotime($end."+ 1 days"))){
				
			$d=intval(date('N', strtotime($start. "+ $x days")));
			//echo "diasem: $d // ";
			
			$tmpjs = new DateTime(date('Y-m-d', strtotime($start. "+ $x days"))." ".$js[$key][$d+6]." America/Bogota");
			$tmpjs -> setTimezone($mx_zone);
			$tjs = $tmpjs ->format('H:i:s');
			//echo date('Y-m-d', strtotime($start. "+ $x days"))." ".$js[$key][$d+6]." America/Bogota<br>".$js[$key][$d+6]."<br>$tjs<br><br>";
			
			$tmpje = new DateTime(date('Y-m-d', strtotime($start. "+ $x days"))." ".$je[$key][$d+6]." America/Bogota");
			$tmpje -> setTimezone($mx_zone);
			$tje = $tmpje->format('H:i:s');
			
			$tmpcs = new DateTime(date('Y-m-d', strtotime($start. "+ $x days"))." ".$cs[$key][$d+6]." America/Bogota");
			$tmpcs -> setTimezone($mx_zone);
			$tcs = $tmpcs->format('H:i:s');
			
			$tmpce = new DateTime(date('Y-m-d', strtotime($start. "+ $x days"))." ".$ce[$key][$d+6]." America/Bogota");
			$tmpce -> setTimezone($mx_zone);
			$tce = $tmpce->format('H:i:s');
			
			$query="SELECT `id` FROM `Historial Programacion` WHERE `Fecha`='".date('Y-m-d', strtotime($start. "+ $x days"))."' AND `asesor`='$userid[$key]'";
			$result=mysql_query($query);
			$rows=mysql_numrows($result);
			
			if($rows==0){
				$query="INSERT INTO `Historial Programacion` (`id`,`asesor`,`Fecha`,`jornada start`,`jornada end`,`comida start`,`comida end`) VALUES (NULL,'$userid[$key]','".date('Y-m-d', strtotime($start. "+ $x days"))."','$tjs','$tje','$tcs','$tce')";
			}else{
			
				$qid=mysql_result($result,0,'id');
				$query="UPDATE `Historial Programacion` SET `asesor`='$userid[$key]',`Fecha`='".date('Y-m-d', strtotime($start. "+ $x days"))."',`jornada start`='$tjs',`jornada end`='$tje',`comida start`='$tcs',`comida end`='$tce' WHERE `id`='$qid'";
			}
			mysql_query($query);
	        if(mysql_errno()){
					    echo "$key1 // $key_xfered MySQL error ".mysql_errno().": "
					         .mysql_error()."\n<br>When executing <br>\n$query\n<br><br>";
					}
			
		$x++;
		}

		while($i<=7){
			echo "\t\t<tr>\n
				\t\t\t<td>$printinfo[15]</td>\n
				\t\t\t<td>$userid[$key]</td>\n
				\t\t\t<td>$i</td>\n
				\t\t\t<td>".$tjs."</td>\n
				\t\t\t<td>".$tje."</td>\n
				\t\t\t<td>".$tcs."</td>\n
				\t\t\t<td>".$tce."</td>\n
				\t\t</tr>\n";
		$i++;
		}
	}
}
?>
</table>
<?php


NoDatesError:
if($error==1){echo "No dates selected or Invalid Dates";}

?>