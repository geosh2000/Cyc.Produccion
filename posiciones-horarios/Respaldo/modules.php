<?php
class posicionHModule{

	private $skill;
	private $inicio;
	private $fin;
	private $depart;
	private $data;

	public function __construct(){
		$this->skill=$_POST['skill'];
		$this->inicio=$_POST['inicio'];
		$this->fin=$_POST['fin'];

		echo "<script src='".MODULE_PATH."posiciones.js'></script>";


	}

	public function printFilter(){
		$tbody="<th>Periodo</th><th><input type='text' name='inicio' id='inicio' value='".$this->inicio."' required><input type='text' name='fin' id='fin' value='".$this->fin."' required></th>";
		$tbody.="<th>Programa</th><th><select name='skill' required><option value=''>Selecciona...</option>";
		$query="SELECT * FROM PCRCs WHERE forecast=1 ORDER BY Departamento";
		    if($result=Queries::query($query)){
		      while($fila=$result->fetch_assoc()){
		        $tbody.= "<option value='".$fila['id']."' ";
		        if($fila['id']==$this->skill){ $tbody.= "selected"; $this->depart=$fila['Departamento'];}
		        $tbody.= ">".$fila['Departamento']."</option>";
		      }
		    }
		$tbody.="</th>";

		Filters::showFilter($_SERVER['REQUES_URI'], 'POST', 'ver', 'Ver', $tbody);
	}


	public function printBlocks(){
		if($this->inicio!=NULL){

			$query="SELECT a.id, Fecha, Nombre, posicion FROM
(SELECT Fecha, b.id, Nombre, getDepartamento(b.id, Fecha) as dep, getPuesto(b.id, Fecha) as puestoOK FROM Fechas a  JOIN Asesores b WHERE Fecha BETWEEN '".$this->inicio."' AND '".$this->fin."' AND Egreso>'".$this->fin."' HAVING dep=".$this->skill." AND puestoOK=1) a LEFT JOIN horarios_position_select c ON a.id=c.asesor AND YEAR(Fecha)=c.year AND WEEK(Fecha,1)=c.semana ORDER BY Nombre";
			if($result=Queries::query($query)){
				while($fila=$result->fetch_assoc()){
					$this->data[$fila['id']][$fila['Fecha']]['posicion']=$fila['posicion'];
					$this->data[$fila['id']][$fila['Fecha']]['asesor']=utf8_encode($fila['Nombre']);

				}
			}

			echo "<br><table class='t2' style='width:600px; margin:auto'>
					<tr class='title'>
						<th colspan=10>Editor de Posiciones ('".$this->depart." -> ".$this->inicio." a ".$this->fin."') </th>
					</tr>
				</table>
				<br>
				<br>
				<table id='tableedit' style='margin: auto; width: 600; text-align: center'>
					<thead>
						<tr>
							<th>Fechas</th>
							<th>Semana</th>
							<th>Asesor</th>
							<th>Posicion</th>
						</tr>
					</thead>
					<tbody>";

				$Week=0;
				for($i=date('Y-m-d',strtotime($this->inicio));date('Y-m-d',strtotime($i))<=date('Y-m-d',strtotime($this->fin));$i=date('Y-m-d',strtotime($i.' +1 days'))){
						if(date('W',strtotime($i))==1 && date('w',strtotime($i))!=0){
							$Weektmp=date('W',strtotime(date('Y',strtotime($i.' -1 year')).'-12-31'));
							$Yeartmp=date('Y',strtotime($i.' -1 year'));
						}else{
							$Weektmp=date('W',strtotime($i));
							$Yeartmp=date('Y',strtotime($i));
						}

						$Weektmp=date('W',strtotime($i));
						$Yeartmp=date('Y',strtotime($i));

						if($Weektmp!=$Week){
							$Week=date('W',strtotime($i));
							foreach($this->data as $asesor => $info){
								$fechas=date('d/m/Y',strtotime(date('Y-m-d',strtotime($i).' -'.date('w',strtotime($i).' days'))))." a ".date('d/m/Y',strtotime(date('d-m-Y',strtotime(date('Y-m-d',strtotime($i).' -'.date('w',strtotime($i).' days')))).' +6 days'));
								echo "<tr>\n\t\t";
								echo "<td>$fechas</td>\n\t\t";
								echo "<td>$Weektmp</td>\n\t\t";
								echo "<td>".$info[$i]['asesor']."</td>\n\t";
								echo "<td semana='$Weektmp' asesor='".$asesor."' year='$Yeartmp'>".$info[$i]['posicion']."</td>\n\t";
								echo "</tr>\n\t";
							}
						}

				}
				echo "</tbody></table>";

		}
	}

}

?>
