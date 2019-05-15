<?php
include_once("../modules/modules.php");

timeAndRegion::setRegion('Mex');

$connectdb=Connection::mysqliDB('CC');
$connectdbcc=Connection::mysqliDB('WFM');

$cun_time = new DateTimeZone('America/Bogota');

//Declare Type of Output
$tipo=$_GET['tipo'];
$getskill=$_GET['skill'];

//Function Declares

    //Data to Arrays
    function getData($input,$varname){
        global $block;

        preg_match_all("/<tr>(.*?)<\/tr>/s", $input, $resume);

        foreach($resume[1] as $index => $data){
            if($index==0){continue;}
            preg_match_all("/<td(.*?)<\/td>/s", $data, $tmp);
            foreach($tmp[1] as $ind => $info){
                $name=substr($info,strpos($info, "'")+1,strpos($info, ">")-strpos($info, "'")-2);
                $block[$varname][$index][$name]=substr($info,strpos($info, ">")+1,100);
                //echo "index // $ind // $name // ".$resumen[$index][$name]."<br>";
            }
            unset($ind,$info,$tmp);
        }
    }

	//explode sla info
	function getVarSla($variable,$input){
		global $blockSla;

		preg_match_all("/-queue- (.*?) -queue-/s", $input, $q_output);
		preg_match_all("/-$variable- (.*?) -$variable-/s", $input, $v_output);
		$blockSla[$q_output[1][0]][$variable]=$v_output[1][0];
	}

	//Build BlockSLA
	function getSlaBlock($input){
		getVarSla("Answered",$input);
		getVarSla("Unanswered",$input);
		getVarSla("sla20",$input);
		getVarSla("sla30",$input);
	}

//GetSkills
$query="SELECT a.* FROM Cola_Skill a LEFT JOIN PCRCs b ON a.Skill_sec=b.id";
if($result=$connectdb->query($query)){
  while($fila=$result->fetch_assoc()){
    $skill[$fila['Skill_sec']][]=$fila['Cola'];
    $skillq[$fila['Skill_sec']][]=$fila['queue'];
    $q_skill[$fila['id']][]=$fila['Cola'];
    $qskillq[$fila['id']][]=$fila['queue'];
    $queue_name[$fila['Cola']]=$fila['queue'];
    $queue_num[$fila['queue']]=$fila['Cola'];
  }
}else{
  echo "Error: ".$connectdb->error;
}
unset($result);

//Get Calls per Skill
$query="SELECT * FROM d_PorCola a LEFT JOIN Asesores b ON a.asesor=b.id WHERE Fecha=CURDATE()";
if($result=$connectdb->query($query)){
  while($fila=$result->fetch_assoc()){
    $porcola[$fila['N Corto']][$fila['Skill']]=$fila['Calls'];
  }
}else{
  echo "Error: ".$connectdb->error;
}
unset($result);

//Calls
$query="SELECT * FROM mon_live_calls_row WHERE tipo=2 ORDER BY Last_Update DESC LIMIT 1";
if($result=$connectdbcc->query($query)){
  while($fila=$result->fetch_assoc()){

    $lu=$fila['Last_Update'];
    $txt=str_replace(" &nbsp;","",utf8_encode($fila['live']));
    $txt=str_replace("</td>","</td>\n",$txt);
  }
}else{
  echo "Error: ".$connectdbcc->error;
}
unset($result);

//Resumen
$query="SELECT * FROM mon_live_calls_row WHERE tipo=1 ORDER BY Last_Update DESC LIMIT 1";
if($result=$connectdbcc->query($query)){
  while($fila=$result->fetch_assoc()){
    $lu=$fila['Last_Update'];
    $txtR=str_replace(" &nbsp;","",utf8_encode($fila['live']));
    $txtR=str_replace("</td>","</td>\n",$txtR);
  }
}else{
  echo "Error: ".$connectdbcc->error;
}
unset($result);


//Agentes
$query="SELECT * FROM mon_live_calls_row WHERE tipo=3 ORDER BY Last_Update DESC LIMIT 1";
if($result=$connectdbcc->query($query)){
  while($fila=$result->fetch_assoc()){
    $lu=$fila['Last_Update'];
    $txtA=str_replace(" &nbsp;","",utf8_encode($fila['live']));
    $txtA=str_replace("</td>","</td>\n",$txtA);
  }
}else{
  echo "Error: ".$connectdbcc->error;
}
unset($result);

