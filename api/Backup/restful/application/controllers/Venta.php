<?php
//defined('BASEPATH') OR exit('No direct script access allowed');
require( APPPATH.'/libraries/REST_Controller.php');
// use REST_Controller;


class Venta extends REST_Controller {

  public function __construct(){

    parent::__construct();
    $this->load->helper('json_utilities');
    $this->load->helper('validators');
    $this->load->helper('jwt');
    $this->load->database();
  }

  public function getVentaPorCanalSV_get(){

    $result = validateToken( $_GET['token'], $_GET['usn'], $func = function(){

      $start = $this->uri->segment(3);
      $end = $this->uri->segment(4);
      $sv = $this->uri->segment(5);
      $type = $this->uri->segment(6);
      $td = $this->uri->segment(7);

      segmentSet( 3, 'Debes ingresar una fecha de inicio', $this );
      segmentSet( 4, 'Debes ingresar una fecha de fin', $this );

      segmentType( 3, "El input debe ser de tipo 'Fecha' en formato YYYY-MM-DD", $this, 'date' );
      segmentType( 4, "El input debe ser de tipo 'Fecha' en formato YYYY-MM-DD", $this, 'date' );

      if($type == 1){
        $t = true;
      }else{
        $t = false;
      }

      if($td == 1){
        $td = TRUE;
      }else{
        $td = FALSE;
      }

      if($this->uri->segment(8) == 1){
        $prod = TRUE;
      }else{
        $prod = FALSE;
      }

      if($this->ventaMP($start, $end, $t, $td)){

        $this->db->query("DROP TEMPORARY TABLE IF EXISTS soloVenta");

        if($sv == 1){
          $qSV = "IF((SUM(VentaMXN + OtrosIngresosMXN + EgresosMXN) < 0
                          AND NewLoc IS NOT NULL)
                          OR SUM(VentaMXN + OtrosIngresosMXN + EgresosMXN) >= 0,
                      SUM(VentaMXN + OtrosIngresosMXN + EgresosMXN),
                      0) as Monto";
        }else{
          $qSV = "SUM(VentaMXN + OtrosIngresosMXN + EgresosMXN) as Monto";
        }

        $this->db->select("Fecha, Localizador, gpoInterno")
                ->select($qSV, FALSE)
                ->from("locs")
                ->group_by('Fecha, Localizador');

        $soloVenta = $this->db->get_compiled_select();
        $this->db->query("CREATE TEMPORARY TABLE soloVenta $soloVenta");
        $this->db->query("ALTER TABLE soloVenta
                          ADD PRIMARY KEY (Fecha, Localizador)");

        if($prod){
          if($this->ventaProducto($start, $end, $t, $td)){
            $this->db->query("DROP TEMPORARY TABLE IF EXISTS soloVentaProd");

            $this->db->select("Fecha, Localizador, item, itemType, categoryId, isPaq")
                    ->select($qSV, FALSE)
                    ->from("locsProd")
                    ->group_by('Fecha, Localizador, item');

            $soloVentaProd = $this->db->get_compiled_select();
            $this->db->query("CREATE TEMPORARY TABLE soloVentaProd $soloVentaProd");
            $this->db->query("ALTER TABLE soloVentaProd
                              ADD PRIMARY KEY (Fecha, Localizador, item)");

            $this->db->query("DROP TEMPORARY TABLE IF EXISTS prod");

            $this->db->select("a.*, gpoInterno")
                    ->select("CASE
                                WHEN isPaq != 0 THEN 'Paquete'
                                WHEN itemType = 0 THEN 'None'
                                WHEN itemType = 1 THEN 'Hotel'
                                WHEN itemType = 2 THEN 'Transfer'
                                WHEN itemType = 3 THEN 'Vuelo'
                                WHEN itemType = 4 THEN 'Tour'
                                WHEN itemType = 5 THEN 'Auto'
                                WHEN itemType = 6 THEN 'Paquete'
                                WHEN itemType = 7 THEN 'ServiceCharge'
                                WHEN itemType = 8 THEN 'Bus'
                                WHEN itemType = 11 THEN 'Crucero'
                                WHEN itemType = 12 THEN 'Seguro'
                                WHEN itemType = 13 THEN 'Circuito'
                                WHEN itemType = 14 THEN
                                  CASE
                                    WHEN categoryId = 0 THEN 'None'
                                    WHEN categoryId = 1 THEN 'Hotel'
                                    WHEN categoryId = 6 THEN 'Transfer'
                                    WHEN categoryId = 3 THEN 'Vuelo'
                                    WHEN categoryId = 7 THEN 'Tour'
                                    WHEN categoryId = 8 THEN 'Auto'
                                    WHEN categoryId = 0 THEN 'Paquete'
                                    WHEN categoryId = 14 THEN 'ServiceCharge'
                                    WHEN categoryId = 9 THEN 'Bus'
                                    WHEN categoryId = 2 THEN 'Crucero'
                                    WHEN categoryId = 4 THEN 'Seguro'
                                    WHEN categoryId = 10 THEN 'Circuito'
                                    WHEN categoryId = 5 THEN 'Generico'
                                    ELSE 'Otro'
                                  END
                                ELSE 'Otro'
                              END as iType", FALSE)
                    ->from("soloVentaProd a")
                    ->join("soloVenta b", "a.Fecha = b.Fecha AND a.Localizador = b.Localizador", "left")
                    ->where("b.Localizador IS NOT ", "NULL", FALSE);

                    $prodQ = $this->db->get_compiled_select();
                    if($this->db->query("CREATE TEMPORARY TABLE prod $prodQ")){
                      $this->db->select('Fecha, gpoInterno, iType as producto, SUM(Monto) as Monto', FALSE)
                              ->from('prod')
                              ->group_by('Fecha, gpoInterno, producto');
                    }else{
                      errResponse('Error al compilar información', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', array($this->db->error(),$prodQ));
                    }

              }

        }else{
          $this->db->select('Fecha, gpoInterno, SUM(Monto) as Monto', FALSE)
                  ->from('soloVenta')
                  ->group_by('Fecha, gpoInterno');
        }

        if($q = $this->db->get()){
          $result = $q->result_array();

          foreach($result as $index => $info){
            if($info['Monto'] == NULL){
              $monto = 0;
            }else{
              $monto = $info['Monto'];
            }

            if($prod){
              $dataRes[$info['Fecha']][$info['producto']][$info['gpoInterno']]=floatVal($monto);
            }else{
              $dataRes[$info['Fecha']][$info['gpoInterno']]=floatVal($monto);
            }
          }

          $luQ = $this->db->query("SELECT MAX(Last_Update) as lu FROM d_Locs WHERE Fecha=CURDATE()");
          $luR = $luQ->row_array();
          $lu = $luR['lu'];

          okResponse( 'Data obtenida', 'data', $dataRes, $this, 'lu', $lu );
        }else{
          errResponse('Error al compilar información', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
        }

        return true;

      }

    });

    jsonPrint( $result );

  }

  private function ventaMP($inicio, $fin, $type, $td=false){

    $this->db->query("DROP TEMPORARY TABLE IF EXISTS locs");

    if($type){
      $pdvType = "WHEN gpoCanalKpi = 'PDV' THEN 'PDV Presencial'";
    }else{
      $pdvType = "WHEN tipo = 1 THEN 'CC OUT'
                  WHEN tipo = 2 THEN 'PDV IN'
                  ELSE 'PDV Presencial'";
    }

    if($td){
      $table = "d_Locs";
      $fecha = "a.Fecha";
      $fechaVar = "CURDATE()";
    }else{
      $table = "t_Locs";
      $fecha = "a.Fecha BETWEEN";
      $fechaVar = "'$inicio' AND '$fin'";
    }

    $this->db->select("a.*, canal, gpoCanal, gpoCanalKpi, marca, pais, tipoCanal, dep, vacante, puesto")
            ->select("case
                    		WHEN gpoCanalKpi = 'PDV' THEN
                          CASE
                            $pdvType
                          END
                    		WHEN a.asesor>=0 THEN
                    			CASE
                    				WHEN dep = 5 THEN
                    					CASE
                    						WHEN tipo = 2 THEN 'CC IN'
                    						ELSE 'CC OUT'
                    					end
                    				WHEN dep = 29 THEN 'PDV IN'
                    				ELSE 'CC IN'
                    			end
                    		ELSE 'Online'
                    	end gpoInterno", FALSE)
            ->select("IF(VentaMXN>0, Localizador, NULL) as NewLoc", FALSE)
            ->from("$table a")
            ->join("chanGroups b", "a.chanId = b.id", "left")
            ->join("dep_asesores c", "a.asesor = c.asesor AND a.Fecha = c.Fecha", "left")
            ->where($fecha, $fechaVar, FALSE)
            ->where( array( 'marca' => 'Marcas Propias', 'pais' => 'MX' ) );

    $tableLocs = $this->db->get_compiled_select();

    IF($this->db->query("CREATE TEMPORARY TABLE locs $tableLocs")){
      return true;
    }else{
      errResponse('Error al compilar información', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
    }


  }
  private function ventaProducto($inicio, $fin, $type, $td=false){

    $this->db->query("DROP TEMPORARY TABLE IF EXISTS locsProd");

    if($td){
      $table = "d_hoteles";
      $fecha = "a.Fecha";
      $fechaVar = "CURDATE()";
    }else{
      $table = "t_hoteles";
      $fecha = "a.Fecha BETWEEN";
      $fechaVar = "'$inicio' AND '$fin'";
    }

    $this->db->select("a.*, canal, gpoCanal, gpoCanalKpi, marca, pais, tipoCanal")
            ->select("IF(VentaMXN>0, Localizador, NULL) as NewLoc", FALSE)
            ->from("$table a")
            ->join("chanGroups b", "a.chanId = b.id", "left")
            ->where($fecha, $fechaVar, FALSE)
            ->where( array( 'marca' => 'Marcas Propias', 'pais' => 'MX' ) );

    $tableLocs = $this->db->get_compiled_select();

    IF($this->db->query("CREATE TEMPORARY TABLE locsProd $tableLocs")){
      return true;
    }else{
      errResponse('Error al compilar información', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
    }


  }

  public function getRN_post(){

    $result = validateToken( $_GET['token'], $_GET['usn'], $func = function(){

      $data = $this->post();

      $this->db->query("SET @inicio = CAST('".$data['start']."' as DATE)");
      $this->db->query("SET @fin    = CAST('".$data['end']."' as DATE)");
      $this->db->query("SET @pais   = '".$data['pais']."'");
      $this->db->query("SET @marca  = '".$data['marca']."'");

      $this->db->query("DROP TEMPORARY TABLE IF EXISTS hotelesRAW");

      $this->db->select("a.*", FALSE)
              ->select("if(VentaMXN>0,CONCAT(Localizador,"-",item),null) as NewLoc", FALSE)
              ->select("IF(tipoCanal = 'Movil', 'Online', tipoCanal) as tipoCanal", FALSE)
              ->select("gpoCanalKpi")
              ->from("t_hoteles a")
              ->join("chanGroups b", "a.chanId = b.id", "left")
              ->where("Fecha BETWEEN ", "@inicio AND IF(@fin>CURDATE(),CURDATE(),@fin)", FALSE)
              ->where(array( 'categoryId' => 1, 'pais' => $data['pais'], 'marca' => $data['marca'] ));

      $hotelesRAW = $this->db->get_compiled_select();

      if( $this->db->query("CREATE TEMPORARY TABLE hotelesRAW $hotelesRAW") ){

        $this->db->query("ALTER TABLE hotelesRAW ADD PRIMARY KEY (`Localizador`, `Fecha`, `Hora`, `item`)");
        $this->db->query("SELECT @maxDate := MAX(Fecha) FROM hotelesRAW");

        $this->db->select("a.*", FALSE)
                ->select("if(VentaMXN>0,CONCAT(Localizador,"-",item),null) as NewLoc", FALSE)
                ->select("IF(tipoCanal = 'Movil', 'Online', tipoCanal) as tipoCanal", FALSE)
                ->select("gpoCanalKpi")
                ->from("d_hoteles a")
                ->join("chanGroups b", "a.chanId = b.id", "left")
                ->where("Fecha BETWEEN ", "IF(@maxDate IS NULL, @inicio, @maxDate) AND IF(@fin>CURDATE(),CURDATE(),@fin)", FALSE)
                ->where(array( 'categoryId' => 1, 'pais' => $data['pais'], 'marca' => $data['marca'] ));

        $hotelesRAW = $this->db->get_compiled_select();

        if( $this->db->query("INSERT INTO hotelesRAW (SELECT * FROM ($hotelesRAW) a) ON DUPLICATE KEY UPDATE VentaMXN = a.VentaMXN") ){

          $this->db->select("Fecha, gpoCanalKpi, tipoCanal, SUM(clientNights) as RN_w_xld, SUM(IF(clientNights>0,clientNights,0)) as RN", FALSE)
                  ->from('hotelesRAW')
                  ->group_by("Fecha, gpoCanalKpi, tipoCanal");

          if( $dates = $this->db->get() ){

            $this->db->select("gpoCanalKpi, tipoCanal, SUM(clientNights) as RN_w_xld, SUM(IF(clientNights>0,clientNights,0)) as RN", FALSE)
                    ->from('hotelesRAW')
                    ->group_by("gpoCanalKpi, tipoCanal");

            if( $all = $this->db->get() ){

              $luq = $this->db->query("SELECT MAX(Last_Update) as LU FROM d_hoteles WHERE Fecha = CURDATE()");

              $data = array( 'dates' => $dates->result_array(), 'all' => $all->result_array(), 'lu' => $luq->row_array());

              okResponse( 'Data obtenida', 'data', $data, $this, 'lu', $lu );

            }else{
              errResponse('Error al compilar data por Rango', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
            }

          }else{
            errResponse('Error al compilar data por Fecha', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
          }


        }else{
          errResponse('Error al insertar data actual a hotelesRAW', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
        }

      }else{
        errResponse('Error al compilar información hotelesRAW', REST_Controller::HTTP_BAD_REQUEST, $this, 'error', $this->db->error());
      }



        return true;

    });

    jsonPrint( $result );

  }

}
