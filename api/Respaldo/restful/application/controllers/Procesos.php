<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require( APPPATH.'/libraries/REST_Controller.php');

class Procesos extends REST_Controller {

  public function __construct(){

    parent::__construct();
    $this->load->helper('json_utilities');
    $this->load->helper('validators');
    $this->load->helper('jwt');
    $this->load->database();
  }

  public function grafAsesores_get(){

    $result = validateToken( $_GET['token'], $_GET['usn'], $func = function(){

        $id = $this->uri->segment(3);

        if( !$this->db->query("SET @inicio = CAST(CONCAT(YEAR(CURDATE()), '-', MONTH(CURDATE()), '-01') AS DATE)") ){
          errResponse('Error en la base de datos', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
        }
        if( !$this->db->query("SET @fin = ADDDATE(CURDATE(), - 1)") ){
          errResponse('Error en la base de datos', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
        }

        $this->db->select("Fecha, asesor, dep")
                  ->select("IF(dep IN (35,29,5),35,dep) as Skill", FALSE)
                  ->from("dep_asesores")
                  ->where("vacante IS NOT NULL", NULL, FALSE)
                  ->where("Fecha BETWEEN @inicio AND CURDATE()", NULL, FALSE);

        if( !$g_asesores = $this->db->get_compiled_select() ){
          errResponse('Error en la base de datos', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
        }

        if( !$this->db->query("DROP TEMPORARY TABLE IF EXISTS g_asesores") ){
          errResponse('Error en la base de datos', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
        }
        if( !$this->db->query("CREATE TEMPORARY TABLE g_asesores $g_asesores") ){
          errResponse('Error en la base de datos', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
        }
        if( !$this->db->query("ALTER TABLE g_asesores  ADD PRIMARY KEY(Fecha, asesor)") ){
          errResponse('Error en la base de datos', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
        }

        if( !$this->db->query("DROP TEMPORARY TABLE IF EXISTS locBalance") ){
          errResponse('Error en la base de datos', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
        }
        $this->db->select("b.*,
                            Localizador,
                            SUM(VentaMXN + OtrosIngresosMXN + EgresosMXN) AS Balance,
                            IF(SUM(VentaMXN) > 0
                                      AND SUM(VentaMXN + OtrosIngresosMXN + EgresosMXN) > 0
                                      AND gpoTipoRsva = 'In',
                                  Localizador,
                                  NULL) AS NewLocIn,
                          	IF(SUM(VentaMXN) > 0
                                      AND SUM(VentaMXN + OtrosIngresosMXN + EgresosMXN) > 0
                                      AND gpoTipoRsva != 'In',
                                  Localizador,
                                  NULL) AS NewLocElse,", FALSE)
                  ->from("t_Locs a")
                  ->join("g_asesores b", "a.asesor = b.asesor AND a.Fecha = b.Fecha", "left")
                  // ->join("config_tipoRsva c", "IF(a.asesor!=-1 AND b.dep IN (35,5,29), b.dep, 0)=c.dep AND IF(ISNULL(a.tipo)=1 OR a.tipo='',0,a.tipo)=c.tipo", "left", FALSE)
                  ->join("config_tipoRsva c", "IF(a.asesor!=-1, b.dep, 0)=c.dep AND IF(ISNULL(a.tipo)=1 OR a.tipo='',0,a.tipo)=c.tipo", "left", FALSE)
                  ->where("a.Fecha BETWEEN @inicio AND CURDATE()", NULL, FALSE)
                  ->group_by( array("a.Fecha", "Localizador") )
                  ->having("asesor IS NOT NULL", NULL, FALSE);

        if( !$locBalance = $this->db->get_compiled_select() ){
          errResponse('Error en la base de datos locs', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
        }
        if( !$this->db->query("CREATE TEMPORARY TABLE locBalance $locBalance") ){
          errResponse('Error en la base de datos', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $locBalance);
        }
        if( !$this->db->query("ALTER TABLE locBalance  ADD PRIMARY KEY(Fecha, Localizador)") ){
          errResponse('Error en la base de datos 69', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
        }

        if( !$this->db->query("DROP TEMPORARY TABLE IF EXISTS callsAsesor") ){
          errResponse('Error en la base de datos 73', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
        }
        $this->db->select("a.*, b.dep, b.Skill as aSkill, c.Skill", FALSE)
                ->from("t_Answered_Calls a")
                ->join("g_asesores b", "a.asesor = b.asesor AND a.Fecha = b.Fecha", "left")
                ->join("Cola_Skill c", "a.Cola = c.Cola", "left")
                ->where("a.Fecha BETWEEN @inicio AND @fin", NULL, FALSE)
                ->having("!(Desconexion = 'Transferida' AND TIME_TO_SEC(Duracion_Real) <= 120)", NULL, FALSE)
                ->having("aSkill = c.Skill", NULL, FALSE);
        if( !$callAsesorINSIDE = $this->db->get_compiled_select() ){
          errResponse('Error en la base de datos 82', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
        }

        $this->db->select("Fecha, asesor, COUNT(*) AS llamadas", FALSE)
                  ->from("($callAsesorINSIDE) a ", FALSE)
                  ->group_by( array('a.Fecha', 'a.asesor') );

        if( !$callAsesor = $this->db->get_compiled_select() ){
          errResponse('Error en la base de datos calls', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
        }
        if( !$this->db->query("CREATE TEMPORARY TABLE callsAsesor $callAsesor") ){
          errResponse('Error en la base de datos 93', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
        }
        if( !$this->db->query("ALTER TABLE callsAsesor ADD PRIMARY KEY(Fecha, asesor(10))") ){
          errResponse('Error en la base de datos 96', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
        }

        if( !$this->db->query("DROP TEMPORARY TABLE IF EXISTS ausAsesor") ){
          errResponse('Error en la base de datos 100', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
        }
        $this->db->select("a.Fecha, a.asesor, IF(js != je AND ausent_id IS NULL, 1, 0) as Ausentismo", FALSE)
                  ->from("asesores_programacion a")
                  ->join("g_asesores b", "a.Fecha = b.Fecha AND a.asesor = b.asesor", "left")
                  ->join("Ausentismos c", "a.Fecha BETWEEN Inicio AND Fin AND a.asesor=c.asesor", "left")
                  ->where("a.Fecha BETWEEN @inicio AND @fin AND b.asesor IS NOT NULL", NULL, FALSE);

        if( !$ausAsesor = $this->db->get_compiled_select() ){
          errResponse('Error en la base de datos 109', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
        }
        if( !$this->db->query("CREATE TEMPORARY TABLE ausAsesor $ausAsesor") ){
          errResponse('Error en la base de datos 112', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
        }
        if( !$this->db->query("ALTER TABLE ausAsesor  ADD PRIMARY KEY(Fecha, asesor)") ){
          errResponse('Error en la base de datos 115', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
        }

        if( !$this->db->query("DROP TEMPORARY TABLE IF EXISTS locsAsesor") ){
          errResponse('Error en la base de datos 119', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
        }
        $this->db->select("b.Fecha,
                            b.asesor,
                            COUNT(DISTINCT NewLocIn) AS LocsIn,
                            COUNT(DISTINCT NewLocElse) AS LocsElse,
                            SUM(IF(!ISNULL(NewLocIn), VentaMXN + OtrosIngresosMXN + EgresosMXN,0)) AS MontoIn,
                            SUM(IF(!ISNULL(NewLocElse), VentaMXN + OtrosIngresosMXN + EgresosMXN,0)) AS MontoElse", FALSE)
                  ->from("t_Locs a")
                  ->join("locBalance b", "a.Fecha = b.Fecha AND a.Localizador = b.Localizador", "left")
                  ->where("a.Fecha BETWEEN @inicio AND @fin AND b.asesor IS NOT NULL ", NULL, FALSE)
                  ->group_by( array("b.Fecha", "b.asesor") );

        $locsAsesor = $this->db->get_compiled_select();
        $this->db->query("CREATE TEMPORARY TABLE locsAsesor $locsAsesor");
        $this->db->query("ALTER TABLE locsAsesor  ADD PRIMARY KEY(Fecha, asesor)");

        $this->db->query("DELETE FROM graf_ventaDiariaAsesores WHERE Fecha BETWEEN @inicio AND @fin");

        if( $this->db->query("INSERT INTO graf_ventaDiariaAsesores (SELECT
  a.Fecha, a.asesor, IF(llamadas IS NULL,0,llamadas) as llamadas, LocsIn, LocsElse, MontoIn, MontoElse, IF(Ausentismo IS NULL,0,Ausentismo) as Ausentismo, NULL
                                                                  FROM
                                                                    g_asesores a
                                                                  	  LEFT JOIN
                                                                    locsAsesor b ON a.asesor = b.asesor
                                                                  	  AND a.Fecha = b.Fecha
                                                                  	  LEFT JOIN
                                                                    callsAsesor c ON a.Fecha = c.Fecha
                                                                  	  AND a.asesor = c.asesor
                                                                  	  LEFT JOIN
                                                                    ausAsesor d ON a.Fecha = d.Fecha
                                                                  	  AND a.asesor = d.asesor WHERE a.Fecha<CURDATE())") ){

          if( $this->db->query("UPDATE config_reportUpdates SET lastRun = NOW() WHERE id=$id") ){
            okResponse( "Reporte corrió correctamente", 'data', true, $this );
          }else{
            errResponse('Error en la base de datos', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
          }

        }else{

          errResponse('Error en la base de datos', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());

        }

      return true;

    });

    $this->response( $result );

  }

  public function grafAsesoresDaily_get(){

      $this->db->query("SELECT * FROM graf_ventaDiariaAsesores");

      $this->db->query("DROP TEMPORARY TABLE IF EXISTS g_asesores");
      $this->db->query("CREATE TEMPORARY TABLE g_asesores SELECT
                  Fecha, asesor, dep, IF(dep IN (35,29, 5),35,dep) as Skill
              FROM
                  dep_asesores
              WHERE
                  vacante IS NOT NULL
                      AND Fecha = CURDATE()");

      $this->db->query("ALTER TABLE g_asesores  ADD PRIMARY KEY(Fecha, asesor)");

      $this->db->query("DROP TEMPORARY TABLE IF EXISTS locBalance");
      $this->db->query("CREATE TEMPORARY TABLE locBalance SELECT
                  b.*,
                  Localizador,
                  SUM(VentaMXN + OtrosIngresosMXN + EgresosMXN) AS Balance,
                  IF(SUM(VentaMXN) > 0
                            AND SUM(VentaMXN + OtrosIngresosMXN + EgresosMXN) > 0
                            AND gpoTipoRsva = 'In',
                        Localizador,
                        NULL) AS NewLocIn,
                	IF(SUM(VentaMXN) > 0
                            AND SUM(VentaMXN + OtrosIngresosMXN + EgresosMXN) > 0
                            AND gpoTipoRsva != 'In',
                        Localizador,
                        NULL) AS NewLocElse
              FROM
                  d_Locs a
                      LEFT JOIN
                  g_asesores b ON a.asesor = b.asesor
                      AND a.Fecha = b.Fecha
                      LEFT JOIN
                  config_tipoRsva c ON IF(a.asesor != - 1,
                      b.dep,
                      0) = c.dep
                      AND IF(ISNULL(a.tipo)=1 OR a.tipo='',0,a.tipo) = c.tipo
              WHERE
                  a.Fecha = CURDATE()
              GROUP BY a.Fecha , Localizador
              HAVING asesor IS NOT NULL");

      $this->db->query("ALTER TABLE locBalance  ADD PRIMARY KEY(Fecha, Localizador)");

      $this->db->query("DROP TEMPORARY TABLE IF EXISTS tdCalls");
      $this->db->query("CREATE TEMPORARY TABLE tdCalls SELECT
                  CAST(tstStart AS DATE) AS Fecha,
                GETIDASESOR(descr_agente, 2) AS asesor,
                Skill
              FROM
                  ccexporter.callsDetails a
                      LEFT JOIN
                  ccexporter.agentDetails b ON a.agent = b.nome_agente
                  LEFT JOIN
                      Cola_Skill c ON a.queue=c.queue
              WHERE
                  callLen IS NOT NULL AND
                  tstStart >= CAST(CONCAT(CURDATE(),' 00:00:00') as DATE)");

      $this->db->query("ALTER TABLE tdCalls ADD INDEX `asesor` (Fecha ASC, asesor, Skill)");

      $this->db->query("DROP TEMPORARY TABLE IF EXISTS callsAsesor");
      $this->db->query("CREATE TEMPORARY TABLE callsAsesor SELECT
                  a.Fecha, a.asesor, COUNT(*) AS llamadas
              FROM
                  tdCalls a
                      LEFT JOIN
                  g_asesores b ON a.Fecha = b.Fecha
                      AND a.asesor = b.asesor
              WHERE
                  b.Skill = a.Skill
              GROUP BY a.Fecha , a.asesor");

      $this->db->query("ALTER TABLE callsAsesor ADD PRIMARY KEY(Fecha, asesor)");

      $this->db->query("DROP TEMPORARY TABLE IF EXISTS ausAsesor");
      $this->db->query("CREATE TEMPORARY TABLE ausAsesor SELECT
                  a.Fecha, a.asesor, IF(js != je AND ausent_id IS NULL, 1, 0) as Ausentismo
              FROM
                  asesores_programacion a
                      LEFT JOIN
                  g_asesores b ON a.Fecha = b.Fecha
                      AND a.asesor = b.asesor
                      LEFT JOIN
                      Ausentismos c ON a.Fecha BETWEEN Inicio AND Fin AND a.asesor=c.asesor
              WHERE
                  a.Fecha = CURDATE()
                      AND b.asesor IS NOT NULL");

      $this->db->query("ALTER TABLE ausAsesor  ADD PRIMARY KEY(Fecha, asesor)");

      $this->db->query("DROP TEMPORARY TABLE IF EXISTS locsAsesor");
      $this->db->query("CREATE TEMPORARY TABLE locsAsesor SELECT
                  b.Fecha,
                  b.asesor,
                  COUNT(DISTINCT NewLocIn) AS LocsIn,
                  COUNT(DISTINCT NewLocElse) AS LocsElse,
                  SUM(IF(!ISNULL(NewLocIn), VentaMXN + OtrosIngresosMXN + EgresosMXN,0)) AS MontoIn,
                  SUM(IF(!ISNULL(NewLocElse), VentaMXN + OtrosIngresosMXN + EgresosMXN,0)) AS MontoElse
              FROM
                  d_Locs a
                      LEFT JOIN
                  locBalance b ON a.Fecha = b.Fecha
                      AND a.Localizador = b.Localizador
              WHERE
                  a.Fecha = CURDATE()
                      AND b.asesor IS NOT NULL
              GROUP BY b.Fecha , b.asesor");

      $this->db->query("ALTER TABLE locsAsesor  ADD PRIMARY KEY(Fecha, asesor)");

      if( $this->db->query("INSERT INTO graf_ventaDiariaAsesores
              (SELECT * FROM (SELECT
                                  a.Fecha, a.asesor, IF(llamadas IS NULL,0,llamadas) as llamadas, LocsIn, LocsElse, MontoIn, MontoElse, IF(Ausentismo IS NULL,0,Ausentismo) as Ausentismo, NULL
                              FROM
                                  g_asesores a
                                      LEFT JOIN
                                  locsAsesor b ON a.asesor = b.asesor
                                      AND a.Fecha = b.Fecha
                                      LEFT JOIN
                                  callsAsesor c ON a.Fecha = c.Fecha
                                      AND a.asesor = c.asesor
                                      LEFT JOIN
                                  ausAsesor d ON a.Fecha = d.Fecha
                                      AND a.asesor = d.asesor) a)
              ON DUPLICATE KEY UPDATE llamadas=a.llamadas, rsvas=a.LocsIn, rsvas_else=a.LocsElse, monto=a.MontoIn, monto_else=a.MontoElse, ausentismo=a.Ausentismo") ){
            okResponse( "Reporte corrió correctamente!", 'data', true, $this );
          }else{
            $err = $this->db->error();

            errResponse('Error en la base de datos '.$err, REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
          }
  }

  public function xferCheck_get(){

    $result = validateToken( $_GET['token'], $_GET['usn'], $func = function(){

      $this->db->query("SET @inicio = ADDDATE(CURDATE(),-2)");
      $this->db->query("SET @fin = CURDATE()");

      $this->db->query("DROP TEMPORARY TABLE IF EXISTS tmpXfered");
      $this->db->query("CREATE TEMPORARY TABLE tmpXfered SELECT 0 as id, '00:00:00' as Duracion");

      if( $result = $this->db->query("SELECT SUM(Xfered) as found
                                        FROM
                                          (SELECT
                                            xferCheck(ac_id) as xFered
                                          FROM
                                            t_Answered_Calls
                                          WHERE
                                            Fecha BETWEEN @inicio AND @fin AND Answered=1) a") ){
        $foundData = $result->row_array();
        $found = $foundData['found'];

        if( $this->db->query("UPDATE
                                  t_Answered_Calls a
                                LEFT JOIN
                                  tmpXfered b ON a.ac_id = b.id
                                SET
                                  a.Duracion_Real=b.Duracion, Desconexion='Transferida'
                                WHERE
                                  Fecha BETWEEN @inicio AND @fin
                                    AND Answered=1
                                    AND b.id IS NOT NULL") ){

            $this->db->query("UPDATE config_reportUpdates SET lastRun=NOW() WHERE id=2");
            okResponse("$found Xfers aplicados correctamente", 'data', true, $this);
          }else{

            errResponse('Error en la base de datos', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
          }
      }else{

        errResponse('Error en la base de datos', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
      }

    });
  }

  public function uplCalls_get(){

    $name = $this->uri->segment(3);

    $calls = $this->csvProcess("$name.csv");

    foreach($calls['data'] as $index => $info){
      if( $info['Llamante'] != '*' ){


        if( $name == 'ans' ){
          $this->db->set($info)
                    ->set('asesor',$calls['escaped'][$index]['asesor'], FALSE);
          $asesorQ = "asesor = ".$calls['escaped'][$index]['asesor'].", ";
        }else{
          $this->db->set($info);
          $asesorQ = "";
        }

        $query = $this->db->get_compiled_insert("t_Answered_Calls")." ON DUPLICATE KEY UPDATE $asesorQ Duracion = '".$info['Duracion']."', Duracion_Real = '".$info['Duracion_Real']."', Desconexion = '".$info['Desconexion']."', Answered = ".$info['Answered'];

        if( $this->db->query($query) ){
          $resultado['ans'][$index] = array( 'status' => TRUE, 'msg' => '');
          $ansOK++;
        }else{
          $resultado['ans'][$index] = array( 'status' => FALSE, 'msg' => $this->db->error(), 'query' => $query, 'info' => $info );
          $ansERR++;
        }
        // $result[] = array($info, $ans['escaped'][$index]);
      }
    }


    $res = array(
                  'Calls'               => $ans['total'],
                  'UPL_OK'              => $ansOK,
                  'UPL_ERR'             => $ansERR,
                  'data'                => $resultado
                );

    okResponse('Data procesada', 'data', $res, $this);



  }

  private function csvProcess( $fname ){

    $route = $_SERVER['DOCUMENT_ROOT']."/img/calls/";

    if ( ( $gestor = fopen( $route.$fname, "r" ) ) !== FALSE ) {

      $fila = 1;
      $i    = 0;

      while( ( $datos = fgetcsv( $gestor, 1000, ';' ) ) ){

        foreach( $datos as $key => $info ){

          // FIELD NAME ASSING
          if( $i == 0 ){
            $keys[] = $info;
          }else{
            $field = $keys[$key];

            switch($field){
              case 'Fecha':
                $curYear  = date('Y');
                $curMonth = date('n');
                $month    = substr( $info, 0, 2 );

                if( $month == '12' && $curMonth == 1 ){
                  $year = $curYear - 1;
                }else{
                  $year = $curYear;
                }

                $fecha = "$year/".substr( $info, 0, 5 );
                $data[$i-1]['Fecha']  = $fecha;
                $data[$i-1]['Hora']   = substr($info, 7,100);
                break;

              case 'Administradas por':

                if( strpos( $info, '(' ) ){
                  $agent = "IF(GETIDASESOR('".trim( substr( $info, 0, strpos( $info, '(' ) ) )."',2) IS NULL, '".trim( substr( $info, 0, strpos( $info, '(' ) ) )."', GETIDASESOR('".trim( substr( $info, 0, strpos( $info, '(' ) ) )."',2))";
                }else{
                  $agent = "'".trim($info)."'";
                }

                $dataEscape[$i-1]['asesor'] = $agent;
                break;

              case 'Pos.':
                $data[$i-1]['Pos'] = trim($info);
                break;

              case 'Posicion':
                $data[$i-1]['Pos_salida'] = trim($info);
                break;

              case 'Duración':
                $data[$i-1]['Duracion'] = trim($info);
                $data[$i-1]['Duracion_Real'] = trim($info);
                break;

              case 'Código':
                $data[$i-1]['Codigo'] = trim($info);
                break;

              case 'Server':
                $data[$i-1]['Srv'] = trim($info);
                break;

              case 'Asterisk ID':
                $data[$i-1]['AsteriskID'] = trim($info);
                break;

              case 'Eventos de Música en Espera':
                $data[$i-1]['MOH__events'] = trim($info);
                break;

              case 'Duración Música en Espera':
                $data[$i-1]['MOH_duration'] = trim($info);
                break;

              case 'Duración IVR':
                $data[$i-1]['IVR_duration'] = trim($info);
                break;

              case 'Ruta IVR':
                $data[$i-1]['IVR_path'] = trim($info);
                break;

              case 'Desconexión':
                $data[$i-1]['Desconexion'] = trim($info);
                break;

              case 'Transferida para':
              case '':
              case 'Tag':
              case 'Logro':
              case 'Variabiles':
              case 'Agente':
                break;

              default:
                $data[$i-1][$field] = trim($info);
                break;
            }

          }

        }

        // Determine if Answered
        if( isset( $dataEscape[$i-1]['asesor'] ) ){
          $data[$i-1]['Answered']=1;
          $total++;
        }else{
          $data[$i-1]['Answered']=0;
          $total++;
        }

        $i++;
      }

      unset($data['-1']);
      fclose( $gestor );
      unlink($route.$fname);
    }

    return array( 'data' => $data, 'escaped' => $dataEscape, 'total' => $total );
  }

  public function setEndTime_get(){

    if( $this->db->query( "UPDATE t_Answered_Calls SET Hora_Fin = ADDTIME(Hora, ADDTIME(IVR, ADDTIME(Espera, Duracion) ) ) WHERE Last_Update >= CURDATE()" ) ){
      okResponse('Data procesada', 'data', true, $this);
    }else{
      errResponse('Error en la base de datos', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
    }

  }

  public function pauseCheck_get(){

    $this->db->query("DROP TEMPORARY TABLE IF EXISTS deletePauses");
    $this->db->query("CREATE TEMPORARY TABLE deletePauses SELECT * FROM asesores_pausas WHERE CAST(Inicio as DATE)>=ADDDATE(CURDATE(),-1)");
    $this->db->query("SELECT PAUSECALC(asesor, Inicio, Fin) as tmp FROM deletePauses");

    $this->response('OK');
  }

}
