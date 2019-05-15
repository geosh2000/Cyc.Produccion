<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require( APPPATH.'/libraries/REST_Controller.php');

class RtMonitor extends REST_Controller {

  public function __construct(){

    parent::__construct();
    $this->load->helper('json_utilities');
    $this->load->helper('validators');
    $this->load->helper('jwt');
    $this->load->database();
  }


    public function rtCalls_put(){
        
        $data = $this->put();
        
        $result = array();
        
        foreach($data['values'] as $index => $info){
            $tmp = array();
            foreach($info as $i => $x){
                if( $x == "&nbsp;" ){
                    $datos = null; 
                }else{
                    $datos = $x;  
                }
                
                $tmp[$data['fields'][$i]] = $datos;
            }
            array_push( $result, $tmp );
        } 
        
        if( $this->db->insert_batch('ccexporter.qm_rtCalls', $result) ){
            okResponse( 'Data', 'data', $data['fields'], $this );
        }else{
            errResponse('Error en la base de datos', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
        }
            
        
    }
    
    public function rtMonitor_put(){

    $result = validateToken( $_GET['token'], $_GET['usn'], $func = function(){ 
        
      $params = $this->put();
        
      $blocks = array(
            "RealTimeDO.RTRiassunto",
            "RealTimeDO.RTCallsBeingProc",
            "RealtimeDO.RTAgentsLoggedIn",
            "RealTimeDO.WallRiassunto",
            "RealTimeDO.WallCallsBeingProc",
            "RealTimeDO.VisitorCallsProc",
            "RealTimeDO.VisitorTodaysOk",
            "RealTimeDO.VisitorTodaysKo",
            "RealTimeDO.RtLiveQueues",
            "RealTimeDO.RtLiveCalls",
            "RealTimeDO.RtLiveAgents",
            "RealTimeDO.RtLIveStatus",
            "RealTimeDO.RtAgentsRaw",
            "RealtimeDO.RtCallsRaw",
            "Agents"
        );

      $this->db->select('json')
              ->select('LOWER(tipo) as tipo', FALSE)
              ->select('Last_update')
              ->from('ccexporter.rtMonitor', false)
              ->where_in( 'tipo', $blocks );

      if( $q = $this->db->get() ){
          
          $result = $q->result_array();
          
          foreach($result as $index => $info){
              $json = json_decode(str_replace("&nbsp;", "", str_replace("'", '"', utf8_decode(str_replace("u'", "'", $info['json'])))));
              $arr[strtolower($info['tipo'])] = $json;
          }
          
          $agents = array();
          
          foreach($arr['agents'] as $i => $agent){
              $info = array(
                            "name"  => trim(preg_replace("/(?: \([0-9]*\))+/", "", $agent->descr_agente)),
                            "ext"   => trim(preg_replace("/(?:^agent\/)+/", "", $agent->nome_agente))
                            );
              $agents[$agent->nome_agente] = $info;
          }
          
          unset($arr['agents']);
          
          $bl = array();
          
          foreach($arr as $tipo => $info){
              $in = "";
              foreach($info as $x => $y){
                  if( strtolower($x) == $tipo ){
                      $bl[$tipo] = $y;
                  }
              }
              
          }
          
          $agProc = $this->buildAgents( $bl['realtimedo.rtagentsraw'] );
          foreach($agProc as $ag => $info){
              $qs = explode(":",$info['queue']);

              foreach( $qs as $k => $q ){
                  if( in_array($q, $params['queues']) ){
                      if( isset($agents[$ag]) ){
                        $agentsOK[$ag] = array_merge( $agProc[$ag], $agents[$ag] ); 
                      }  
                  }
              }
              
          }
          
          $calls = $this->buildCalls( $bl['realtimedo.rtcallsraw'] );
          foreach( $calls['calls'] as $ag => $info){
              if( $ag != 'waits' ){
                unset($info['agent']);
                if( isset($agentsOK[$ag]) ){
                    $agentsOK[$ag] = array_merge ( $agentsOK[$ag], $info );
                }

              }
          }
          
          
          
          $final = array( 'data' => $agentsOK, 'waits' => $calls['calls']['waits'] );
          

        okResponse( "Data obtenida", 'data', $final, $this, 'params', $params );

      }else{

        errResponse('Error en la base de datos', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());

      }

      return true;

    });

    $this->response( $result );

  }
    
    private function buildCalls( $data ){
        
        $fields = $data[0];
        unset($data[0]);
        
        $f['agent'] = array_search('RT_agent', $fields);
        $f['queue'] = array_search('RT_queue', $fields);
        $f['answered'] = array_search('RT_answered', $fields);
        $f['caller'] = array_search('RT_caller', $fields);
        $f['entered'] = array_search('RT_entered', $fields);
        $f['dnis'] = array_search('RT_dnis', $fields);
        
        $calls = array();
        $callers = array();
        $calls['waits'] = array();
        
        foreach($data as $index => $info){
            $tmp = array();
            foreach($f as $key => $k){
                $tmp["c_".$key] = $info[$k];
            }
            
            // Caller info (delete repeated)
            if( isset($callers[$tmp['c_caller']]) ){
                if( $callers[$tmp['c_caller']]['c_entered'] < $tmp['c_entered'] ){
                    $callers[$tmp['c_caller']] = $tmp;
                }
            }else{
                $callers[$tmp['c_caller']] = $tmp;
            }
            
        }
        
        foreach($data as $index => $info){
            $tmp = array();
            foreach($f as $key => $k){
                $tmp["c_".$key] = $info[$k];
            }
            
            
            // Agent info (delete repeated)
            if( $tmp['c_agent'] == '' ){
                array_push($calls['waits'], $tmp);
            }else{
                if( isset($calls[$info[$f['agent']]]) ){
                    if( $tmp['c_agent'] == $callers[$tmp['c_caller']]['c_agent'] ){
                        if( $calls[$info[$f['agent']]]['c_answered'] < $tmp['c_answered'] ){
                            $calls[$info[$f['agent']]] = $tmp;
                        }
                    }
                }else{
                    if( $tmp['c_agent'] == $callers[$tmp['c_caller']]['c_agent'] ){
                        $calls[$info[$f['agent']]] = $tmp;
                    }
                }
            }
            
            
        }
        
        return array( 'calls' => $calls, 'callers' => $callers);
    }
    
    private function buildAgents( $data ){
        
        $fields = $data[0];
        unset($data[0]);
        
        $agents = array();

        
        foreach($data as $index => $info){
            $tmp = array();
            foreach($info as $k => $d){
                $tmp[str_replace("ACB_", "", $fields[$k])] = $d;
            }
            $agents[$tmp['agent']] = $tmp;
        }
        
        return $agents;
    }
    
   
}