//SLA
$query="SELECT * FROM mon_live_calls_row WHERE tipo=4 ORDER BY Last_Update DESC LIMIT 1";
if($result=$connectdbcc->query($query)){
  while($fila=$result->fetch_assoc()){
    $lu=$fila['Last_Update'];
    $txtSLA=$fila['live'];
  }
}else{
  echo "Error: ".$connectdbcc->error;
}
unset($result);

//Explode SLA
preg_match_all("/<(.*?)>/s", $txtSLA, $sla);
foreach($sla[1] as $index => $data){
	getSlaBlock($data);
}
unset($index,$data);

//Free Since
$query="SELECT asesor, last_try FROM mon_calls_intentos";
if($result=$connectdbcc->query($query)){
	while($fila=$result->fetch_assoc()){
		if(date('Y-m-d', strtotime($fila["last_try"]))==date('Y-m-d')){
			$lasttry[$fila['asesor']]=(intval(date('H', strtotime($fila["last_try"])))*60*60+intval(date('i', strtotime($fila["last_try"])))*60+intval(date('s', strtotime($fila["last_try"]))));
		}else{
			$lasttry[$fila['asesor']]=0;
		}
	}
}

/*
//Get Comidas
$query="SELECT NombreAsesor(asesor,1) as asesor, `comida start`, `comida end` FROM `Historial Programacion` WHERE Fecha=CURDATE()";
if($result=$connectdb->query($query)){
	while($fila=$result->fetch_assoc()){
		if($fila['comida start']==$fila['comida end']){
			$tmpcomida="NA";
		}else{
			$inicio = new DateTime(date('Y-m-d')." ".$fila['comida start']." America/Mexico_City");
			$fin = new DateTime(date('Y-m-d')." ".$fila['comida end']." America/Mexico_City");
			$inicio -> setTimezone($cun_time);
			$fin -> setTimezone($cun_time);
			$tmpcomida=$inicio->format('H:i')." - ".$fin->format('H:i');
		}
		$comida[$fila['asesor']]=$tmpcomida;
	}
}
*/

//Get Asesores CC
$query="SELECT a.id, `N Corto` as asesor, Departamento FROM `Asesores` a LEFT JOIN PCRCs b ON `id Departamento`=b.id WHERE `id Departamento` NOT IN (29,31)";
if($result=$connectdb->query($query)){
	while($fila=$result->fetch_assoc()){
		$asesoresCC[]=$fila['asesor'];
		$asesoresCCdep[$fila['asesor']]=$fila['Departamento'];
	}
}


//Create Arrays
getData($txt,'cbp');
getData($txtR,'resume');
getData($txtA,'agn');

//Agents and Pauses per Queue
foreach($block['agn'] as $index => $data){
    $tmpqueue=str_replace(", ",",",$data['Queue(s):']);
    $queuenames = explode(",", $tmpqueue);
    foreach($queuenames as $ind => $nqueue){
    	if($data['Agent']=='Fredy Canchola (800)'){continue;}
        $queue[$nqueue]['Agents'][]=$data['Agent'];
		//Pauses
        if($data['On pause']!='-'){
            $queue[$nqueue]['OnPause'][$index]['asesor']=$data['Agent'];
			$queue[$nqueue]['OnPause'][$index]['motivo']=substr($data['On pause'],strpos(":", $data['On pause'])+6,100);
			//$queue[$nqueue]['OnPause'][$ind]['motivo']=$data['On pause'];
			$tmpdate=substr($data['On pause'],0,strpos(":",$data['On pause'])+5).":00";
			$now=date('H')*60*60+date('i')*60+date('s');
			$tts=date('H',strtotime($tmpdate))*60*60+date('i',strtotime($tmpdate))*60;
			//$queue[$nqueue]['OnPause'][$ind]['since']=$now-$tts;
			$queue[$nqueue]['OnPause'][$index]['seg']=($now)-($tts);
			$queue[$nqueue]['OnPause'][$index]['since']=gmdate('H:i:s',($now)-($tts));
		}
    }
    unset($ind,$nqueue,$queuenames);
}


