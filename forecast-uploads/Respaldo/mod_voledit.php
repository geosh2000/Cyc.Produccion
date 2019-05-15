<?php
class volEdit{
	
	private $flag;
	private $inicio;
	private $fin;
	private $skill;
	private $data;
	private $depart;
	
	public function __construct($flag){
		
		$this->flag=$flag;
		
		if($this->flag){
			$this->inicio=date('Y-m-d', strtotime($_POST['start']));
			$this->fin=date('Y-m-d', strtotime($_POST['end']));
			$this->skill=$_POST['skill'];
			
			$query="SELECT Fecha, skill, factor, volumen, AHT, Reductores FROM forecast_volume WHERE skill=".$this->skill." AND Fecha BETWEEN '".$this->inicio."' AND '".$this->fin."' ORDER BY Fecha";
			if($result=Queries::query($query)){
				while($fila=$result->fetch_assoc()){
					$this->data[$fila['Fecha']]['factor']=$fila['factor'];
					$this->data[$fila['Fecha']]['volumen']=$fila['volumen'];
					$this->data[$fila['Fecha']]['AHT']=$fila['AHT'];
					$this->data[$fila['Fecha']]['Reductores']=$fila['Reductores'];
				}	
			}
			
			$query="SELECT Departamento FROM PCRCs WHERE id=".$this->skill;
			if($result=Queries::query($query)){
				$fila=$result->fetch_assoc();
				$depart=$fila['Departamento'];
			}
		}
		
	}
	
	public function printBlocks($upload){
		
		if($this->flag){
			echo "<link rel='stylesheet' href='/js/periodpicker/build/jquery.periodpicker.min.css'>
						<link rel='stylesheet' href='/styles/editable_table.css'>
						<script type='text/javascript' src='/js/tablesorter/js/widgets/widget-editable.js'></script>
						<script src='/js/periodpicker/build/jquery.periodpicker.full.min.js'></script>
						<script>skill = ".$this->skill."; </script> 
						<script src='".MODULE_PATH."voledit.js'></script>";
		}
		
		$tbody="<th>Periodo</th><th><input type='text' name='start' id='inicio' value='".$this->inicio."' required><input type='text' name='end' id='fin' value='".$this->fin."' required></th>";
		$tbody.="<th>Programa</th><th><select name='skill' required><option value=''>Selecciona...</option>";
		$query="SELECT * FROM PCRCs WHERE forecast=1 ORDER BY Departamento";
            if($result=Queries::query($query)){
              while($fila=$result->fetch_assoc()){
                $tbody.= "<option value='".$fila['id']."' ";
                if($fila['id']==$this->skill){ $tbody.= "selected";}
                $tbody.= ">".$fila['Departamento']."</option>";
              }
            }
        $tbody.="</th>";
        
		Filters::showFilter($upload, 'POST', 'consultar', 'Ver', $tbody);
		
		if($this->flag){
			
			echo "<br><table class='t2' style='width:600px; margin:auto'>
					<tr class='title'>
						<th colspan=10>Editor de Volumenes ('".$this->depart." ".$this->inicio." a ".$this->fin."') </th>
					</tr>
				</table>
				<br>
				<br>
				<table id='tableedit' style='margin: auto; width: 600; text-align: center'>
					<thead>
						<tr>
							<th>Fecha</th>
							<th>Factor</th>
							<th>Volumen</th>
							<th>AHT</th>
							<th>Reductores</th>
						</tr>
					</thead>
					<tbody>";
				for($i=date('Y-m-d',strtotime($this->inicio));date('Y-m-d',strtotime($i))<=date('Y-m-d',strtotime($this->fin));$i=date('Y-m-d',strtotime($i.' +1 days'))){
						echo "<tr>\n\t\t";
						echo "<td>$i</td>\n\t\t";
						echo "<td fecha='$i' col='factor'>".$this->data[$i]['factor']."</td>\n\t\t";
						echo "<td fecha='$i' col='volumen'>".$this->data[$i]['volumen']."</td>\n\t";
						echo "<td fecha='$i' col='AHT'>".$this->data[$i]['AHT']."</td>\n\t";
						echo "<td fecha='$i' col='Reductores'>".$this->data[$i]['Reductores']."</td>\n\t";
						echo "</tr>\n\t";
				}
				echo "</tbody></table>";
		}
	}
}

?>
