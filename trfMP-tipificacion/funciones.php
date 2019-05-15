<?php
header('Content-Type: text/html; charset=utf-8');

session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="asesor_formulario_mp";

date_default_timezone_set('America/Bogota');

include("../connectDB.php");
include("../common/scripts.php");
include("../common/menu.php");


$asesor=$_SESSION['asesor_id'];
$area="funciones";

$query="SELECT * FROM trfMP_opts WHERE activo=1 ORDER BY nivel, opcion";
$result=mysql_query($query);
$num=mysql_numrows($result);
for($i=0;$i<$num;$i++){
	$opciones[mysql_result($result, $i, 'nivel')][mysql_result($result, $i, 'actividad')]['id']=mysql_result($result, $i, 'id');
	$opciones[mysql_result($result, $i, 'nivel')][mysql_result($result, $i, 'actividad')]['titulo']=mysql_result($result, $i, 'titulo');
	$opciones[mysql_result($result, $i, 'nivel')][mysql_result($result, $i, 'actividad')]['opcion']=mysql_result($result, $i, 'opcion');
	$opciones[mysql_result($result, $i, 'nivel')][mysql_result($result, $i, 'actividad')]['trigger']=mysql_result($result, $i, 'trigger');
	$opciones[mysql_result($result, $i, 'nivel')][mysql_result($result, $i, 'actividad')]['tipo']=mysql_result($result, $i, 'tipo');
	
	$opts[mysql_result($result, $i, 'nivel')][mysql_result($result, $i, 'actividad')][mysql_result($result, $i, 'id')]['id']=utf8_encode(mysql_result($result, $i, 'id'));
	$opts[mysql_result($result, $i, 'nivel')][mysql_result($result, $i, 'actividad')][mysql_result($result, $i, 'id')]['opcion']=utf8_encode(mysql_result($result, $i, 'opcion'));
	$opts[mysql_result($result, $i, 'nivel')][mysql_result($result, $i, 'actividad')][mysql_result($result, $i, 'id')]['trigger']=utf8_encode(mysql_result($result, $i, 'trigger'));
	$opts[mysql_result($result, $i, 'nivel')][mysql_result($result, $i, 'actividad')][mysql_result($result, $i, 'id')]['parent']=utf8_encode(mysql_result($result, $i, 'parent'));
	$opts[mysql_result($result, $i, 'nivel')][mysql_result($result, $i, 'actividad')][mysql_result($result, $i, 'id')]['tipo']=utf8_encode(mysql_result($result, $i, 'tipo'));
	$opts[mysql_result($result, $i, 'nivel')][mysql_result($result, $i, 'actividad')][mysql_result($result, $i, 'id')]['actividad']=utf8_encode(mysql_result($result, $i, 'actividad'));
	$opts[mysql_result($result, $i, 'nivel')][mysql_result($result, $i, 'actividad')][mysql_result($result, $i, 'id')]['titulo']=utf8_encode(mysql_result($result, $i, 'titulo'));
}



?>
<link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.4/jquery.datetimepicker.css"/>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.4/build/jquery.datetimepicker.full.min.js'></script>

<script>
//Declare options
	var opciones = [], 
		asesor=<?php echo $asesor; ?>,
		em, loc, pnr;
	<?php
		foreach($opciones as $nivel => $info){
			echo "opciones[$nivel]=[]; ";
			foreach($info as $activity => $data){
				echo "opciones[$nivel][$activity]=[]; ";
				foreach($data as $title => $data2){
					echo "opciones[$nivel][$activity]['$title']='".utf8_encode($data2)."';\n";
				}
			}
		}
	?>
	
	//fechas
	
$(function(){
	$('#datein').datetimepicker({
	  value: '<?php echo date('Y-m-d H:i'); ?>',
	  step: 1,
	  format:'Y-m-d H:i',
	  inline:true,
	  maxDate: '<?php echo date('Y.m.d H:i'); ?>',
	  lang:'es'
	});
});
	

    
</script>
<script src='js_func.js'></script>

<style>
.formulario{
    width: 800px;
    height: 100%;
    margin: auto;
    overflow: auto;
}

.titulo{
    width: 800px;
    height: 65px;
    font-size: 24px;
    font-weight: bold;
    text-align: center;
    margin: auto;
    margin-top: -19px;
    border-radius: 15px;
    background: #008CBA
}

.campo{
    width: 520px;
    height: 80px;
    margin: auto;
    margin-top: 20px;
    border-radius: 15px;
}

.campo .name{
    float: left;
    height: 100%;
    width: 40%;
    background: #008CBA;
    border-radius: 15px 0 0 15px;
    color: white;
    font-size: 20px;
    font-weight: bold;
    text-align: center;
}