//Agents in call
foreach($block['cbp'] as $index => $data){
	//Agents In Call
	if($data['Agent']=='Fredy Canchola (800)'){continue;}
	if($data['Duration']!="-"){
        $agentsInCall[]=$data['Agent'];
		$cbp[$data['Queue']][$data['Agent']]['Duration']=gmdate('H:i:s',(substr($data['Duration'],0,strlen($data['Duration'])-3)*60+substr($data['Duration'],-2,5)));
		$cbp[$data['Queue']][$data['Agent']]['seg']=substr($data['Duration'],0,strlen($data['Duration'])-3)*60+substr($data['Duration'],-2,5);
		$cbp[$data['Queue']][$data['Agent']]['start']=$data['Entered'];
		$cbp[$data['Queue']][$data['Agent']]['Caller']=$data['Caller'];
    }else{
    	//if(in_array($data['Queue'],$skill['5'])){
    		$tmpasesor=substr($data['Agent'],0,strpos($data['Agent'],")")+1);
			$query="INSERT INTO mon_calls_intentos VALUES ('$tmpasesor','".date('Y-m-d')." ".$data['Entered']."')";
			if($result=$connectdbcc->query($query)){
			}else{
				$block['error']=$connectdbcc->error;
				$query="UPDATE mon_calls_intentos SET last_try='".date('Y-m-d')." ".$data['Entered']."' WHERE asesor='$tmpasesor'";
				if($result=$connectdbcc->query($query)){
				}else{
					$block['error']=$connectdbcc->error;
				}
			}

    	//}


    }

}
unset($index,$data);

