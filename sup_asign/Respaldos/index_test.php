<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];


if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
$credential="sup_asing";

//default timezone
date_default_timezone_set('America/Bogota');

include("../connectDB.php");
include("../common/list_asesores.php");
include("../common/scripts.php");

//GET VARIABLES
$dept=$_POST['dept'];
$asesor=$_POST['asesor'];
if(!isset($_POST['date'])){$fecha=date('Y-m-d');}else{$fecha=date('Y-m-d',strtotime($_POST['date']));}
$sup=$_POST['super'];
$i=0;
while($i<200){
	if(isset($_POST['asesor'.$i])){$asesor[$i]=$_POST['asesor'.$i]; $check[$i]="checked";}else{$asesor[$i]=$_POST['asesor'.$i]; $check[$i]="";}
$i++;
}

//QUERY Asesores
$query="SELECT * FROM Asesores WHERE `id Departamento`='$dept' AND Activo=1 ORDER BY `N Corto`";
$result=mysql_query($query);
$num=mysql_numrows($result);
if($num % 5 == 0){$filas=$num/5;}else{$filas=$num/5+1;}
?>

<style>
  #draggable, #draggable2 {
      width: 90px;
      height: 30px;
      padding: 0.5em;
      float: left;
      margin: 10px 10px 10px 0;
  }
  #droppable, #droppable2 {
      width: 120px;
      height: 120px;
      padding: 0.5em;
      float: left;
      margin: 10px;
  }
  h3 {
      clear: left;
  }
  .sortable {
      list-style-type: none;
      margin: 10px; float: left;
      margin-right: 10px;
      background: #eee;
      padding: 5px;
      width: 429px;
      height: 400px;
  }
  .draggy{
        float:left;
        width: 115px;
      color: white;
      background: #FF6666;
      padding: 0.5em;
      margin: 0.5em;
      cursor: pointer;


  }
  .titles{
      width:429px;
      background:#eee;
      height:auto;
      margin:10px;
      padding-left: 10px;
      margin-left: 10px;
      font-weight: bold;
      font-size: 12px;
      margin-bottom: -10px;
      padding-bottom: 0;
      padding-top: 5px;
      float:left;
      margin-top:30px;
  }

  </style>
  <script>
  $(function() {

  $('#lists').hide();

  function sendRequest(asesor,supervisor){
        var urlsend= "update_super.php?asesor="+asesor+"&super="+supervisor+"&fecha=<?php echo $fecha;?>&dep=<?php echo $dept; ?>";
        var xmlhttp;
        var text;

        if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        } else { // code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

        xmlhttp.onreadystatechange=function(){
            if (xmlhttp.readyState==4 && xmlhttp.status==200){
                text= xmlhttp.responseText;
                var status = text.match("status- (.*) -status");
                var notif_msg = text.match("msg- (.*) -msg");
                if(status[1]=='OK'){
                    tipo_noti='success';
                }else{
                    tipo_noti='error';

                }
                new noty({
                    text: notif_msg[1],
                    type: tipo_noti,
                    timeout: 5000,
                    animation: {
                        open: {height: 'toggle'}, // jQuery animate function property object
                        close: {height: 'toggle'}, // jQuery animate function property object
                        easing: 'swing', // easing
                        speed: 500 // opening & closing animation speed
                    },

                });

            }
        }
        xmlhttp.open("GET",urlsend,true);
        xmlhttp.send();

    }



    $( "ul.droptrue" ).sortable({
      connectWith: "ul",
      receive: function(event,ui){
          var receiver=$(this).attr("id");
          var asesor=ui.item.attr("id");
          var form=document.getElementById('as_'+asesor);
          form.value=receiver;
          sendRequest(asesor,receiver);
      }
    });



    $( ".sortable" ).disableSelection();
  });
  </script>
