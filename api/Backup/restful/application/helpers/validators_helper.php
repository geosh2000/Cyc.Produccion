<?php

function segmentSet( $segment, $msg, $class ){

  $segVal = $class->uri->segment($segment);

  if( !isset($segVal) ){
    $respuesta = array(
                      'ERR'       => TRUE,
                      'msg'       => $msg,
                      'segmento'  => $segment);

    $class->response( $respuesta, REST_Controller::HTTP_BAD_REQUEST );
    return;
  }

}

function segmentType( $segment, $msg, $class, $type = 'numeric' ){

  $segVal = $class->uri->segment($segment);

  switch($type){
    case 'numeric':
      if( !is_numeric($segVal) ){
        $respuesta = array(
                          'ERR'       => TRUE,
                          'msg'       => $msg,
                          'segmento'  => $segment);

        $class->response( $respuesta, REST_Controller::HTTP_BAD_REQUEST );
        return;
      }
      break;
    case 'date':
      preg_match('/^(([1][9])|([2][01]))[0-9]{2}[-\/](([0][1-9])|([1][0-2]))[-\/](([0][1-9])|([1-2][0-9])|([3][0-1]))$/', $segVal, $matches, PREG_OFFSET_CAPTURE);

      if($matches == null){
        $respuesta = array(
          'ERR'       => TRUE,
          'msg'       => $msg,
          'segmento'  => $segment);
          $class->response( $respuesta, REST_Controller::HTTP_BAD_REQUEST );
      }

      return;

      break;
  }



}

function errResponse( $msg, $status, $class, $addData = 'Data Adicional', $data = null ){

  $respuesta = array(
                    'ERR'     => TRUE,
                    'msg'     => $msg,
                    $addData  => $data );

  $class->response( $respuesta, $status );

}

function okResponse( $msg, $title, $result, $class ){

  $respuesta = array(
                    'ERR'     => FALSE,
                    'msg'     => $msg,
                    $title    => $result);

  $class->response( $respuesta );

}

?>
