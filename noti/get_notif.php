<?php
session_start();
include("../connectDB.php");
date_default_timezone_set('America/Bogota');
//include("../json/check_pause_timeout.php");
$now_date=date('Y-m-d');
$now_time=date('H:i:s');
$now_timestamp=date('Y-m-d H:i:s');
$user=$_GET['user'];
$page=$_GET['page'];

$query="SELECT *
        FROM noti_mensajes
        WHERE `to`='$user' AND valid_thru_date>='$now_timestamp'
                AND recieved!=1 ORDER BY id";
$result=mysql_query($query);
$num=mysql_numrows($result);
if($num==0){echo "status- 0 -status "; exit;}

    echo "status- 1 -status ";
    echo "tipo$i- ".mysql_result($result,0,'type')." -tipo$i ";
    echo "msg$i- ".mysql_result($result,0,'message')." -msg$i ";
    echo "title$i- ".mysql_result($result,0,'title')." -title$i ";
    if(mysql_result($result,0,'dialog')!=NULL){
        echo "dialog$i- ".mysql_result($result,0,'dialog')." -dialog$i ";
        echo "dtext$i- ".mysql_result($result,0,'dialog_message')." -dtext$i ";
    }

$query="UPDATE noti_mensajes
        SET recieved='1', recieved_date='$now_date', recieved_time='$now_time', page_shown='$page', session_id='".session_id()."' WHERE id='".mysql_result($result,0,'id')."'";
mysql_query($query);

//echo "<br><br>$query";





?>