<?php
include("../connectDB.php");

$query="SELECT ac_id, IVR_duration, Hora_fin FROM t_Answered_Calls WHERE IVR_duration>'00:00:00'";
$result=mysql_query($query);
$num=mysql_numrows($result);

$i=0;
while($i<$num){
     $id[$i]=mysql_result($result,$i,'ac_id');
     $dura[$i]=mysql_result($result,$i,'IVR_duration');
     $hf[$i]=mysql_result($result,$i,'Hora_fin');
$i++;
}

function addTime($timeB, $timeA) {

    $timeAinSeconds = intval(date('H', strtotime($timeA)))*60*60 + intval(date('i', strtotime($timeA)))*60 + intval(date('s', strtotime($timeA)));
    $timeBinSeconds = intval(date('H', strtotime($timeB)))*60*60 + intval(date('i', strtotime($timeB)))*60 + intval(date('s', strtotime($timeB)));



    $timeABinSeconds = $timeAinSeconds - $timeBinSeconds;

    $timeABsec = $timeABinSeconds % 60;
    $timeABmin = (($timeABinSeconds - $timeABsec) / 60) % 60;
    $timeABh = ($timeABinSeconds - $timeABsec - $timeABmin*60) / 60 / 60;

    return str_pad((int) $timeABh,2,"0",STR_PAD_LEFT).":"
          .str_pad((int) $timeABmin,2,"0",STR_PAD_LEFT).":"
          .str_pad((int) $timeABsec,2,"0",STR_PAD_LEFT);
}
ob_start();
echo "Start Process... $num Regs<br>";
ob_flush();
foreach($id as $key => $llamada){
    $newhf=addtime($dura[$key],$hf[$key]);
    $query="UPDATE t_Answered_Calls SET Hora_fin='$newhf' WHERE ac_id='$llamada'";
    mysql_query($query);
            echo "$key:<br>$query<br>";
			if(mysql_errno()){
			    echo "$key MySQL error ".mysql_errno().": "
			         .mysql_error()."\n<br>When executing <br>\n$query\n<br><br>";
			}else{echo "OK<br><br>";}
    ob_flush();
}

echo "Done";
ob_end_flush();



?>