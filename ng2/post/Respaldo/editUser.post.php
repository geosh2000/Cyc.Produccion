<?php
header("Access-Control-Allow-Origin: *");

include_once("../../modules/modules.php");
include_once("../../common/JWT.php");
include_once("../validateToken.php");

timeAndRegion::setRegion('Cun');

validateTk(function(){
    function objectToArray ($object) {
        if(!is_object($object) && !is_array($object))
            return $object;

        return array_map('objectToArray', (array) $object);
    }

    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);

    $form = $request->form;
    $original = $request->original;
    $qNames = $request->queryNames;
    $changer = $request->changer;

    $data['form'] = objectToArray($form);
    $data['original'] = objectToArray($original);
    $data['qNames'] = objectToArray($qNames);


    function queryHistoric($asesor, $campo, $old_val, $new_val, $changed_by){
        return "INSERT INTO historial_asesores (asesor, campo, old_val, new_val, changed_by) VALUES ($asesor, '$campo', '$old_val', '$new_val', $changed_by)";
    }

    foreach($data['form'] as $key => $info){
        if($data['original'][$key] == $info){
            $result=false;
        }else{
            $result=true;
        }

        if($result){
            switch($key){
                case "nombre":
                case "apellido":
                    $query['update '.$key]="UPDATE Asesores SET Nombre='".utf8_decode($data['form']['nombre'])." ".utf8_decode($data['form']['apellido'])."', ".$data['qNames'][$key]."='".utf8_decode($info)."' WHERE id=".$data['original']['id']; 
                    $query['update Historic_Nombre_'.$key]=queryHistoric($data['original']['id'],"Nombre",utf8_decode($data['original'][$key]),utf8_decode($data['form']['nombre'])." ".utf8_decode($data['form']['apellido']),$changer);
                    $query['update Historic_'.$key]=queryHistoric($data['original']['id'],$data['qNames'][$key],utf8_decode($data['original'][$key]),utf8_decode($info),$changer);
                    break;
                case "profile":
                    $query['update '.$key]="UPDATE userDB SET profile=$info WHERE asesor_id=".$data['original']['id'];  
                    $query['update Historic_'.$key]=queryHistoric($data['original']['id'],$data['qNames'][$key],$data['original'][$key],$info,$changer);
                    break;
                case "nombre_corto":
                    $username=strtolower(str_replace(" ",".",$info));
                    $username_old=strtolower(str_replace(" ",".",$data['original'][$key]));
                    $query['update '.$key]="UPDATE Asesores SET `N Corto`='$info', Usuario='$username' WHERE id=".$data['original']['id'];
                    $query['update usuario']="UPDATE userDB SET username='$username' WHERE asesor_id=".$data['original']['id'];
                    $query['update Historic_'.$key]=queryHistoric($data['original']['id'],$data['qNames'][$key],$data['original'][$key],$info,$changer);
                    $query['update Historic_Usuario']=queryHistoric($data['original']['id'],"Usuario",$username_old,$username,$changer);
                    $query['update Historic_username']=queryHistoric($data['original']['id'],"username",$username_old,$username,$changer);
                    break;
                default:
                    $query['update '.$key]="UPDATE Asesores SET ".$data['qNames'][$key]."='".utf8_decode($info)."' WHERE id=".$data['original']['id']; 
                    $query['update Historic_'.$key]=queryHistoric($data['original']['id'],$data['qNames'][$key],utf8_decode($data['original'][$key]),utf8_decode($info),$changer);
                    break;
            }

        }

    }



    $td['status']=1;

    if(isset($query)){
        $connectdb=Connection::mysqliDB('Test');
        foreach($query as $index => $info){
            if(!$result=$connectdb->query($info)){
                $td['status']=0;
                $td['errors'][$index]=utf8_encode("ERROR -> ".$connectdb->error." ON $index");
            }
        }

        $connectdb->close();
    }



    echo json_encode($td, JSON_PRETTY_PRINT);
},$tkFlag);
