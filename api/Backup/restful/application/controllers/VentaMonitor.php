<?php
//defined('BASEPATH') OR exit('No direct script access allowed');
require( APPPATH.'/libraries/REST_Controller.php');
// use REST_Controller;


class VentaMonitor extends REST_Controller {

  public function __construct(){

    parent::__construct();
    $this->load->helper('json_utilities');
    $this->load->helper('validators');
    $this->load->helper('jwt');
    $this->load->database();
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

              $luq = $this->db->query("SELECT MAX(Last_Update) as LU FROM d_hoteles WHERE Fecha=CURDATE()");

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