<?php
include("../common/menu.php");
?>
<table width='100%' class='t2'><form action="<?php $_SERVER['PHP_SELF'];?>" method='post'>
    <tr class='title'>
        <td colspan=100>Asignacion de Supervisores</td>

    </tr>
    <tr class='subtitle'>
        <td>Fecha de Inicio</td>
        <td rowspan=2 class='total'><input type="submit" value='Consultar' name='asignar' /></td>
    </tr>
    <tr class='pair'>
        <td><input type="date" name='date' value='<?php echo $fecha;?>' required></td>
    </tr>
    </form>
</table>

<br>
<? if(!isset($_POST['asignar'])){exit;} ?>
<br>
<table width='100%' class='t2'>
	<tr class='title'>
		<th colspan=100>Supervisores Asignados</th>
	</tr>
</table>
<br>
<?php

$query="SELECT a.id, Asesor, Supervisor, b.id as idsuper FROM
	(SELECT
		id, `N Corto` as Asesor, IF(FindSuperDay(".date(d,strtotime($fecha)).",".date(m,strtotime($fecha)).",".date(Y,strtotime($fecha)).",id) IS NULL,'0nosup',FindSuperDay(2,3,2016,id)) as Supervisor
	FROM
		Asesores
	WHERE
		`id Departamento`!= 12 AND `id Departamento`!= 1 AND Activo=1) a
	LEFT JOIN
		Asesores b
	ON
		a.Supervisor=b.`N Corto`

	ORDER BY
		Supervisor, Asesor";
$result=mysql_query($query);
$num=mysql_numrows($result);
$i=0;
while($i<$num){
    if($i==0){$tmp=mysql_result($result,$i,'Supervisor'); $z=0;
    }
    if($tmp!=mysql_result($result,$i,'Supervisor')){
        $tmp=mysql_result($result,$i,'Supervisor'); $z=0;
    }else{$z++;}
    $data[mysql_result($result,$i,'Supervisor')][$z]=mysql_result($result,$i,'Asesor');
    $id[mysql_result($result,$i,'Supervisor')][$z]=mysql_result($result,$i,'id');
    $supid[mysql_result($result,$i,'Supervisor')][$z]=mysql_result($result,$i,'idsuper');
    $tmp=mysql_result($result,$i,'Supervisor');

$i++;
}

$query="SELECT * FROM Asesores WHERE (`id Departamento`=12 OR `id Departamento`=1) AND Activo=1 ORDER BY `N Corto`";
$result=mysql_query($query);
$num=mysql_numrows($result);
$i=0;
while($i<$num){

    $sup[]=mysql_result($result,$i,'N Corto');
    $idsup[]=mysql_result($result,$i,'id');

$i++;
}
$sup[]="0nosup";
    $idsup[]="";
?>
<div class='titles' style='width:1348px; margin-bottom: -10px;'>Sin Supervisor</div>
<ul id='' class='droptrue sortable' style='width:1348px; height:auto;'>

        <?php
  foreach($sup as $ind => $super){
        if($super=="0nosup"){
            $supervisor="Sin Supervisor";
            foreach($data[$super] as $key => $asesor){
                echo "<li id='".$id[$super][$key]."' class='draggy'>$asesor</li>\n";
            }
        unset($key,$asesor);
        }
  }
   unset($ind,$super);
  ?>
    </ul>
  <?php
  foreach($sup as $ind => $super){
        if($super!="0nosup"){$supervisor="$super";
        echo "<div class='titles'>$supervisor";
        echo "<ul id='$idsup[$ind]' class='droptrue sortable' style='padding: 0; margin: 0;'>";
            foreach($data[$super] as $key => $asesor){
                echo "<li id='".$id[$super][$key]."' class='draggy'>$asesor</li>\n";
            }
        unset($key,$asesor);
        echo "</ul></div>";
        }
  }
   unset($ind,$super);
  ?>

<h3></h3>
 <br><br>
 <div id='lists'>
 <? foreach($data as $key => $super){
        foreach($super as $key2 =>$asesor){
            echo "<label for='".$id[$key][$key2]."'>$asesor</label>
<input id='as_".$id[$key][$key2]."' name='".$id[$key][$key2]."' type='text' value='".$supid[$key][$key2]."'/><br>\n";
        }
 }
 ?>
 </div>
