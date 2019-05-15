<?php

class Scripts{

	//Load Scripts
	public static function loadScripts($group,$mobile=false){

		if($mobile){
			$genericcss="<link rel='stylesheet' type='text/css' href='/styles/tables1.css'/>";
		}else{
			$genericcss="<link rel='stylesheet' type='text/css' href='/styles/common.css'/>";
		}

		switch($group){
			case 'generic':
				echo "<link href='/js/jquery/jquery-ui.min.css' rel='stylesheet'>"
						.$genericcss."<link rel='stylesheet' type='text/css' href='/styles/forms.css'/>"
						."<link rel='stylesheet' type='text/css' href='/styles/greentables.css'/>"
						."<link rel='stylesheet' type='text/css' href='/styles/picker.css'/>"
						."<link rel='stylesheet' type='text/css' href='/styles/express-table-style.css'/>"
						."<link rel='stylesheet' type='text/css' href='/js/tablesorter/css/theme.blue.css'/>"
						."<link rel='stylesheet' type='text/css' href='/js/tablesorter/css/theme.jui.css'/>"
						."<link rel='stylesheet' type='text/css' href='/styles/animate.css'/>"
						."<link rel='stylesheet' type='text/css' href='/styles/calendar.css' />"
						."<link rel='stylesheet' href='/js/jquerycustom/jquery-ui.css'>"

						."<script src='/js/jquery.tools.min.js'></script>"
						."<script src='/js/jquery/jquery-1.10.2.js'></script>"
						."<script src='/js/jquery/jquery-ui.js'></script>"
						."<script type='text/javascript' src='/js/pnotify.custom.min.js'></script>"
						// ."<script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script>"
						."<script type='text/javascript' src='/js/jquery.PopUpWindow.js'></script>"
						."<script type='text/javascript' src='/js/noty-2.3.8/js/noty/packaged/jquery.noty.packaged.min.js'></script>"
						."<script type='text/javascript' src='/js/jquery.filtertable.js'></script>";

				echo "<script type='text/javascript' src='/js/tablesorter/js/jquery.tablesorter.js'></script>"
						."<script type='text/javascript' src='/js/tablesorter/js/jquery.tablesorter.widgets.js'></script>"
						."<script type='text/javascript' src='/js/tablesorter/js/widgets/widget-scroller.js'></script>"
						."<script type='text/javascript' src='/js/tablesorter/js/widgets/widget-output.js'></script>"
						."<script type='text/javascript' src='/js/tablesorter/js/widgets/widget-build-table.js'></script>"
						."<script type='text/javascript' src='/js/tablesorter/js/widgets/widget-editable.js'></script>";

				echo "<link rel='stylesheet' href='/js/periodpicker/build/jquery.periodpicker.min.css'>"
						."<script src='/js/periodpicker/build/jquery.periodpicker.full.min.js'></script>";
				break;
		}
	}

	public static function periodScript($inicio,$fin, $moreoptions=NULL){
		echo "<script>
				$(function() {
				    $('#$inicio').periodpicker({
						end: '#$fin',
						lang: 'en',
						animation: true";

		if($moreoptions!=NULL){
			echo ",$moreoptions";
		}

		echo "});
				});
				</script>";
	}

}

?>
