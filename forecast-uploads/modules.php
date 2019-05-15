<?php
class forecastModule{
	
	private $upload;
	private $tipo;
	private $credential;
	
	public function __construct($set){
			
		$this->upload=$_SERVER['REQUEST_URI']."&set=$set";
		$this->tipo=$set;
		
		switch($set){
			case 'upload_part':
			case 'vol_edit':
				$this->credential="schedules_upload";
				break;
			
		}
		
	}
	
	public function start(){
		
		initSettings::start(true,$this->credential);
		
		echo "<script>
				$(function() {
				    $('#inicio').periodpicker({
						end: '#fin',
						lang: 'en',
						animation: true
					});
				});
				</script>";	
				
			
		switch($this->tipo){
			case 'upload_part':
				echo "<br>";
				$this->printUploadPart();
				break;
			case 'vol_edit':
				$this->printVolEdit();
				break;
		}
		
		
	}
	
	public function printVolEdit(){
		
		include_once("mod_voledit.php");
		
		$voledit = new volEdit(isset($_POST['start']));
		
		$voledit->printBlocks($this->upload);
		
	}
	
	public function printUploadPart(){
		
		if(!isset($_POST['skill'])){
			echo "<table class='t2' style='width:600px; margin:auto'><form action='".$this->upload."' method='post' enctype='multipart/form-data'>
					<tr class='title'>
						<th colspan=10>Subir Participacion Forecast</th>
					</tr>
					<tr class='title'>
						<td style='width:33%'>Periodo</td>
						<td style='width:33%'>Programa</td>
						<td style='width:33%'>Archivo</td>
					</tr>
					<tr class='pair'>
						<td><input type='text' name='start' id='inicio' required><input type='text' name='end' id='fin' required></td>
						<td class='pair'><select name='skill' required><option value=''>Selecciona...</option>";
						
						$query="SELECT * FROM PCRCs WHERE forecast=1 ORDER BY Departamento";
			            if($result=Queries::query($query)){
			              while($fila=$result->fetch_assoc()){
			                echo "<option value='".$fila['id']."'>".$fila['Departamento']."</option>";
			              }
			            }
				     
					echo"</select></td>
						<td><input type='file' name='fileToUpload' id='fileToUpload'></td>
					</tr>
					<tr class='total'>
						<td colspan=10><input type='submit' value='Upload' name='submit'></td>
					</tr>
					<tr class='pair'><td colspan=100><a href='".MODULE_PATH."forecast_upload_format.csv' download>Descargar template para Upload</a><br>
														<a href='".MODULE_PATH."analisis_participacion.xlsx' download>Descargar XLS para analisis</a>
					</td></tr>
				</form></table>";
		}else{
			$start=$_POST['start'];
			$end=$_POST['end'];
			$datestart=date('Y-m-d', strtotime($start));
			$dateend=date('Y-m-d', strtotime($end));
			$skill=$_POST['skill'];
			$errores=0;
			
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
				
			//Read CSV
			$fila = 1;
			
			if (($gestor = fopen("../uploads/tmp.csv", "r")) !== FALSE) {
				while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {
			    	$numero = count($datos);
					
					if ($fila==1){
			        	for ($c=0; $c < $numero; $c++) {
			        		switch($c){
				        		case 0:
				        			$validate="Lunes";
				        			break;
				                   case 1:
				        			$validate="Martes";
				        			break;
				                   case 2:
				        			$validate="Miercoles";
				        			break;
				                   case 3:
				        			$validate="Jueves";
				        			break;
				                   case 4:
				        			$validate="Viernes";
				        			break;
				        		case 5:
				        			$validate="Sabado";
				        			break;
				        		case 6:
				        			$validate="Domingo";
				        			break;
				        	}
				        	
				        	if($datos[$c]!=$validate){
				        		echo "ERROR! Las columnas no coinciden con el formato necesario";
				        		unlink("../uploads/tmp.csv");
				        		exit();
				        	}
				        		
				        }
						$fila++;
			        }else{
			        	$data[]=$datos;
			        }
					
			    }
			    fclose($gestor);
			}
			unlink("../uploads/tmp.csv");
			
			echo "<br><table class='t2' style='margin:auto'>";
			
			$connectdb=Connection::mysqliDB('CC');
			foreach($data as $hora => $info){
				foreach($hora as $dow => $info2){
					
				}
			}
			
			for($x=0;$x<48;$x++){
				echo "<tr>\n\t";
				if($x==0){
					for($i=$datestart;date('Y-m-d',strtotime($i))<=date('Y-m-d',strtotime($dateend));$i=date('Y-m-d',strtotime($i.' +1 days'))){
						echo "<th>$i</th>\n\t";
					}
					echo "</tr>\n\t<tr>"; 
				}
				
				for($i=$datestart;date('Y-m-d',strtotime($i))<=date('Y-m-d',strtotime($dateend));$i=date('Y-m-d',strtotime($i.' +1 days'))){
					echo "<td>".$data[$x][date('w',strtotime($i))]."</td>\n\t";
					$query="INSERT INTO forecast_participacion VALUES ('$i','$skill','$x','".$data[$x][date('w',strtotime($i))]."')";
					if(!$result=$connectdb->query($query)){
		        		$query="UPDATE forecast_participacion SET participacion='".$data[$x][(date('N',strtotime($i))-1)]."' WHERE Fecha='$i' AND skill='$skill' AND hora='$x'";
							if(!$result=$connectdb->query($query)){	
			          			$errores++;
							}
					}
				}
				echo "</tr>\n\t";
					
			}
			$connectdb->close();
			
			echo "</table><br>Errores: $errores";
		}
	}
	
}

?>