.campo .name p{
    padding-top:12px;
}

.campo .opcion{
    float: left;
    height: 100%;
    width: 60%;
    background: #E7F5FE;
    border-radius: 0 15px 15px 0;
    color: black;
    font-size: 20px;
    text-align: center;
}

.campo .opcion .seleccion{
    padding-top:5px;
}

.seleccion select, .seleccion input{
    width: 200px;
}

.error{
    background: #FFE8E0;
    color: black;
}



</style>
<?php


?>
<div style='float: left; width:60%; margin: auto;'>
<div class='formulario'>
    <div class='titulo'>
        <p style='padding-top: 13px; color: white;'>Tráfico MP - Funciones</p>
        <p style='padding-top: 0px; color: white; font-size:16px; margin-top: -14px; font-weight: normal;'><?php echo $_SESSION['name'];?></p>
    </div>
    <div id='contain-actividad' class='campo'>
        <div class='name'>
            <p>Actividad</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><select id='f_actividad' class='inputact camposel' req='1' tipo='select'><option value=''>Selecciona...</option>
            <?php
                $query="SELECT * FROM trfMP_actividad WHERE activo=1 ORDER BY actividad";
                $result=mysql_query($query);
                $num=mysql_numrows($result);
                $i=0;
                while($i<$num){
                    echo "<option nombre='actividad' value='".mysql_result($result,$i,'id')."' em='".mysql_result($result,$i,'em')."' loc='".mysql_result($result,$i,'localizador')."' pnr='".mysql_result($result,$i,'pnr')."'>".utf8_encode(mysql_result($result,$i,'actividad'))."</option>";
                $i++;
                }
            ?></select>*</p>
        </div>
    </div>
    <?php
    	foreach($opts as $nivel => $data){
    		
			//SELECTS
    		echo "
				<div id='contain-level".$nivel."' class='campo containers' level='$nivel'>
			        <div class='name'>
			            <p id='lev".$nivel."title'>Actividad</p>
			        </div>
			        <div class='opcion'>
			            <p class='seleccion'><select id='f_level".$nivel."' class='input levelSelect camposel' req='1' tipo='select'><option value=''>Selecciona...</option>";
    		foreach($data as $actividad => $data2){
    			foreach($data2 as $id => $info){
	    			if($info['tipo']=='select'){
	    				echo "<option value='$id' id='input_$id' nombre='".$info['opcion']."' class='options$nivel act_$actividad opts' trig='".$info['trigger']."' parent='".$info['parent']."'>".$info['opcion']."</option>";
	    			}
				}
    		}
			echo "			</select>*</p>
					        </div>
					    </div>\n\n
					";
					
			//TEXTS
			foreach($data as $actividad => $data2){
    			foreach($data2 as $id => $info){
	    			if($info['tipo']=='text'){
	    				echo "
						<div id='contain-".$info['opcion']."' class='campo act_$actividad id_$id opts containers'>
					        <div class='name'>
					            <p id='lev".$nivel."title-txt'>".$info['titulo']."</p>
					        </div>
					        <div class='opcion'>
					            <p class='seleccion'><input type='text' id='f_".$info['opcion']."' nombre='".$info['opcion']."' parent='".$info['parent']."' class='input levelSelect camposel act_$actividad' req='1'>
		    		
			    					</p>
							        </div>
							    </div>\n\n
							";
			    			}
				}
    		}
		}
    		
    ?>
    <div id='contain-em' class='campo default'>
        <div class='name'>
            <p>EM</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><input type='text' id='f_em' class='input camposel' value=''></p>
        </div>
    </div>
    <div id='contain-loc' class='campo default'>
        <div class='name'>
            <p>Localizador</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><input type='text' id='f_loc' class='input camposel' value=''></p>
        </div>
    </div>
    <div id='contain-pnr' class='campo default'>
        <div class='name'>
            <p>PNR</p>
        </div>
        <div class='opcion'>
            <p class='seleccion'><input type='text' id='f_pnr' class='input camposel' value=''></p>
        </div>
    </div>
    <div id='contain-submit' class='campo' style='text-align: right;'>
        <button class='button button_red_w' id='submit_form'>Guardar</button>
    </div>
    
</div>
</div>
<div style='float:right; width: 30%; margin: auto;'>
<div id="sidebar">
   <iframe id='regs' width='100%' height='100%' style='border: 0;' src='registros.php?area=<?php echo $area; ?>'></iframe>
 </div>
 </div>
<div id='login'></div>
<div id='error'></div>
