<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="monitor_y_lw";
$menu_monitores="class='active'";

include("../connectDB.php");
include("../common/scripts.php");
date_default_timezone_set('America/Bogota');
?>
<?php
//header('Content-Type: text/html; charset=utf-8');
include("../common/menu.php");

if(isset($_POST['start'])){
	$inicio=date('Y-m-d', strtotime($_POST['start']));	
}else{
	$inicio=date('Y-m-d');	
}

if(isset($_POST['end'])){
	$fin=date('Y-m-d', strtotime($_POST['end']));	
}else{
	$fin=date('Y-m-d');	
}

if($_POST['copy']==1){
	if(isset($_POST['c_start'])){
		$c_inicio=date('Y-m-d', strtotime($_POST['c_start']));	
	}else{
		$c_inicio=date('Y-m-d', strtotime('-7 days'));	
	}
	
	if(isset($_POST['c_end'])){
		$c_fin=date('Y-m-d', strtotime($_POST['c_end']));	
	}else{
		$c_fin=date('Y-m-d', strtotime('-1 days'));	
	}
}

$skill=$_POST['skill'];

if(isset($_POST['submit']) || $_POST['copy']==1){
	
	//Data Asesores
	
	if($_POST['copy']==1){
		$q_inicio=$c_inicio;
	}else{
		$q_inicio=$inicio;
	}
	
	if($scratch==1){
		$db="prog_scratch";
		$db_id="a.asesor";
		$db_order="slot";
		$db_skill="AND skill='$skill'";
	}else{
		$db="`Historial Programacion`";
		$db_id="`Historial Programacion`";
		$db_order="Esquema, `jornada start`, Nombre";
		$db_skill="";
	}
	
	
}

//Parametros
function printHalfs($id,$ro="",$asesor="",$index){
	global $selector_horarios;
		
		if($ro==""){
			$slot="slot_horarios";
			$id_slot='id=sl_'.$index;
		}
		
		echo "<div class='line_horarios line_$id $slot' slot='$index' $id_slot>";
	
		if($ro!='readonly'){
			echo "<div set='$id' class='parametros'>"
				."<div class='param par_nombre' style='width: 194px;' id='pn_$id'><input id_asesor='".$asesor['id']."' type='text' value='".$asesor['N Corto']."' class='par_input' style='width: 194; height: 30px; border: 2px solid #ccc; border-radius: 5px;'></div>\n"
				."<div class='param par_esquema' style='width: 35px;' id='pe_$id'><input type='text' value='".$asesor['Esquema']."' class='par_input' style='width: 35; height: 30px; border: 2px solid #ccc; border-radius: 5px; text-align: center'></div>\n"
				."<div class='param par_jornada' style='width: 112px' id='pj_$id'>".selectHorario($asesor)."</div>\n"
				."<div class='param par_comida' id='pc_$id'>".selectOther($asesor,'comida')."</div>\n"
				."<div class='param par_x1' id='px1_$id'>".selectOther($asesor,'extra1')."</div>\n"
				."<div class='param par_x2' id='px2_$id'>".selectOther($asesor,'extra2')."</div>\n"
				."</div><div id='$id' style='display: inline-block'>";
		}else{
			echo "<div set='$id' class='parametros' style='text-align:right'>$id</div><div id='$id' style='display: inline-block' class='parent_$id'>";
		}
		
	
	
		if($ro!='readonly'){
			for($i=0;$i<48;$i++){
				if($i<10){
					$x=$i;
				}else{
					$x=$i;
				}
				echo "<input type='text' class='num_in h_$x' hora='$x'  readonly>";
			}
		}else{
			for($i=0;$i<48;$i++){
				if($i<10){
					$x=$i;
				}else{
					$x=$i;
				}
				echo "<input type='text' class='$id h_$x' hora='$x' $ro>";
			}
		}
	
		echo "</div>\n</div>\n\n";
}

?>
<link rel="stylesheet" href="/js/periodpicker/build/jquery.periodpicker.min.css">
<script src="/js/periodpicker/build/jquery.periodpicker.full.min.js"></script>

<script>

	$(function(){
		$('#inicio').periodpicker({
			end: '#fin',
			lang: 'en',
			norange: true,
			animation: true
		});
		
		
	});

</script>
<style>
	
	
</style>
<div id='flag' hidden>0</div>

<table class='t2' style='width:600px; margin:auto'><form action="index.php" id='form_consulta' method="post" enctype="multipart/form-data">
	<tr class='title'>
		<th colspan=10>Bitacora <?php if(isset($_POST['submit']) || $_POST['copy']==1){echo " ($depart $inicio a $fin)"; if($scratch==1){echo " (From Scratch)";}} ?></th>
	</tr>
	<tr class='title'>
		<td style='width:33%'>Fecha</td>
		
		<td rowspan=3 class='total'><input type="submit" value="Consultar" name="submit"></td>
	</tr>
	<tr class='pair'>
		<td><input type='text' name='start' id='inicio' value='<?php echo $inicio; ?>' required><input type='text' name='end' id='fin' value='<?php echo $fin; ?>' required></td>
		
	</tr>
	
</form></table>
<div style='text-align: right'>
<button class='button button_red_w' style='background: #f4e842; color: black; font-weight: bold' id='b_apply'>Load</button>
</div>

