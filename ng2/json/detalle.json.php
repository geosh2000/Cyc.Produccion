<?php
header("Access-Control-Allow-Origin: *");

include_once("../../modules/modules.php");
include_once("../../common/JWT.php");
include_once("../validateToken.php");

timeAndRegion::setRegion('Cun');

class Asesor{
    public $nombre;
    public $corto;
    public $numcol;
    public $tel;
    public $tel2;
    public $correo;
    public $correoPersonal;
    public $puesto;
    public $hc_puesto;
    public $departamento;
    public $ingreso;
    public $egreso;
    public $status;
    public $contrato_tipo;
    public $contrato_status;
    public $contrato_fin;
    public $cxc_total;
    public $cxc_adeudo;
    public $cxc_quincena;
    public $histo_puestos;
    public $sanc;
    public $cxc_pend;
    public $recontratable;
    public $idAsesor;
    public $profile;
    public $solPendiente=0;
    public $profileID;
    public $rfc;
    public $fnacimiento;
    public $nom;
    public $ape;
    public $ncorto;
    public $pasaporte;
    public $visa;
    public $asistencia = array();
    public $salario;
    public $oficina;
    public $PDV;
    public $sup;
    public $solSalario;



    public function __construct($id){
        $connectdb=Connection::mysqliDB('CC');

        //Get General Data
        $query="SELECT
                    a.id,
                    a.Nombre,
                    `N Corto`,
                    num_colaborador,
                    Telefono1,
                    Telefono2,
                    Usuario,
                    correo_personal as correo,
                    c.Departamento,
                    b.hc_puesto,
                    e.oficina,
                    f.PDV,
                    g.nombre as Puesto,
                    d.Puesto as Alias,
                    Ingreso,
                    Egreso,
                    IF(Egreso>CURDATE(),'Activo','Inactivo') as Status,
                    RFC,
                    Fecha_Nacimiento,
                    Nombre_Separado,
                    Apellidos_Separado,
                    Vigencia_Pasaporte,
                    Vigencia_Visa
                FROM
                    Asesores a
                        LEFT JOIN
                    dep_asesores b ON a.id = b.asesor AND CURDATE()=b.Fecha
                        LEFT JOIN
                    PCRCs c ON b.dep = c.id
                        LEFT JOIN
                    PCRCs_puestos d ON b.puesto = d.id
                        LEFT JOIN
                    asesores_plazas e ON b.vacante=e.id
                        LEFT JOIN
                    PDVs f ON e.oficina = f.id
                        LEFT JOIN
                    hc_codigos_Puesto g ON b.hc_puesto=g.id
                WHERE
                    a.id = $id";
        if($result=$connectdb->query($query)){
            $fila=$result->fetch_assoc();
            $this->idAsesor=$fila['id'];
            $this->nombre=utf8_encode($fila['Nombre']);
            $this->corto=utf8_encode($fila['N Corto']." (id: ".$fila['id'].")");
            $this->ncorto=utf8_encode($fila['N Corto']);
            $this->tel=utf8_encode($fila['Telefono1']);
            $this->tel1=utf8_encode($fila['Telefono2']);
            $this->correo=utf8_encode($fila['Usuario']."@pricetravel.com");
            $this->correoPersonal=utf8_encode($fila['correo']);
            $this->puesto=utf8_encode($fila['Puesto'])." (".utf8_encode($fila['Alias']).")";
            $this->hc_puesto=utf8_encode($fila['hc_puesto']);
            $this->departamento=utf8_encode($fila['Departamento']);
            $this->ingreso=utf8_encode($fila['Ingreso']);
            $this->rfc=utf8_encode($fila['RFC']);
            $this->fnacimiento=utf8_encode($fila['Fecha_Nacimiento']);
            $this->nom=utf8_encode($fila['Nombre_Separado']);
            $this->ape=utf8_encode($fila['Apellidos_Separado']);
            $this->pasaporte=utf8_encode($fila['Vigencia_Pasaporte']);
            $this->visa=utf8_encode($fila['Vigencia_Visa']);
            $this->oficina=utf8_encode($fila['oficina']);
            $this->PDV=utf8_encode($fila['PDV']);
            $this->numcol=utf8_encode($fila['num_colaborador']);

            if($fila['Status']=='Activo'){
                $this->status=utf8_encode("Activo");
            }else{
                $this->status=utf8_encode("Inactivo");
            }


            if(date('Y',strtotime($fila['Egreso']))>=2030){
                $this->egreso=utf8_encode('NA');
            }else{
                $this->egreso=utf8_encode($fila['Egreso']);

                $query="SELECT * FROM asesores_recontratable WHERE asesor=$id";
                if($result=$connectdb->query($query)){
                    $fila=$result->fetch_assoc();
                    $this->recontratable=$fila['recontratable'];
                }
            }




        }

        //Get profile
        $query="SELECT id, profile_name FROM userDB a LEFT JOIN profilesDB b ON a.profile=b.id WHERE a.asesor_id=$id";
        if($result=$connectdb->query($query)){
          $fila=$result->fetch_assoc();
          $this->profile=$fila['profile_name'];
            $this->profileID=$fila['id'];
        }

        //CodigoPuesto
        $query="SELECT
                  Codigo
              FROM
                  dep_asesores a
                      LEFT JOIN
                  (SELECT
                      a.id AS puestoID,
                          d.clave AS UDN,
                          c.clave AS Area,
                          b.clave AS Departamento,
                          a.clave AS Puesto,
                          CONCAT(d.clave, '-', c.clave, '-', b.clave, '-', a.clave) AS Codigo,
                          d.nombre AS UDN_nombre,
                          c.nombre AS Area_nombre,
                          b.nombre AS Departamento_nombre,
                          a.nombre AS Puesto_nombre
                  FROM
                      hc_codigos_Puesto a
                  LEFT JOIN hc_codigos_Departamento b ON a.departamento = b.id
                  LEFT JOIN hc_codigos_Areas c ON b.area = c.id
                  LEFT JOIN hc_codigos_UnidadDeNegocio d ON c.unidadDeNegocio = d.id) c ON a.hc_puesto = c.puestoID
              WHERE
                  a.asesor=$id AND a.Fecha=CURDATE()";
        if($result=$connectdb->query($query)){
          $fila =  $result->fetch_assoc();
          $this->codigo=$fila['Codigo'];
        }

        //Get Vacantes
        $query="SELECT
                    a.id,
                    fecha_in,
                    c.Departamento,
                    d.Puesto,
                    e.Ciudad,
                    f.PDV AS Oficina
                FROM
                    asesores_movimiento_vacantes a
                        LEFT JOIN
                    asesores_plazas b ON a.vacante = b.id
                        LEFT JOIN
                    PCRCs c ON b.departamento = c.id
                        LEFT JOIN
                    PCRCs_puestos d ON b.puesto = d.id
                        LEFT JOIN
                    db_municipios e ON b.ciudad = e.id
                        LEFT JOIN
                    PDVs f ON b.oficina = f.id
                WHERE
                    asesor_in = $id
                ORDER BY Fecha_in";
        if($result=$connectdb->query($query)){
            $i=0;
            while($fila=$result->fetch_assoc()){
                $this->histo_puestos[$i]['fecha']=utf8_encode($fila['fecha_in']);
                $this->histo_puestos[$i]['puesto']=utf8_encode($fila['Departamento']." -> ".$fila['Puesto']);
                $this->histo_puestos[$i]['pdv']=utf8_encode($fila['Oficina']);
                $this->histo_puestos[$i]['ciudad']=utf8_encode($fila['Ciudad']);
                $this->histo_puestos[$i]['id']=utf8_encode($fila['id']);
                $i++;
            }
        }

        //Solicitudes Pendientes
        $query="SELECT
                    a.id,
                    fecha_solicitud,
                    NOMBREASESOR(solicitado_por, 1) solicitado,
                    solicitado_por as solicitadoID,
                    recontratable,
                    reemplazable,
                    fecha,
                    a.tipo,
                    c.Departamento,
                    d.Puesto,
                    e.Ciudad,
                    PDV AS Oficina,
                    a.comentarios,
                    a.comentariosRRHH,
                    CASE
                        WHEN a.status=0 THEN 'En espera'
                        WHEN a.status=1 THEN 'Aprobada'
                        WHEN a.status=2 THEN 'En proceso de revision'
                        WHEN a.status=3 THEN 'Declinada'
                        WHEN a.status=4 THEN 'Cancelada'
                    END as statusOK,
                    a.status
                FROM
                    rrhh_solicitudesCambioBaja a
                        LEFT JOIN
                    asesores_plazas b ON a.vacante = b.id
                        LEFT JOIN
                    PCRCs c ON b.departamento = c.id
                        LEFT JOIN
                    PCRCs_puestos d ON b.puesto = d.id
                        LEFT JOIN
                    db_municipios e ON b.ciudad = e.id
                        LEFT JOIN
                    PDVs f ON b.oficina = f.id
                WHERE
                    asesor =$id
                ORDER BY fecha_solicitud";
        if($result=$connectdb->query($query)){
            while($fila=$result->fetch_assoc()){


                switch($fila['status']){
                    case 0:
                        $this->histo_solicitudes[$fila['id']]['status']=utf8_encode("En Espera");
                        $this->solPendiente=1;
                        break;
                    case 1:
                        $this->histo_solicitudes[$fila['id']]['status']=utf8_encode("Aprobada");
                        break;
                    case 2:
                        $this->histo_solicitudes[$fila['id']]['status']=utf8_encode("En proceso de Revision");
                        $this->solPendiente=1;
                        break;
                    case 3:
                        $this->histo_solicitudes[$fila['id']]['status']=utf8_encode("Declinada");
                        break;
                    case 4:
                        $this->histo_solicitudes[$fila['id']]['status']=utf8_encode("Cancelada");
                        break;
                }

                $this->histo_solicitudes[$fila['id']]['statusId']=$fila['status'];
                $this->histo_solicitudes[$fila['id']]['fechaRequest']=utf8_encode($fila['fecha_solicitud']);
                $this->histo_solicitudes[$fila['id']]['id']=utf8_encode($fila['id']);
                $this->histo_solicitudes[$fila['id']]['rrhhcomment']=utf8_encode($fila['comentariosRRHH']);
                $this->histo_solicitudes[$fila['id']]['solicitante']=utf8_encode($fila['solicitado']);
                $this->histo_solicitudes[$fila['id']]['solicitanteID']=utf8_encode($fila['solicitadoID']);
                $this->histo_solicitudes[$fila['id']]['fechaSolicitada']=utf8_encode($fila['fecha']);
                $this->histo_solicitudes[$fila['id']]['dep']=utf8_encode($fila['Departamento']);
                $this->histo_solicitudes[$fila['id']]['puesto']=utf8_encode($fila['Puesto']);
                $this->histo_solicitudes[$fila['id']]['pdv']=utf8_encode($fila['Oficina']);
                $this->histo_solicitudes[$fila['id']]['ciudad']=utf8_encode($fila['Ciudad']);
                $this->histo_solicitudes[$fila['id']]['solicitudComment']=utf8_encode($fila['comentarios']);
                $this->histo_solicitudes[$fila['id']]['recontratable']=utf8_encode($fila['recontratable']);
                $this->histo_solicitudes[$fila['id']]['reemplazable']=utf8_encode($fila['reemplazable']);

                if($fila['tipo']==1){
                  $this->histo_solicitudes[$fila['id']]['tipo']=utf8_encode("Cambio");
                }else{
                  $this->histo_solicitudes[$fila['id']]['tipo']=utf8_encode("Baja");
                }
            }
        }

        //GET asistencia
        $query="SELECT CAST(MAX(login) as DATE) as Last FROM asesores_logs WHERE asesor=$id";
        if($result = $connectdb->query($query)){
          $fila=$result->fetch_assoc();
          $this->asistencia['last']=$fila['Last'];
        }

        $query="SELECT
                  IF(b.ausentismo IS NULL,
              		IF(js = je, 'Descanso', CONCAT(CAST(js as TIME),' - ',CAST(je as TIME)))
                  ,IF(b.a=1,b.ausentismo,'Descanso')) as tdHorario
              FROM
                  asesores_programacion a
                      LEFT JOIN
                  asesores_ausentismos b ON a.asesor = b.asesor
                      AND a.Fecha =b.Fecha
                      LEFT JOIN
                  `Tipos Ausentismos` c ON b.ausentismo = c.id
              WHERE
                  a.asesor = $id AND a.Fecha = CURDATE()";
          if($result = $connectdb->query($query)){
            $fila=$result->fetch_assoc();
            $this->asistencia['horario']= $fila['tdHorario'];
          }

        //Get Sanciones
        $query="SELECT
                    CASE
                        WHEN tipo = 1 THEN 'Acta'
                        WHEN tipo = 2 THEN 'Accion'
                        WHEN tipo = 3 THEN 'PPerformance'
                        ELSE 'Otro'
                    END AS Tipo,
                    COUNT(*) AS total,
                    SUM(Vigente) AS Vigentes
                FROM
                    (SELECT
                        tipo,
                            IF(CURDATE() BETWEEN fecha_afectacion_inicio AND fecha_afectacion_fin, 1, 0) AS Vigente
                    FROM
                        Sanciones
                    WHERE
                        asesor = $id) a
                GROUP BY tipo";
        if($result=$connectdb->query($query)){
            while($fila=$result->fetch_assoc()){
                $this->sanc[$fila['Tipo']]['Total']=$fila['total'];
                $this->sanc[$fila['Tipo']]['Vigentes']=$fila['Vigentes'];
            }
        }


        //CxCs
        $query="SET @thisnom=(SELECT id FROM rrhh_calendarioNomina WHERE CURDATE() BETWEEN inicio AND fin);

                DROP TEMPORARY TABLE IF EXISTS cxcs;

                CREATE TEMPORARY TABLE cxcs SELECT b.*, IF(quincena=@thisnom,1,0) as thisNom FROM asesores_cxc a LEFT JOIN rrhh_pagoCxC b ON a.id=b.cxc WHERE asesor=$id;";
        $i=0;
        if($connectdb->multi_query($query)){

          do{
            //echo $i."<br>";
            $i++;
          } while (@$connectdb->next_result());
        }else{
          echo "ERROR Multi! -> ".$connectdb->error;
        }

        $query="SELECT SUM(monto) as Monto, SUM(IF(cobrado=0,monto,0)) as Adeudo, SUM(IF(thisNom=1 AND cobrado = 0,monto,0)) as siguiente FROM cxcs";
        if($result=$connectdb->query($query)){
            while($fila=$result->fetch_assoc()){
                $this->cxc_total=$fila['Monto'];
                $this->cxc_adeudo=$fila['Adeudo'];
                $this->cxc_quincena=$fila['siguiente'];
            }
        }

        //Pagos Pendientes
        $query="SELECT
                    b.id, Localizador, n_pago, pago, b.monto
                FROM
                    asesores_cxc a
                        LEFT JOIN
                    rrhh_pagoCxC b ON a.id = b.cxc
                        LEFT JOIN
                    rrhh_calendarioNomina c ON b.quincena = c.id
                WHERE
                    asesor = $id AND cobrado = 0
                        AND activo = 1";
        if($result=$connectdb->query($query)){
            while($fila=$result->fetch_assoc()){
                $this->cxc_pend[$fila['id']]['Localizador']=$fila['Localizador'];
                $this->cxc_pend[$fila['id']]['n_pago']=$fila['n_pago'];
                $this->cxc_pend[$fila['id']]['Fecha']=$fila['pago'];
                $this->cxc_pend[$fila['id']]['Monto']=$fila['monto'];
            }
        }

        //Contratos
        $query="SELECT *, IF(CURDATE()>=fin,'Vencido','Vigente') as Status FROM asesores_contratos WHERE asesor=$id AND inicio<=CURDATE() ORDER BY inicio DESC LIMIT 1";
        if($result=$connectdb->query($query)){
            while($fila=$result->fetch_assoc()){
                if($fila['tipo']==1){
                    $this->contrato_fin=$fila['fin'];
                    $this->contrato_tipo='Temporal';
                    $this->contrato_status=$fila['Status'];
                }else{
                    $this->contrato_fin='NA';
                    $this->contrato_tipo='Indefinido';
                    $this->contrato_status='Vigente';
                }

            }
        }




        $this->salario['factor']="NA";
        $this->salario['factor']="NA";
        $this->salario['diario']="NA";
        $this->salario['mensual']="NA";

        //Salarios
        $query="SELECT salarioPuesto(".$this->hc_puesto.", CURDATE()) as codigo, salarioAsesor(".$this->idAsesor.",CURDATE(),'salario') as mensual, salarioAsesor(".$this->idAsesor.",CURDATE(),'factor') as factor";
        if($result = $connectdb->query($query)){
          $fila=$result->fetch_assoc();
          $this->salario['codigo']=$fila['codigo'];
          $this->salario['factor']=$fila['factor']*100;
          $this->salario['mensual']=$fila['mensual'];
          $this->salario['diario']=$fila['mensual']/30;
        }

        // Jefe Directo
        if($this->departamento != 'PDV'){
          $query="SELECT FINDSUPERDAY(DAY(CURDATE()),MONTH(CURDATE()),YEAR(CURDATE()),".$this->idAsesor.") as sup";
        }else{
          $query="SELECT FINDSUPPDVDAY(".$this->oficina.",CURDATE(),1) as sup";
        }

        if($result = $connectdb->query($query)){
          $fila = $result->fetch_assoc();
          $this->sup = utf8_encode($fila['sup']);
        }

        //solSalario
        $query="SELECT * FROM rrhh_solicitudAjusteSalarial WHERE asesor=".$this->idAsesor." AND solicitudActiva='1'";
        if($result = $connectdb->query($query)){
          $fila = $result->fetch_assoc();
          if($fila['id'] == NULL){
            $this->solSalario['exists'] = false;
          }else{
            $this->solSalario['exists']         = true;
            $this->solSalario['info']['id']     = $fila['id'];
            $this->solSalario['info']['ajuste'] = $fila['ajuste'];
            $this->solSalario['info']['antiguo'] = number_format($fila['antiguo_salario'],2);
            $this->solSalario['info']['nuevo']  = number_format($fila['nuevo_salario'],2);
            $this->solSalario['info']['fecha']  = $fila['fecha_cambio'];
          }

        }




        $connectdb->close();
    }
}

validateTk(function(){
    if(isset($_GET['id'])){
      $id=$_GET['id'];
    }else{
      $postdata = file_get_contents("php://input");
      $request = json_decode($postdata);

      $id=$request->id;
    }

    $asesor = new Asesor($id);
    $data =  (array) $asesor;
    echo json_encode($data);
},$tkFlag);







 ?>
