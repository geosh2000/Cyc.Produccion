<?php
//defined('BASEPATH') OR exit('No direct script access allowed');
require( APPPATH.'/libraries/REST_Controller.php');
// use REST_Controller;


class Asistencia extends REST_Controller {

  public function __construct(){

    parent::__construct();
    $this->load->helper('json_utilities');
    $this->load->helper('jwt');
    $this->load->helper('validators');
    $this->load->database();

  }

  public function pya_get(){

    $result = validateToken( $_GET['token'], $_GET['usn'], $func = function(){

      $dep=$this->uri->segment(3);
      $inicio=$this->uri->segment(4);
      $fin=$this->uri->segment(5);

      $this->db->query("SET @inicio = CAST('$inicio' as DATE)");
      $this->db->query("SET @fin = CAST('$fin' as DATE)");
      $this->db->query("SET @dep = $dep");

      $this->db->query("DROP TEMPORARY TABLE IF EXISTS asistenciaAsesores");
      $this->db->query("CREATE TEMPORARY TABLE asistenciaAsesores SELECT
          a.*,
          IF(vacante IS NOT NULL, NOMBREDEP(dep), NULL) as Departamento,
          IF(vacante IS NOT NULL, NOMBREPUESTO(a.puesto), NULL) as PuestoName,
          esquema
      FROM
          dep_asesores a LEFT JOIN Asesores b ON a.asesor=b.id
      WHERE
          Fecha BETWEEN @inicio AND @fin AND vacante IS NOT NULL
              AND IF(@dep=0, dep != 29, dep = @dep)");
      $this->db->query("ALTER TABLE asistenciaAsesores ADD PRIMARY KEY (`Fecha`, `asesor`)");

      $this->db->query("DROP TEMPORARY TABLE IF EXISTS log_asesor");
      $this->db->query("CREATE TEMPORARY TABLE log_asesor (SELECT
          a.*,
      	b.id as h_id,
          js, je, x1s, x1e, x2s, x2e, cs, ce,
          checkLog(a.Fecha, a.asesor, 'in') AS login,
          checkLog(a.Fecha, a.asesor, 'out') AS logout
      FROM
          asistenciaAsesores a
              LEFT JOIN
          asesores_programacion b ON a.asesor = b.asesor AND a.Fecha = b.Fecha)");

      $this->db->query("DROP TEMPORARY TABLE IF EXISTS xtraTime");
      $this->db->query("CREATE TEMPORARY TABLE xtraTime SELECT
      	a.Fecha, a.asesor,
      	IF(login IS NOT NULL,
          IF(login<=js AND logout>js,
            js,
            login
          ),
          NULL) as j_login,
        IF(logout IS NOT NULL,
          IF(logout>je AND login<=je,
            je,
            logout),
          NULL) as j_logout,

      	IF(x1s!=x1e,IF(login<x1e AND logout>=x1s,IF(login<x1s,x1s,login),NULL),NULL) as x1_login,
		IF(x1s!=x1e,IF(login<x1e AND logout>=x1s,IF(logout>x1e,x1e,logout),NULL),NULL) as x1_logout,
      	IF(x2s!=x2e,IF(login<x2e AND logout>=x2s,IF(login<x2s,x2s,login),NULL),NULL) as x2_login,
      	IF(x2s!=x2e,IF(login<x2e AND logout>=x2s,IF(logout>x2e,x2e,logout),NULL),NULL) as x2_logout
      FROM
      	log_asesor a");



      $this->db->query("DROP TEMPORARY TABLE IF EXISTS ausTable");
      $this->db->query("CREATE TEMPORARY TABLE ausTable SELECT
              Fecha,
                  a.asesor,
                  IF(js = je, 1, 0) AS Descanso,
                  CASE
                      WHEN login IS NULL THEN 0
                      WHEN login IS NOT NULL THEN 1
                  END AS Asistencia,

                  CASE
                      WHEN tipo_ausentismo IS NULL THEN 0
                      ELSE 1
                  END AS Ausentismo,
                  Caso as Aus_Caso,
                  Comments as Aus_Nota,
                  User as Aus_register,
                  `Last Update` as Aus_LU, c.Ausentismo as Aus_Nombre,
                  CASE
                      WHEN
                          tipo_ausentismo IS NOT NULL
                      THEN
                          CASE
                              WHEN Descansos = 0 THEN c.Code
                              WHEN
                                  Descansos != 0
                              THEN
                                  CASE
                                      WHEN DATEDIFF(fin, inicio) < 5 THEN IF(fin = Fecha OR inicio = Fecha, 'D', c.Code)
                                      ELSE CASE
                                          WHEN
                                              esquema = 10
                                          THEN
                                              IF(WEEKDAY(Fecha) + 1 IN (6 , 7)
                                                  AND (FLOOR((DAYOFYEAR(Fecha) - DAYOFYEAR(inicio)) / 7)) < Descansos, 'D', c.Code)
                                          ELSE IF(WEEKDAY(Fecha) + 1 = 7
                                              AND (FLOOR((DAYOFYEAR(Fecha) - DAYOFYEAR(inicio)) / 7)) < Descansos, 'D', IF((FLOOR(((DAYOFYEAR(Fecha) - DAYOFYEAR(inicio)) - (FLOOR((DAYOFYEAR(Fecha) - DAYOFYEAR(inicio)) / 7)) - FLOOR((DAYOFYEAR(Fecha) - DAYOFYEAR(inicio)) - (FLOOR((DAYOFYEAR(Fecha) - DAYOFYEAR(inicio)) / 7)) / 5)) / 5)) < Beneficios
                                              AND WEEKDAY(Fecha) + 1 = 6, 'B', IF(Fecha = fin
                                              AND Descansos - ((DAYOFYEAR(Fecha) - DAYOFYEAR(inicio)) - (FLOOR((DAYOFYEAR(Fecha) - DAYOFYEAR(inicio)) / 7)) - FLOOR((DAYOFYEAR(Fecha) - DAYOFYEAR(inicio)) - (FLOOR((DAYOFYEAR(Fecha) - DAYOFYEAR(inicio)) / 7)) / 5)) > 0, 'D', c.Code)))
                                      END
                                  END
                          END
                  END AS Code_aus,
                  IF(WEEKDAY(Fecha) + 1 = 7, 1, 0) AS Domingo
          FROM
              log_asesor a
          LEFT JOIN Ausentismos b ON a.asesor = b.asesor
              AND Fecha BETWEEN inicio AND fin
          LEFT JOIN `Tipos Ausentismos` c ON b.tipo_ausentismo = c.id");

      $this->db->query("ALTER TABLE log_asesor ADD PRIMARY KEY (`Fecha`, `asesor`)");
      $this->db->query("ALTER TABLE xtraTime ADD PRIMARY KEY (`Fecha`, `asesor`)");
      $this->db->query("ALTER TABLE ausTable ADD PRIMARY KEY (`Fecha`, `asesor`)");

      $this->db->query("DROP TEMPORARY TABLE IF EXISTS pyaTable");
      $this->db->query("CREATE TEMPORARY TABLE pyaTable SELECT
              horario_id,
                  tipo,
                  caso,
                  Nota,
                  `Last Update` as Last_Update,
                  changed_by as reg_by,
                  Excepcion,
                  Codigo
          FROM
              PyA_Exceptions a
          LEFT JOIN `Tipos Excepciones` b ON a.tipo = b.exc_type_id");
      $this->db->query("ALTER TABLE pyaTable ADD PRIMARY KEY (`horario_id`)");


      $this->db->query("DROP TEMPORARY TABLE IF EXISTS asistenciaTableResult");
      $this->db->query("CREATE TEMPORARY TABLE asistenciaTableResult SELECT
      	NOMBREASESOR(a.asesor,2) as Nombre,
          a . *,
          j_login,
          j_logout,
          x1_login,
          x1_logout,
          x2_login,
          x2_logout,
          IF(Descanso=0 AND j_logout<je,1,0) as SalidaAnticipada,
          IF(Descanso=0,TIMESTAMPDIFF(SECOND,j_login,j_logout)/TIMESTAMPDIFF(SECOND,js,je)*100,null) as tiempoLaborado,
          CASE
              WHEN j_login > ADDTIME(js, '00:13:00') THEN 'RT-B'
              WHEN j_login >= ADDTIME(js, '00:01:00') THEN 'RT-A'
              ELSE NULL
          END as Retardo,
          CASE
              WHEN j_login >= ADDTIME(js, '00:01:00') THEN ADDTIME(j_login, - js)
              ELSE NULL
          END as Retardo_time,
          d.tipo as RT_tipo,
          d.caso as RT_caso,
          d.Nota as RT_Nota,
          d.Last_Update as RT_LU,
          NOMBREUSUARIO(d.reg_by,1) as RT_register,
          d.Excepcion as RT_Excepcion,
          d.Codigo as RT_Codigo,
          Descanso,
          Asistencia,
          Ausentismo,
          Code_aus,
          Aus_caso, Aus_Nota, NOMBREUSUARIO(Aus_register,1) as Aus_Register, Aus_LU, Aus_Nombre,
          Domingo
      FROM
          log_asesor a
              LEFT JOIN
          xtraTime b ON a.Fecha = b.Fecha
              AND a.asesor = b.asesor
              LEFT JOIN
          ausTable c ON a.Fecha = c.Fecha
              AND a.asesor = c.asesor
              LEFT JOIN
          pyaTable d ON h_id = horario_id
      ORDER BY
          Nombre");

          $q = $this->db->query("SELECT * FROM asistenciaTableResult");

      $result = $q->result_array();

      foreach($result as $index => $info){
        $fechas[$info['Fecha']]=1;
        $data[$info['asesor']]['Nombre']=$info['Nombre'];
        $data[$info['asesor']]['PuestoName']=$info['PuestoName'];
        $data[$info['asesor']]['Departamento']=$info['Departamento'];
        $data[$info['asesor']]['data'][$info['Fecha']]=$info;
        unset($data[$info['asesor']]['data'][$info['Fecha']]['asesor']);
        unset($data[$info['asesor']]['data'][$info['Fecha']]['Nombre']);
        unset($data[$info['asesor']]['data'][$info['Fecha']]['PuestoName']);
        unset($data[$info['asesor']]['data'][$info['Fecha']]['Departamento']);
        unset($data[$info['asesor']]['data'][$info['Fecha']]['Fecha']);
      }

      return array('Fechas' => $fechas, 'data' => $data);

    });

    $this->response($result);


  }

  public function ausPorAsesor_get(){

    $result = validateToken( $_GET['token'], $_GET['usn'], $func = function(){

      $asesor   = $this->uri->segment(3);
      $fecha    = $this->uri->segment(4);

      $ausentismo = $this->db->query("SELECT
                            *, DATEDIFF(Fin, Inicio)+1 - Descansos - Beneficios as dias, motivo
                        FROM
                            Ausentismos a
                              LEFT JOIN
                            `Dias Pendientes Redimidos` b ON a.ausent_id = b.id_ausentismo
                        WHERE
                            asesor = $asesor
                                AND '$fecha' BETWEEN Inicio AND Fin");

      $result = $ausentismo->row();

      if($result == NULL){
        $result = 0;
      }

      return $result;

    });

    $this->response($result);


  }

  public function tipos_get(){

    $result = validateToken( $_GET['token'], $_GET['usn'], $func = function(){

      $asesor   = $this->uri->segment(3);

      $ausentismos = $this->db->query("SELECT
                            id, Ausentismo, max_days as dias
                        FROM
                            `Tipos Ausentismos`
                        ORDER BY
                            Ausentismo");

      $result = $ausentismos->result_array();

      $pendientes = $this->diasPendientes($asesor);

      return array("tipos" => $result, "pending" => $pendientes);

    });

    $this->response($result);

  }

  public function diasPendientes( $asesor ){

    $query = $this->db->query("SELECT
                          a.id as asesor,
                          a.motivo,
                          assign,
                          IF(redim IS NULL, 0, redim) as redim,
                          assign - IF(redim IS NULL, 0, redim) as available
                      FROM
                          (SELECT
                              id, sum(`dias asignados`) as assign, motivo
                          FROM
                              `Dias Pendientes`
                          GROUP BY id , motivo) a
                              LEFT JOIN
                          (SELECT
                              id, sum(dias) as redim, motivo
                          FROM
                              `Dias Pendientes Redimidos`
                          GROUP BY id , motivo) b ON a.motivo = b.motivo AND a.id = b.id
                      WHERE a.id=$asesor HAVING available>0 ORDER BY a.motivo");

      return $query->result_array();

  }

  public function setAusentismo_put(){

    $data = $this->put();
    $this->load->library('form_validation');
    $this->form_validation->set_data( $data );

    if( $this->form_validation->run( 'ausentismo_put' ) ){
      $this->response( $data );
    }else{
      errResponse( "Existen errores en el formulario", REST_Controller::HTTP_BAD_REQUEST, $this, 'errores', $this->form_validation->get_errores_arreglo() );
    }



    // $this->response( $data );

  }

  public function calendario_get(){

    $result = validateToken( $_GET['token'], $_GET['usn'], $func = function(){

      $inicio = $this->uri->segment(3);
      $fin    = $this->uri->segment(4);
      $skill  = $this->uri->segment(5);

      segmentSet( 3, 'El segmento 3 debe incluir la fecha de inicio', $this );
      segmentSet( 4, 'El segmento 4 debe incluir la fecha final', $this );
      segmentSet( 5, 'El segmento 5 debe incluir el skill que se busca', $this );
      segmentType( 3, 'El formato debe ser de tipo fecha YYYY-MM-DD', $this, 'date' );
      segmentType( 4, 'El formato debe ser de tipo fecha YYYY-MM-DD', $this, 'date' );
      segmentType( 5, 'El formato debe ser el id del skill', $this );

      $this->db->query("SET @inicio = CAST('$inicio' as DATE)");
      $this->db->query("SET @fin = CAST('$fin' as DATE)");
      $this->db->query("SET @skill = $skill");

      $this->db->query("DROP TEMPORARY TABLE IF EXISTS aus_registry");

      $this->db->select("a.*,
                        Code,
                        Ausentismo,
                        showcal, d.clave")
              ->select(" NOMBREASESOR(a.asesor, 1) AS nombre ", FALSE)
              ->from("Ausentismos a")
              ->join("dep_asesores b", "a.asesor = b.asesor AND a.Inicio = b.Fecha", 'left')
              ->join("`Tipos Ausentismos` c ", "a.tipo_ausentismo = c.id", 'left', FALSE)
              ->join("hc_codigos_Puesto d", "b.hc_puesto = d.id", 'left')
              ->where("Fin >= @inicio
                        AND Inicio <= @fin
                        AND vacante IS NOT NULL
                        AND dep = @skill

                        AND showcal = 1", NULL, FALSE);

      $ausReg = $this->db->get_compiled_select();

      $this->db->query("CREATE TEMPORARY TABLE aus_registry $ausReg");

      if( $aus = $this->db->query("SELECT * FROM aus_registry") ){

        $this->db->select("a.Fecha,
                            espacios,
                            abierto")
                  ->select("COUNT(IF(ausent_id IS NOT NULL AND clave LIKE '%d%', ausent_id, NULL)) AS asignados,
                            espacios - COUNT(IF(ausent_id IS NOT NULL AND clave LIKE '%d%', ausent_id, NULL)) AS disponibles", FALSE)
                  ->from("ausentismos_calendario a")
                  ->join("aus_registry b", "a.Fecha BETWEEN Inicio AND Fin", "left")
                  ->where("Departamento = @skill AND Fecha BETWEEN @inicio AND @fin", NULL, FALSE)
                  ->group_by("Fecha");

        if( $q = $this->db->get() ){
          okResponse( 'Ausentismos Obtenidos', 'data', array('aus' => $aus->result_array(), 'q' => $q->result_array() ), $this );
        }else{
          errResponse( "Error en la base de datos Cantidades", REST_Controller::HTTP_BAD_REQUEST, $this, 'errores', $this->db->error() );
        }

      }else{
        errResponse( "Error en la base de datos Ausentismos", REST_Controller::HTTP_BAD_REQUEST, $this, 'errores', $this->db->error(), 'query', $ausReg );
      }

      return true;

    });

    $this->response( $result );

  }
}