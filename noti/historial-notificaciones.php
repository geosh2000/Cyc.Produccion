<?php
session_start();
$this_page=$_SERVER['PHP_SELF'];
$iddiv=0;

if($_SESSION['login']!='1'){ include("../common/login.php"); exit;}
include("../connectDB.php");
date_default_timezone_set('America/Bogota');
include("../common/scripts.php");
?>
<script>
setTimeout(function() {
    window.location.reload();
}, 60000);

$(function(){

    function sendRequest(val_id){
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
                

            }
        }

        xmlhttp.open("GET","../json/noti_read_historial.php?id="+val_id,true);
        xmlhttp.send();

    }

    $('.read').button().on("click",function(){
        var valor=this.id;
       sendRequest(valor);
        $('#td'+valor).attr('class','green');
    });

});
</script>

<table width='100%' class='t2'>
    <tr class='title'>
        <td  colspan=100>Notificaciones Recientes</td>
    </tr>
    <tr class='title'>
        <?php
            $query="SELECT * FROM noti_categorias ORDER BY categoria";
            $result=mysql_query($query);
            $numcat=mysql_numrows($result);
            $i=0;
            while($i<$numcat){
                $width=100/$numcat;
                $categoria[$i]=mysql_result($result,$i,'categoria');
                echo "<td width='$width%'>".mysql_result($result,$i,'categoria')."</td>\n";
            $i++;
            }
        ?>
    </tr>
    <?php
        $query="SELECT `read`, id, message, b.categoria as categoria, date_sent FROM noti_mensajes a, noti_categorias b WHERE a.categoria=b.noti_cat_id AND `to`='".$_SESSION['id']."' AND date_sent>='".date('Y-m-d')."' ORDER BY b.categoria, date_sent DESC";
        //echo $query;
        $result=mysql_query($query);
        $num=mysql_numrows($result);
        $i=0;
        $x=0;
        $y=1;
        while($i<$num){
            $categ[$i]=mysql_result($result,$i,'categoria');
            $index[$categ[$i]]++;
            if($index[$categ[$i]]>$x){$x=$index[$categ[$i]];}
            $mensaje[$categ[$i]][$index[$categ[$i]]]=mysql_result($result,$i,'message');
            $date[$categ[$i]][$index[$categ[$i]]]=mysql_result($result,$i,'date_sent');
            $id[$categ[$i]][$index[$categ[$i]]]=mysql_result($result,$i,'id');
            $read_m[$categ[$i]][$index[$categ[$i]]]=mysql_result($result,$i,'read');
        $i++;
        }
        //print_r($mensaje);
        //echo $query;
        $z=1;
        while($y<=$x){
            if($y %2 == 0){$class="class='pair'";}else{$class="class='odd'";}
            echo "<tr $class>\n";
            $i=0;

            while($i<$numcat){
                if($mensaje[$categoria[$i]][$z]!=NULL){
                    if($read_m[$categoria[$i]][$z]!=1){
                        $read="<br><br><bt class='read' id='".$id[$categoria[$i]][$z]."'>Marcar como leido</bt>";
                    }
                }else{$read="";}
                if($mensaje[$categoria[$i]][$z]!=NULL && $read_m[$categoria[$i]][$z]!=1){$class_td="class='flashred'";}else{$class_td="";}
                echo "\t<td $class_td id='td".$id[$categoria[$i]][$z]."'>".$date[$categoria[$i]][$z]."<br><br>".$mensaje[$categoria[$i]][$z]."$read</td>\n";
            $i++;
            }
            $z++;
            echo "</tr>\n";
        $y++;
        }
    ?>

</table>