//Create Skill info
foreach($skill as $skillnum => $skdata){
    //In-Calls and Waiting
    foreach($block['cbp'] as $index => $info){

        //By Skill
        if(in_array($info['Queue'],$skdata)){
            if($info['Duration']=='-'){
                $rtcalls['skill'][$skillnum]['Waiting'][$index]=$info['Caller'];
                $rtcalls['skill'][$skillnum]['waittime'][$index]=substr($info['Waiting'],0,strpos($info['Waiting'],':'))*60+substr($info['Waiting'],strpos($info['Waiting'],':')+1,2);
            }else{
            	if($info['Queue']=="OUTBound CUN" || $info['Queue']=="OUTBound Mex"){
            		$rtcalls['skill'][$skillnum]['out-call'][$index]=$info['Caller'];
            	}else{
            		$rtcalls['skill'][$skillnum]['in-call'][$index]=$info['Caller'];
            	}
                $rtcalls['skill'][$skillnum]['duration'][$index]=substr($info['Duration'],0,strpos($info['Duration'],':'))*60+substr($info['Duration'],strpos($info['Duration'],':')+1,2);
            }

        }

        //All
        if($info['Duration']=='-'){
            $rtcalls['queue'][$queue_name[$info['Queue']]]['Waiting'][$index]=$info['Caller'];
            $rtcalls['queue'][$queue_name[$info['Queue']]]['waittime'][$index]=substr($info['Waiting'],0,strpos($info['Waiting'],':'))*60+substr($info['Waiting'],strpos($info['Waiting'],':')+1,2);
        }else{
          if($info['Queue']=="OUTBound CUN" || $info['Queue']=="OUTBound Mex"){
            $rtcalls['queue'][$queue_name[$info['Queue']]]['out-call'][$index]=$info['Caller'];
          }else{
            $rtcalls['queue'][$queue_name[$info['Queue']]]['in-call'][$index]=$info['Caller'];
          }
            $rtcalls['queue'][$queue_name[$info['Queue']]]['duration'][$index]=substr($info['Duration'],0,strpos($info['Duration'],':'))*60+substr($info['Duration'],strpos($info['Duration'],':')+1,2);
        }
    }
    unset($index,$info);

    //Agents per Skill
    foreach($queue as $qname => $info){

        //By Skill
        if(in_array($qname,$skdata)){
            foreach($info['Agents'] as $ind => $data){
                if(in_array($data,$agentsInCall)){$avail=0;}else{$avail=1;}
                $rtcalls['skill'][$skillnum]['agents'][$data]=$avail;
            }
            unset($ind,$data);
            if(count($info['OnPause'])>0){
                foreach($info['OnPause'] as $ind => $data){
                	$rtcalls['skill'][$skillnum]['agents'][$data['asesor']]=0;
                    $rtcalls['skill'][$skillnum]['OnPause'][$data['asesor']]=1;
                }
                unset($ind,$data);
            }
        }

        //All
        foreach($info['Agents'] as $ind => $data){
            if(in_array($data,$agentsInCall)){$avail=0;}else{$avail=1;}
            $rtcalls['queue'][$queue_name[$qname]]['agents'][$data]=$avail;
        }
        unset($ind,$data);
        if(count($info['OnPause'])>0){
            foreach($info['OnPause'] as $ind => $data){
              $rtcalls['queue'][$queue_name[$qname]]['agents'][$data['asesor']]=0;
                $rtcalls['queue'][$queue_name[$qname]]['OnPause'][$data['asesor']]=1;
            }
            unset($ind,$data);
        }
    }
    unset($qname,$info);

	//Pauses per Skill
	foreach($queue as $qskill => $data){
    //By Skill
		if(in_array($qskill,$skdata)){
		    if(count($data['OnPause'])>0){
    			foreach($data['OnPause'] as $ind => $info){
    				$rtcalls['skill'][$skillnum]['Paused'][$info['asesor']]['motivo']=$info['motivo'];
    				$rtcalls['skill'][$skillnum]['Paused'][$info['asesor']]['time']=$info['since'];
    				$rtcalls['skill'][$skillnum]['Paused'][$info['asesor']]['seg']=$info['seg'];
    			}
    			unset ($ind,$info);
            }
		}

    //All
    if(count($data['OnPause'])>0){
      foreach($data['OnPause'] as $ind => $info){
        $rtcalls['queue'][$queue_name[$qskill]]['Paused'][$info['asesor']]['motivo']=$info['motivo'];
        $rtcalls['queue'][$queue_name[$qskill]]['Paused'][$info['asesor']]['time']=$info['since'];
        $rtcalls['queue'][$queue_name[$qskill]]['Paused'][$info['asesor']]['seg']=$info['seg'];
      }
      unset ($ind,$info);
    }
	}
	unset($qskill,$data);

	//Calls per Skill
	foreach($skdata as $indice => $qname){
    //By Skill
		if(in_array($qname,$skdata)){
			if($qname=="OUTBound CUN" || $qname=="OUTBound Mex"){
			    if(count($cbp[$qname])>0){
    				foreach($cbp[$qname] as $asesor => $data){
    					if(date('H:i:s', strtotime($data['start']))>date('H:i:s', strtotime($rtcalls['skill'][$skillnum]['OutCalls'][$asesor]['start'])) || $rtcalls['skill'][$skillnum]['OutCalls'][$asesor]['start']==NULL){
    						$rtcalls['skill'][$skillnum]['OutCalls'][$asesor]['start']=$data['start'];
    						$rtcalls['skill'][$skillnum]['OutCalls'][$asesor]['Duration']=$data['Duration'];
    						$rtcalls['skill'][$skillnum]['OutCalls'][$asesor]['seg']=$data['seg'];
    						$rtcalls['skill'][$skillnum]['OutCalls'][$asesor]['Caller']=$data['Caller'];
    						$rtcalls['skill'][$skillnum]['out-dur'][]=$data['seg'];
    					}
    				}
                }
            }else{
                if(count($cbp[$qname])>0){
                    foreach($cbp[$qname] as $asesor => $data){
                		if(date('H:i:s', strtotime($data['start']))>date('H:i:s', strtotime($rtcalls['skill'][$skillnum]['InCalls'][$asesor]['start'])) || $rtcalls['skill'][$skillnum]['InCalls'][$asesor]['start']==NULL){
    						$rtcalls['skill'][$skillnum]['InCalls'][$asesor]['start']=$data['start'];
    						$rtcalls['skill'][$skillnum]['InCalls'][$asesor]['Duration']=$data['Duration'];
    						$rtcalls['skill'][$skillnum]['InCalls'][$asesor]['seg']=$data['seg'];
    						$rtcalls['skill'][$skillnum]['InCalls'][$asesor]['Caller']=$data['Caller'];
    						$rtcalls['skill'][$skillnum]['in-dur'][]=$data['seg'];
    					}
                	}
                }

			}
			unset($asesor,$data);
		}

      //ALL
			if($qname=="OUTBound CUN" || $qname=="OUTBound Mex"){
			    if(count($cbp[$qname])>0){
    				foreach($cbp[$qname] as $asesor => $data){
    					if(date('H:i:s', strtotime($data['start']))>date('H:i:s', strtotime($rtcalls['queue'][$queue_name[$qname]]['OutCalls'][$asesor]['start'])) || $rtcalls['queue'][$queue_name[$qname]]['OutCalls'][$asesor]['start']==NULL){
    						$rtcalls['queue'][$queue_name[$qname]]['OutCalls'][$asesor]['start']=$data['start'];
    						$rtcalls['queue'][$queue_name[$qname]]['OutCalls'][$asesor]['Duration']=$data['Duration'];
    						$rtcalls['queue'][$queue_name[$qname]]['OutCalls'][$asesor]['seg']=$data['seg'];
    						$rtcalls['queue'][$queue_name[$qname]]['OutCalls'][$asesor]['Caller']=$data['Caller'];
    						$rtcalls['queue'][$queue_name[$qname]]['out-dur'][]=$data['seg'];
    					}
    				}
                }
            }else{
                if(count($cbp[$qname])>0){
                    foreach($cbp[$qname] as $asesor => $data){
                		if(date('H:i:s', strtotime($data['start']))>date('H:i:s', strtotime($rtcalls['queue'][$queue_name[$qname]]['InCalls'][$asesor]['start'])) || $rtcalls['queue'][$queue_name[$qname]]['InCalls'][$asesor]['start']==NULL){
    						$rtcalls['queue'][$queue_name[$qname]]['InCalls'][$asesor]['start']=$data['start'];
    						$rtcalls['queue'][$queue_name[$qname]]['InCalls'][$asesor]['Duration']=$data['Duration'];
    						$rtcalls['queue'][$queue_name[$qname]]['InCalls'][$asesor]['seg']=$data['seg'];
    						$rtcalls['queue'][$queue_name[$qname]]['InCalls'][$asesor]['Caller']=$data['Caller'];
    						$rtcalls['queue'][$queue_name[$qname]]['in-dur'][]=$data['seg'];
    					}
                	}
                }

			}
			unset($asesor,$data);
	}
	unset($qname,$indice);

}
unset($skillnum,$skdata);

