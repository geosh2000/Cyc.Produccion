<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require( APPPATH.'/libraries/REST_Controller.php');

class Queuemetrics extends REST_Controller {

  public function __construct(){

    parent::__construct();
    $this->load->helper('json_utilities');
    $this->load->helper('validators');
    $this->load->helper('jwt');
    $this->load->database();
  }

  public function rtMonitor_post(){

    $result = validateToken( $_GET['token'], $_GET['usn'], $func = function(){

      $block = $this->post();

      $this->db->select('tipo, json')
              ->select('Last_update')
              ->from('ccexporter.rtMonitor', false)
              ->where_in( 'tipo', $block );

      if( $q = $this->db->get() ){

        okResponse( $block." obtenido", 'data', $q->result_object(), $this );

      }else{

        errResponse('Error en la base de datos', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());

      }

      return true;

    });

    $this->response( $result );

  }

  public function queues_post(){

    $result = validateToken( $_GET['token'], $_GET['usn'], $func = function(){

      $this->db->select('Skill, Cola, queue, Departamento, monShow, direction')
              ->from('Cola_Skill a')
              ->join('PCRCs b', 'a.monShow = b.id', 'left')
              ->where('active', '1')
              ->order_by('Departamento');


      if( $q = $this->db->get() ){

        okResponse( "Colas obtenidas", 'data', $q->result_object(), $this );

      }else{

        errResponse('Error en la base de datos', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());

      }

      return true;

    });

    $this->response( $result );

  }

  public function asesorDep_post(){

    $result = validateToken( $_GET['token'], $_GET['usn'], $func = function(){

      $this->db->select('asesor, dep, Departamento, color')
              ->select('NOMBREASESOR(asesor,1) as name', FALSE)
              ->from('dep_asesores a')
              ->join('PCRCs b', 'a.dep = b.id', 'left')
              ->where('Fecha = ', 'CURDATE()', FALSE)
              ->where('vacante IS NOT ', 'NULL', FALSE);


      if( $q = $this->db->get() ){

        okResponse( "Deps asesores obtenidos", 'data', $q->result_array(), $this );

      }else{

        errResponse('Error en la base de datos', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());

      }

      return true;

    });

    $this->response( $result );

  }

  public function pauseMon_get(){

    $result = validateToken( $_GET['token'], $_GET['usn'], $func = function(){

      $date = $this->uri->segment(3);
      segmentSet(  3, "Debe incluir una fecha", $this );
      segmentType( 3, "Debe incluir una fecha en formato YYYY-MM-DD", $this, $type = 'date' );

      $this->db->select('a.*,
                        NOMBREASESOR(a.asesor, 2) AS Nombre,
                        NOMBREDEP(dep) AS Departamento,
                        Pausa,
                        TIME_TO_SEC(Duracion) AS dur_seconds', FALSE)
              ->from('asesores_pausas a')
              ->join('dep_asesores b', 'a.asesor = b.asesor', 'left')
              ->join('Tipos_pausas c', 'a.tipo = c.pausa_id', 'left')
              ->where('Inicio >=', $date);


      if( $q = $this->db->get() ){

        okResponse( "Pausas obtenidas", 'data', $q->result_array(), $this );

      }else{

        errResponse('Error en la base de datos', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());

      }

      return true;

    });

    $this->response( $result );

  }


}