<?php

class Filters{
	
	public static function showFilter($form_action, $form_method, $butt_id, $butt_title, $tbody){
			
		echo "<div id='showFilter' style='background:#99bfe6; height: 50px; margin: 0;'><form action='$form_action' method='$form_method'>";
		echo "<table style='width:100px; margin: auto; text-align: center; font-weight: normal; color: white;'><tr>$tbody<th><button class='button button_red_w' id='$butt_id'>$butt_title</button></th></tr></table></form></div>";
		
	}
	
	public static function showFilterNOFORM($butt_id, $butt_title, $tbody){
			
		echo "<div id='showFilter' style='background:#99bfe6; height: 50px; margin: 0;'>";
		echo "<table style='width:100px; margin: auto; text-align: center; font-weight: normal; color: white;'><tr>$tbody<th><button class='button button_red_w' id='$butt_id'>$butt_title</button></th></tr></table></div>";
		
	}
	
}



?>