//Create Skill info
foreach($skillq as $skillnum => $skdata){
	foreach($skdata as $index => $skqueue){
		$slaData[$skillnum]['Answered']+=$blockSla[$skqueue]['Answered'];
		$slaData[$skillnum]['Unanswered']+=$blockSla[$skqueue]['Unanswered'];
		$slaData[$skillnum]['sla20']+=$blockSla[$skqueue]['sla20'];
		$slaData[$skillnum]['sla30']+=$blockSla[$skqueue]['sla30'];
		$slaData[$skillnum]['Total']+=$blockSla[$skqueue]['Answered']+$blockSla[$skqueue]['Unanswered'];
	}
}
unset($skillnum, $skdata);

//Agents Array
foreach($block['agn'] as $agent => $agndata){
	if(strpos($agndata['Agent'],"(")>0){
		$tmpagent=substr($agndata['Agent'],0,strpos($agndata['Agent'],"(")-1);
	}else{
		$tmpagent=$agndata['Agent'];
	}

	//Queues
	$tmpqueue=str_replace(", ",",",$agndata['Queue(s):']);
    $queuenames = explode(",", $tmpqueue);
	foreach($queuenames as $index => $qname){
		$asesor[$agndata['Agent']][Queues][]=$qname;
	}
	unset($tmpqueue,$queuenames,$index,$qname);

	//Calls
	foreach($block['cbp'] as $index => $info){
		if($info['Duration']!="-"){
			$asesor[$info['Agent']]['Call']['Caller']=$info['Caller'];
			if($info['Queue']=='Servicio a Cliente'){
				$asesor[$info['Agent']]['Call']['Queue']="Servicio a Cliente - Pool C";
			}else{
				$asesor[$info['Agent']]['Call']['Queue']=$info['Queue'];
			}
			$asesor[$info['Agent']]['Call']['Duration']=gmdate('H:i:s',(substr($info['Duration'],0,strlen($info['Duration'])-3)*60+substr($info['Duration'],-2,5)));
			$asesor[$info['Agent']]['Call']['seg']=substr($info['Duration'],0,strlen($info['Duration'])-3)*60+substr($info['Duration'],-2,5);
		}
	}

	//Pauses
	if($agndata['On pause']!='-'){
		$asesor[$agndata['Agent']]['Pause']['Motivo']=substr($agndata['On pause'],strpos(":", $agndata['On pause'])+6,100);
		$tmpdate=substr($agndata['On pause'],0,strpos(":",$agndata['On pause'])+5).":00";
		$now=date('H')*60*60+date('i')*60+date('s');
		$tts=date('H',strtotime($tmpdate))*60*60+date('i',strtotime($tmpdate))*60;
		$asesor[$agndata['Agent']]['Pause']['seg']=($now)-($tts);
		$asesor[$agndata['Agent']]['Pause']['tiempo']=gmdate('H:i:s',($now)-($tts));
	}

	//Status
	if(count($asesor[$agndata['Agent']]['Pause'])!=0){
		if(count($asesor[$agndata['Agent']]['Call'])!=0){
			$asesor[$agndata['Agent']]['Status']=3;
		}else{
			$asesor[$agndata['Agent']]['Status']=2;
		}
	}elseif(count($asesor[$agndata['Agent']]['Call'])!=0){
		if(substr($asesor[$agndata['Agent']]['Call']['Queue'],0,3)=='OUT'){
			$asesor[$agndata['Agent']]['Status']=4;
		}else{
			$asesor[$agndata['Agent']]['Status']=1;
		}

	}else{
		$asesor[$agndata['Agent']]['Status']=0;
	}

	//Tiempo disponible
	/*if($agndata['Free Since']=='-'){
		$tmpdateseg=(intval(date('H'))*60*60+intval(date('i'))*60+intval(date('s')))-(intval(substr($agndata['Last logon'],8,2))*60*60+intval(substr($agndata['Last logon'],11,2))*60+intval(substr($agndata['Last logon'],14,2)));
		$asesor[$agndata['Agent']]['Avail']=gmdate('H:i:s',$tmpdateseg);
		$asesor[$agndata['Agent']]['Availseg']=$tmpdateseg;
	}else{
		$asesor[$agndata['Agent']]['Avail']=gmdate('H:i:s',(substr($agndata['Free Since'],0,strlen($agndata['Free Since'])-3)*60+substr($agndata['Free Since'],-2,5)));
		$asesor[$agndata['Agent']]['Availseg']=(substr($agndata['Free Since'],0,strlen($agndata['Free Since'])-3)*60+substr($agndata['Free Since'],-2,5));
	}*/
	if($agndata['Free Since']=='-'){
		if(strlen($agndata['Last call'])==6){
			if(substr($asesor[$agndata['Agent']]['Call']['Queue'],0,3)=='OUT'){
				$tmpdateseg=(intval(date('H'))*60*60+intval(date('i'))*60+intval(date('s')))-$lasttry[$agndata['Agent']];
				$asesor[$agndata['Agent']]['Avail']=gmdate('H:i:s',$tmpdateseg);
				$asesor[$agndata['Agent']]['Availseg']=$tmpdateseg;

			}else{
				$lasttry[$agndata['Agent']]=0;
				$tmpdateseg=(intval(date('H'))*60*60+intval(date('i'))*60+intval(date('s')))-(intval(substr($agndata['Last logon'],8,2))*60*60+intval(substr($agndata['Last logon'],11,2))*60+intval(substr($agndata['Last logon'],14,2)));
				$asesor[$agndata['Agent']]['Avail']=gmdate('H:i:s',$tmpdateseg);
				$asesor[$agndata['Agent']]['Availseg']=$tmpdateseg;
			}

		}else{
			$tmpdateseg=(intval(date('H'))*60*60+intval(date('i'))*60+intval(date('s')))-(intval(date('H',strtotime($agndata['Last call'])))*60*60+intval(date('i',strtotime($agndata['Last call'])))*60+intval(date('s',strtotime($agndata['Last call']))));
			$asesor[$agndata['Agent']]['Avail']=gmdate('H:i:s',$tmpdateseg);
			$asesor[$agndata['Agent']]['Availseg']=(intval(date('H',strtotime($agndata['Last call'])))*60*60+intval(date('i',strtotime($agndata['Last call'])))*60+intval(date('s',strtotime($agndata['Last call']))));
		}
	}else{
		$asesor[$agndata['Agent']]['Avail']=gmdate('H:i:s',(substr($agndata['Free Since'],0,strlen($agndata['Free Since'])-3)*60+substr($agndata['Free Since'],-2,5)));
		$asesor[$agndata['Agent']]['Availseg']=(substr($agndata['Free Since'],0,strlen($agndata['Free Since'])-3)*60+substr($agndata['Free Since'],-2,5));
	}

	//$asesor[$agndata['Agent']]['Avail']=strlen($agndata['Last call']);

	//Calls Processed
	$asesor[$agndata['Agent']]["Processed"]=$porcola[$tmpagent];


}
ksort($asesor);

