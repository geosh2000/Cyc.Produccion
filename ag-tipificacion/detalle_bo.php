<?php
include_once("../modules/modules.php");

initSettings::start(true,'asesor_cuartiles');
initSettings::printTitle('Reporte BO');

timeAndRegion::setRegion('Cun');

//GET Variables
$skill=6;
if(isset($_POST['from'])){$from=date('Y-m-d',strtotime($_POST['from']));}else{$from=date('Y-m-d',strtotime('-1 months'));}
if(isset($_POST['to'])){$to=date('Y-m-d',strtotime($_POST['to']));}else{$to=date('Y-m-d',strtotime('-1 days'));}

$tbody="<td><input type='text' id='from' name='from' value='$from' required/><input type='text' id='to' name='to' value='$to' required/><input type='hidden' name='consultar'></td>";

Filters::showFilter('','POST','consultar','Consultar',$tbody);


?>

<link rel="stylesheet" href="/js/periodpicker/build/jquery.timepicker.min.css">
<script src="/js/periodpicker/build/jquery.timepicker.min.js"></script>
<script>
$(function(){
    $('#from').periodpicker({
		end: '#to',
		lang: 'en',
		<?php
			if($cheat!=1){
				echo "minDate: '2016-07-11',";
			}
		?>
		animation: true
	});
});
</script>

<br>
<?php if(!isset($_POST['consultar'])){exit;} ?>
<style> .center{
	text-align: center
}

.f_recep{
	text-align: center
}
</style>

<script>
$(function(){

	flag=true;

	$( "#progressbar" ).progressbar({
      value: false
    });

	$('body').on('focus',".f_recep", function(){
		$(this).periodpicker({
			norange: true, // use only one value
			cells: [1, 1], // show only one month

			resizeButton: false, // deny resize picker
			fullsizeButton: false,
			fullsizeOnDblClick: false,

			timepicker: true, // use timepicker
			formatDateTime: 'YYYY-MM-DD HH:mm:ss',
			timepickerOptions: {
				hours: true,
				minutes: true,
				seconds: false,
				ampm: true
			},

			onOkButtonClick: function(){
				reg_id=this.startinput.closest('td').find('.f_recep').attr('reg');
				reg_newVal=this.startinput.val();
				editDate(reg_id,reg_newVal);
			}
		});
	});

	function editDate(id, newVal){
		$.ajax({
			url: 'update_detalle.php',
			type: 'POST',
			data: {newVal: newVal, id: id, updatedBy: <?php echo $_SESSION['asesor_id']; ?>},
			dataType: 'html',
			success: function(data){
					if(data=='Done'){
						new noty({
                            text: "id: "+ id + " Saved",
                            type: "success",
                            timeout: 3000,
                            animation: {
                                open: {height: 'toggle'}, // jQuery animate function property object
                                close: {height: 'toggle'}, // jQuery animate function property object
                                easing: 'swing', // easing
                                speed: 500 // opening & closing animation speed
                            }
                        });
					}else{
						new noty({
                            text: "id: "+ id + " ERROR -> "+ data,
                            type: "error",
                            timeout: 3000,
                            animation: {
                                open: {height: 'toggle'}, // jQuery animate function property object
                                close: {height: 'toggle'}, // jQuery animate function property object
                                easing: 'swing', // easing
                                speed: 500 // opening & closing animation speed
                            }
                        });
					}
				},
			error: function(){
        dialogLoad.dialog('close');
				alert("Error de conexion");
			}
		})
	}

	function printTable(){

    showLoader('Obteniendo Información');

    $.ajax({
      url: 'detalle_json.php',
      type: 'POST',
      data: {from: '<?php echo $from; ?>', to: '<?php echo $to; ?>'},
      dataType: 'json',
      success: function(array){
          data=array;

          dialogLoad.dialog('close');

          $('#result-table').tablesorter({
      		    theme: 'jui',
      		    headerTemplate: '{content} {icon}',
              data: data,
    			    widgets: ['zebra','columns','uitheme','filter', 'output' , 'stickyHeaders', 'editable'],
      		    tableClass: 'center',
      		    widgetOptions: {
      		    	//Filters
            			filter_childRows: false,
                      filter_columnFilters: true,
                      filter_cssFilter: "tablesorter-filter",
                      filter_functions: null,
                      filter_hideFilters: false,
                      filter_ignoreCase: true,
                      filter_reset: null,
                      filter_searchDelay: 300,
                      filter_startsWith: false,
                      filter_useParsedData: false,

                      //Outputs
                      output_separator     : ',',         // ',' 'json', 'array' or separator (e.g. ';')
                      output_ignoreColumns : [0],          // columns to ignore [0, 1,... ] (zero-based index)
                      output_hiddenColumns : false,       // include hidden columns in the output
                      output_includeFooter : true,        // include footer rows in the output
                      output_dataAttrib    : 'data-name', // data-attribute containing alternate cell text
                      output_headerRows    : true,        // output all header rows (multiple rows)
                      output_delivery      : 'd',         // (p)opup, (d)ownload
                      output_saveRows      : 'a',         // (a)ll, (v)isible, (f)iltered, jQuery filter selector (string only) or filter function
                      output_duplicateSpans: true,        // duplicate output data in tbody colspan/rowspan
                      output_replaceQuote  : '\u201c;',   // change quote to left double quote
                      output_includeHTML   : true,        // output includes all cell HTML (except the header cells)
                      output_trimSpaces    : false,       // remove extra white-space characters from beginning & end
                      output_wrapQuotes    : false,       // wrap every cell output in quotes
                      output_popupStyle    : 'width=580,height=310',
                      output_saveFileName  : 'Detalle_<?php echo $from."a".$to;?>.csv',
                      // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
                      output_encoding      : 'data:application/octet-stream;charset=utf8,',

                      //Sticky
                      stickyHeaders_attachTo : '#container-cuartiles'
      		    }
      	  	});
        },
      error: function(){
        showNoty('error', 'Error de conexión', 3000);
      }
    })

	}

	printTable();

	$('#export').click(function(){
		$('.tablesorter').trigger('outputTable');
	})

	$('.f_recep').change(function(){
		fila = $(this).closest('tr').find('td');
		alert(fila);
	})

});
</script>

<div id='buttons' style='width: 90%; margin: auto; overflow: auto;'>
	<button id='export' class='button button_red_w'>Exportar</button>
</div>
<div style='width: 90%; margin: auto; overflow: auto;'>
<div id='result-table'></div>
