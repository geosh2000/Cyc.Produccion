<?php

include_once('../modules/modules.php');

timeAndRegion::setRegion('Cun');

$cun_time = new DateTimeZone('America/Bogota');

//Declare Type of Output
$tipo=$_GET['tipo'];

if(isset($_POST['tipo'])){
  $tipo=$_POST['tipo'];
}
$getskill=$_GET['skill'];

$connectdbcc=Connection::mysqliDB('WFM');
$connectdb=Connection::mysqliDB('CC');

class qmVars{

  //Main Queues
  public static function MainQueues($conection){
    $query="SELECT Skill, queue FROM Cola_Skill WHERE calls=1 GROUP BY queue";
    if($result=$conection->query($query)){
      $data['status']=1;
      while($fila=$result->fetch_assoc()){
        $data[$fila['Skill']][]=$fila['queue'];
      }
    }else{
      $data['status']=0;
      $data['msg']=utf8_encode("Error: ".$conection->error." on $query");
    }
    unset($result);

    return $data;
  }

  //RT Monitor
  public static function rtMon($conection, $conection2){
    $query="SELECT * FROM mon_live_calls_row WHERE tipo=3 ORDER BY Last_Update DESC LIMIT 1";
    if($result=$conection->query($query)){
      $data['status']=1;
      $fila=$result->fetch_assoc();
      $data['lu']=$fila['Last_Update'];
      $tmp=str_replace(" &nbsp;","",utf8_encode($fila['live']));
      $tmp=str_replace("</td>","</td>\n",$tmp);

      //break text
      preg_match_all("/<tr>(.*?)<\/tr>/s", $tmp, $rtInfo);

      foreach($rtInfo[1] as $index => $info){
        preg_match_all("/<td id='(.*)'>(.*?)<\/td>/m", $info, $tempData);

        foreach($tempData[1] as $index2 => $title){

          $data[$index][utf8_encode(strtolower($title))]=$tempData[2][$index2];
        }

        unset($tempData, $index2, $title);
      }

    }else{
      $data['status']=0;
      $data['msg']=utf8_encode("Error: ".$conection->error." on $query");
    }
    unset($result);

    return $data;
  }

  //SLA LIVE
  public static function SLA($conection, $conection2){
    $query="SELECT * FROM mon_live_calls_row WHERE tipo=4 ORDER BY Last_Update DESC LIMIT 1";
    if($result=$conection->query($query)){
      $data['status']=1;
      $fila=$result->fetch_assoc();
      $data['lu']=$fila['Last_Update'];
      $tmp=$fila['live'];

      //break text
      preg_match_all("/<(.*?)>/s", $tmp, $sla);

      foreach($sla[1] as $index => $info){
        preg_match_all("/-queue- (.*?) -queue-/s", $info, $queue);
        preg_match_all("/-Answered- (.*?) -Answered-/s", $info, $Answered);
        preg_match_all("/-Unanswered- (.*?) -Unanswered-/s", $info, $Unanswered);
        preg_match_all("/-sla20- (.*?) -sla20-/s", $info, $sla20);
        preg_match_all("/-sla30- (.*?) -sla30-/s", $info, $sla30);

        $temp=$queue[1][0];
        $data[$temp]['Answered']=$Answered[1][0];
        $data[$temp]['Unanswered']=$Unanswered[1][0];
        $data[$temp]['sla20_calls']=$sla20[1][0];
        $data[$temp]['sla30_calls']=$sla30[1][0];

        unset($queue, $Answered, $Unanswered, $sla20, $sla30);
      }

    }else{
      $data['status']=0;
      $data['msg']=utf8_encode("Error: ".$conection->error." on $query");
    }
    unset($result);

    $skills=qmVars::MainQueues($conection2);

    foreach($skills as $skill => $info){
      if($skill=='status'){continue;}
      foreach($info as $index => $datos){
        if(isset($data[$datos])){
          foreach($data[$datos] as $kpi => $datos2){
            @$resultado[$skill][$kpi]+=$datos2;
          }
        }
      }

      @$resultado[$skill]['calls']=$resultado[$skill]['Answered']+$resultado[$skill]['Unanswered'];
      if($resultado[$skill]['Answered']+$resultado[$skill]['Unanswered']==0){
        @$resultado[$skill]['abandon']=0;
        @$resultado[$skill]['sla20']=100;
        @$resultado[$skill]['sla30']=100;
      }else{
        @$resultado[$skill]['abandon']=number_format($resultado[$skill]['Unanswered']/($resultado[$skill]['Answered']+$resultado[$skill]['Unanswered'])*100,2);
        @$resultado[$skill]['sla20']=number_format($resultado[$skill]['sla20_calls']/$resultado[$skill]['Answered']*100,2);
        @$resultado[$skill]['sla30']=number_format($resultado[$skill]['sla30_calls']/$resultado[$skill]['Answered']*100,2);
      }
    }

    $resultado['status']=$data['status'];
    @$resultado['lu']=$data['lu'];
    @$resultado['msg']=$data['msg'];

    return $resultado;
  }
}

function timeConvert($lu){
  $cun_time = new DateTimeZone('America/Bogota');

  $tmplu = new DateTime($lu.' America/Mexico_City');
  $tmplu -> setTimezone($cun_time);

  return $tmplu->format('Y-m-d H:i:s');
}

function printPre($array){
  echo "<pre>";
  print_r($array);
  echo "</pre>";
}

switch($tipo){
  case 'slamon':
    $td=qmVars::SLA($connectdbcc, $connectdb);
    break;
  case 'rtmon':
    $td=qmVars::rtMon($connectdbcc, $connectdb);
    break;
}

//Convert to Local Time
$td['lu']=timeConvert($td['lu']);

//Print Info
//print json_encode($td, JSON_PRETTY_PRINT);
printPre($td);

$connectdb->close();
$connectdbcc->close();

?>