//print rtcalls
function p_rtcalls($sknum,$type){
	global $rtcalls,$datos;

  if($type=='skill'){
    $tipo='skill';
  }else{
    $tipo='queue';
  }

	$info=$rtcalls[$tipo][$sknum];

  //pre($info);

	if(count($info['in-dur'])==0){$info['in-dur'][]=0;}
    if(count($info['out-dur'])==0){$info['out-dur'][]=0;}
    if(count($info['OnPause'])==0){$info['OnPause'][]=0;}
    if(count($info['waittime'])==0){$info['waittime'][]=0;}

	$datos['avail']=array_sum($info['agents']);
	$datos['inbound']=count($info['InCalls']);
	$datos['outbound']=count($info['OutCalls']);
	$datos['aht']=gmdate('H:i:s',(array_sum($info['in-dur'])+array_sum($info['out-dur']))/(count($info['in-dur'])+count($info['out-dur'])));
	$datos['ahtseg']=(array_sum($info['in-dur'])+array_sum($info['out-dur']))/(count($info['in-dur'])+count($info['out-dur']));
	$datos['waiting']=count($info['Waiting']);
	$datos['longestw']=gmdate('H:i:s',intval("0".max($info['waittime'])));
	$datos['longestcall']=gmdate('H:i:s',intval("0".max($info['in-dur'])));
	$datos['online']=count($info['agents']);
	$datos['pause']=intval("0".array_sum($info['OnPause']));

}

