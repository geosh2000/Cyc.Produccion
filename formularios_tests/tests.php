<?php

include_once("../modules/modules.php");
initSettings::start(true);

class test{
	private $asesor;
	private $skill;
	private $testNo;
	private $data;
	private $respuestas;
	
	public function __construct($testNo){
		
		$this->testNo=$testNo;
		
		if(isset($_GET['forceskill'])){
			$this->skill=$_GET['forceskill'];
		}else{
			$this->skill=$_SESSION['dep'];
		}
		
		$this->asesor=$_SESSION['asesor_id'];
		
		$querySEL="SELECT a.id, asesor, b.pregunta, b.id as preguntaId, c.respuesta FROM test_results a LEFT JOIN test_preguntas b ON a.pregunta=b.id LEFT JOIN test_respuestas c ON a.respuesta=c.id WHERE testno=".$this->testNo." AND asesor=".$this->asesor." ORDER BY id";
		if($result=Queries::query($querySEL)){
			if($result->num_rows>0){
				while($fila=$result_fetch_assoc()){
					$this->data[$fila['preguntaId']]['pregunta']=$fila['pregunta'];
					$this->data[$fila['preguntaId']]['respuesta']=$fila['respuesta'];
				}
			}else{
				$query="SELECT a.id as preguntaID, a.pregunta FROM test_preguntas WHERE skill=".$this->skill." AND testNo=".$this->testNo." ORDER BY RAND()";
				if($result=Queries::query($query)){
					while($fila=$result->fetch_assoc()){
						$insert[$fila['preguntaID']]=$fila['pregunta'];
					}
					$connectdb=Connection::mysqliDB('CC');
					foreach($insert as $pregunta => $info){
						$query="INSERT INTO test_results VALUES (NULL, ".$this->asesor.", $pregunta,".$this->testNo.", NULL, NULL, NULL)";
						$connectdb->query($query);
					}
				}
				
				if($result=Queries::query($querySEL)){
					while($fila=$result_fetch_assoc()){
						$this->data[$fila['preguntaId']]['pregunta']=$fila['pregunta'];
						$this->data[$fila['preguntaId']]['respuesta']=$fila['respuesta'];
					}
				}
				
				
			}
		}

		$queryPreg="SELECT a.id, pregunta, index, respuesta FROM test_respuestas a LEFT JOIN test_preguntas b ON a.pregunta=b.id WHERE testNo=".$this->testNo." ORDER BY RAND()";
		if($result=Queries::query($queryPreg)){
			while($fila=$result->fetch_assoc()){
				$this->respuestas[$fila['pregunta']][$fila['index']]['respuesta']=$fila['respuesta'];
				$this->respuestas[$fila['pregunta']][$fila['index']]['id']=$fila['id'];
			}
		}
	}

	public function printBlocks(){
		echo "<ul><li>";
		foreach($this->data as $pregunta => $info){
			echo "<ul>".$info['pregunta'];
				foreach($respuestas[$pregunta] as $index => $info2){
					if($info2['respuesta']==$info['respuesta']){ $color = "style='color: red'";}else{$color="";}
					echo "<li $color>".$info2['respuesta']."</li>";
				}
			echo "</ul>\n";
		}
		echo "</li></ul>";
	}
	
	
	
}

$test= new test(1);
$test->printBlocks();
?>