//Waits
function p_waits($type){
	global $rtcalls, $datos, $queue_num;

  if($type=='skill'){
    $tipo='skill';
    foreach($rtcalls[$tipo] as $sknum => $info){
          $datos['waits'][$sknum]=count($info['Waiting']);
  	}
  }else{
    $tipo='queue';
    foreach($rtcalls[$tipo] as $sknum => $info){
          $datos['waits'][$queue_num[$sknum]]=count($info['Waiting']);
  	}
  }


}

preg_match_all("/<span class=\"card-title activator grey-text text-darken-4\">(.*)<i/", $pbx_file, $ext);
preg_match_all("/<p>(.*)<\/p>/", $pbx_file, $dev);

foreach($ext[1] as $index => $extension){
	$tmp=str_replace(" ", "", $extension);
	$tmp=str_replace("Ext:", "", $tmp);
	$ext_est[$tmp]=str_replace(",","",$dev[1][$index]);
}


//print Asesores
function p_asesor($skill_get,$type){
	global $asesor, $skill, $getskill, $ext_est, $datos, $comida, $asesoresCC,$asesoresCCdep, $queue_num;
	$i=0;
	if(isset($asesor)){
		foreach($asesor as $agent => $data){

			$tmpstatus=$data['Status'];

			if(strpos($agent,"(")==0){
				$asesor_name=$agent;
				$tmpagent_noext=$agent;
				$ext=substr($agent, strpos($agent, "/")+1, 5);
			}else{
				$tempext=substr($agent, strpos($agent, "(")+1, -1);
				if($ext_est[$tempext]=="" || !isset($ext_est[$tempext])){
					//$tmpEst=" (NL)";
					//$tmpstatus=5;
				}else{
					$tmpEst="";
				}
				$tmpagent_noext=substr($agent,0,strpos($agent,"(")-1);
				$tmpagent=str_replace(" (", "<br>(", $agent);
				$asesor_name=str_replace(" (", "<br>(", $agent);
				$ext=substr($agent, strpos($agent, "(")+1, -1);
				//$asesor_name=str_replace(")"," - ".$ext_est[$tempext]." )",$tmpagent);

			}

			//Only Skill or QUEUE Selected
			$flag=false;

      if($type=='cola'){
        if(isset($skill)){
          if(isset($data['Queues'])){
  						if(in_array($queue_num[$skill_get], $data['Queues'])){
  							$flag=true;
				       }
  				}
  				unset($index,$info);
  			}
        $datos['Title']=$queue_num[$skill_get];
      }else{
        if(isset($skill)){
          foreach($skill[$skill_get] as $index => $info){
            if(isset($data['Queues'])){
  						if(in_array($info, $data['Queues'])){
  							$flag=true;
  						}
            }
  				}
  				unset($index,$info);
  			}
      }

      //$datos['asesor'][$agent]['test']="QName: ".$queue_num[$skill_get]." || QNum: $skill_get -> $agent";

			if($flag){
				if($data['Processed'][$skill_get]==NULL){
					$processed=0;
				}else{
					$processed=$data['Processed'][$skill_get];
				}



				$datos['asesor'][$i]['status']=$data['Status'];
				$datos['asesor'][$i]['asesor']=$tmpagent_noext;
				$datos['asesor'][$i]['ext']=$ext;
				$datos['asesor'][$i]['avail']=$data['Avail'];
				$datos['asesor'][$i]['availseg']=$data['Availseg'];
				$datos['asesor'][$i]['caller']=$data['Call']['Caller'];
				$datos['asesor'][$i]['queue']=$data['Call']['Queue'];
				$datos['asesor'][$i]['calldur']=$data['Call']['Duration'];
				$datos['asesor'][$i]['callseg']=$data['Call']['seg'];
				$datos['asesor'][$i]['pausem']=$data['Pause']['Motivo'];
				$datos['asesor'][$i]['pauseseg']=$data['Pause']['seg'];
				$datos['asesor'][$i]['pausedur']=$data['Pause']['tiempo'];
				$datos['asesor'][$i]['processed']=$processed;
				//@$datos['asesor'][$i]['comida']="C: ".$comida[$tmpagent_noext];
                @$datos['asesor'][$i]['comida']="C: ";

				if(in_array($tmpagent_noext,$asesoresCC)){
					$datos['asesor'][$i]['PDV']=0;
					$datos['asesor'][$i]['Departamento']=$asesoresCCdep[$tmpagent_noext];
				}else{
					$datos['asesor'][$i]['PDV']=1;
					$datos['asesor'][$i]['Departamento']='PDV';
				}

				$i++;
			}


		}
		unset($agent,$data);
	}

}



$datos['lu']=$lu;

function pre($data){
  echo "<pre>";
  print_r($data);
  echo "</pre>";
}

//PrintInfo
switch($tipo){
    case 'newRTMon':
  		p_asesor($_GET['skill'],'skill');
  		p_rtcalls($_GET['skill'],'skill');
  		p_waits('skill');
      if($_GET['pre']==1){
        pre($datos);
      }else{
  		    print json_encode($datos,JSON_PRETTY_PRINT);
      }
		break;
    case 'cola':
      p_asesor($_GET['skill'],'cola');
      p_rtcalls($_GET['skill'],'cola');
      p_waits('cola');
      if($_GET['pre']==1){
        pre($datos);
      }else{
          print json_encode($datos,JSON_PRETTY_PRINT);
      }
      break;
    case 'test':
      pre($rtcalls);
      break;

}

$connectdb->close();
$connectdbcc->close();

?>
