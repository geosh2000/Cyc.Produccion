<?php

include_once("../modules/modules.php");

class mailSolicitudPuesto(){

  public $destinatario;
  public $mailInfo;

  public function sendMail( $params, $vac_off ){

    $this->mailInfo['asesor'] = $params['asesor'];
    $this->mailInfo['fechaCambio'] = $params['fechaCambio'];
    $this->mailInfo['fechaLiberacion'] = $params['fechaLiberacion'];
    $this->mailInfo['reemplazable'] = $params['reemplazable'];
    $this->mailInfo['old']['vacante'] = $vac_off];
    $this->mailInfo['new']['vacante'] = $params['puesto']['vacante'];

    $query="SELECT a.id, b.Departamento, c.Puesto, PDV FROM asesores_plazas a LEFT JOIN PCRCs b ON a.departamento=b.id LEFT JOIN PCRCs_puestos c ON a.puesto=c.id LEFT JOIN PDVs e ON a.oficina=e.id WHERE a.id IN (".$mailInfo['old']['vacante'].",".$mailInfo['new']['vacante'].")";
    if($result=Queries::query($query)){
      while($fila=$result->fetch_assoc()){
        if($fila['id'] == $mailInfo['old']['vacante']){
          $flag = 'old';
        }else{
          $flag = 'new';
        }

        $this->mailInfo[$flag]['dep']=$fila['Departamento'];
        $this->mailInfo[$flag]['puesto']=$fila['Puesto'];
        $this->mailInfo[$flag]['oficina']=$fila['PDV'];
      }
    }


    $query="SELECT NombreAsesor(".$this->mailInfo['asesor'].",1) as nombreAsesor";
    if($result=Queries::query($query)){
      $fila=$result->fetch_assoc();
      $this->mailInfo['asesor'] = $fila['nombreAsesor'];
    }

    $query="SELECT usuario FROM mail_lists WHERE notif='cambio_puestoSOL'";
    if($result=Queries::query($query)){
      while($fila=$result->fetch_assoc()){
         $this->sendMail($fila['usuario'],$this->mailInfo);
      }
    }
  }

  private function sendMail($user, $m_data){
    $name=str_replace('.',' ',$user);
    $name=ucwords($name);

    $msg= "<html xmlns=\"http://www.w3.org/1999/xhtml\" style=\"-webkit-box-sizing: border-box;box-sizing: border-box;font-family: sans-serif;line-height: 1.15;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;-ms-overflow-style: scrollbar;-webkit-tap-highlight-color: transparent;\">\n";
    $msg.= "  <head style=\"-webkit-box-sizing: inherit;box-sizing: inherit;\">\n";
    $msg.= "    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;\">\n";
    $msg.= "    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;\">\n";
    $msg.= "    <title style=\"-webkit-box-sizing: inherit;box-sizing: inherit;\">Solicitud Aprobada</title>\n";
    $msg.= "  </head>\n";
    $msg.= "  <body style=\"-webkit-box-sizing: inherit;box-sizing: inherit;margin: 0;font-family: -apple-system,system-ui,BlinkMacSystemFont,&quot;Segoe UI&quot;,Roboto,&quot;Helvetica Neue&quot;,Arial,sans-serif;font-size: 1rem;font-weight: 400;line-height: 1.5;color: #292b2c;background-color: #fff;\">\n";
    $msg.= "    <div class=\"container\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;position: relative;margin-left: auto;margin-right: auto;padding-right: 15px;padding-left: 15px;  min-width: 900px; max-width:1200px\">\n";
    $msg.= "      <div class=\"card text-center\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;position: relative;display: flex;-webkit-box-orient: vertical;-webkit-box-direction: normal;-webkit-flex-direction: column;-ms-flex-direction: column;flex-direction: column;background-color: #fff;border: 1px solid rgba(0,0,0,.125);border-radius: .25rem;text-align: center!important;\">\n";
    $msg.= "        <div class=\"card-header\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;padding: .75rem 1.25rem;margin-bottom: 0;background-color: #f7f7f9;border-bottom: 1px solid rgba(0,0,0,.125);border-radius: calc(.25rem - 1px) calc(.25rem - 1px) 0 0;\">\n";
    $msg.= "          ComeyCome\n";
    $msg.= "        </div>\n";
    $msg.= "        <div class=\"card-block\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;-webkit-box-flex: 1;-webkit-flex: 1 1 auto;-ms-flex: 1 1 auto;flex: 1 1 auto;padding: 1.25rem;\">\n";
    $msg.= "          <h4 class=\"card-title\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;margin-top: 0;margin-bottom: .75rem;font-family: inherit;font-weight: 500;line-height: 1.1;color: inherit;font-size: 1.5rem;\">Cambio de Puesto</h4>\n";
    $msg.= "          <p class=\"card-text\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;orphans: 3;widows: 3;margin-top: 0;margin-bottom: 1rem;\">Hola $name!, <strong class=\"font-weight-bold\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;font-weight: 700;\">".$m_data['sol']."</strong> ha registrado una <strong class=\"font-weight-bold\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;font-weight: 700;\">solicitud de cambio</strong> en el ComeyCome. A continuación los detalles</p>\n";
    $msg.= "          <hr class=\"my-4\" style=\"-webkit-box-sizing: content-box;box-sizing: content-box;height: 0;overflow: visible;margin-top: 1.5rem!important;margin-bottom: 1.5rem!important;border: 0;border-top: 1px solid rgba(0,0,0,.1);\">\n";
    $msg.= "          <div class=\"d-flex justify-content-around align-items-center\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;display: flex!important;-webkit-justify-content: space-around!important;-ms-flex-pack: distribute!important;justify-content: space-around!important;-webkit-box-align: center!important;-webkit-align-items: center!important;-ms-flex-align: center!important;align-items: center!important;\">\n";
    $msg.= "\n";
    $msg.= "            <!-- OLD Puesto -->\n";
    $msg.= "            <div class=\"p-2 card\" style=\"width: 20rem;-webkit-box-sizing: inherit;box-sizing: inherit;position: relative;display: flex;-webkit-box-orient: vertical;-webkit-box-direction: normal;-webkit-flex-direction: column;-ms-flex-direction: column;flex-direction: column;background-color: #fff;border: 1px solid rgba(0,0,0,.125);border-radius: .25rem;padding: .5rem .5rem!important;\">\n";
    $msg.= "              <div class=\"card-block\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;-webkit-box-flex: 1;-webkit-flex: 1 1 auto;-ms-flex: 1 1 auto;flex: 1 1 auto;padding: 1.25rem;\">\n";
    $msg.= "                <h4 class=\"card-title\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;margin-top: 0;margin-bottom: .75rem;font-family: inherit;font-weight: 500;line-height: 1.1;color: inherit;font-size: 1.5rem;\">Puesto Actual</h4>\n";
    $msg.= "                <p class=\"card-text\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;orphans: 3;widows: 3;margin-top: 0;margin-bottom: 0;\">Some quick example text to build on the card title and make up the bulk of the card's content.</p>\n";
    $msg.= "              </div>\n";
    $msg.= "              <ul class=\"list-group list-group-flush\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;margin-top: 0;margin-bottom: 0;display: flex;-webkit-box-orient: vertical;-webkit-box-direction: normal;-webkit-flex-direction: column;-ms-flex-direction: column;flex-direction: column;padding-left: 0;\">\n";
    $msg.= "                <li class=\"list-group-item\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;position: relative;display: flex;-webkit-flex-flow: row wrap;-ms-flex-flow: row wrap;flex-flow: row wrap;-webkit-box-align: center;-webkit-align-items: center;-ms-flex-align: center;align-items: center;padding: .75rem 1.25rem;margin-bottom: -1px;background-color: #fff;border: 1px solid rgba(0,0,0,.125);border-top-right-radius: .25rem;border-top-left-radius: .25rem;border-right: 0;border-left: 0;border-radius: 0;\">".utf8_encode($m_data['old']['dep'])."</li>\n";
    $msg.= "                <li class=\"list-group-item\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;position: relative;display: flex;-webkit-flex-flow: row wrap;-ms-flex-flow: row wrap;flex-flow: row wrap;-webkit-box-align: center;-webkit-align-items: center;-ms-flex-align: center;align-items: center;padding: .75rem 1.25rem;margin-bottom: -1px;background-color: #fff;border: 1px solid rgba(0,0,0,.125);border-right: 0;border-left: 0;border-radius: 0;\">";
    $msg.= "                ".utf8_encode($m_data['old']['puesto'])."</li>\n";
    $msg.= "                <li class=\"list-group-item\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;position: relative;display: flex;-webkit-flex-flow: row wrap;-ms-flex-flow: row wrap;flex-flow: row wrap;-webkit-box-align: center;-webkit-align-items: center;-ms-flex-align: center;align-items: center;padding: .75rem 1.25rem;margin-bottom: 0;background-color: #fff;border: 1px solid rgba(0,0,0,.125);border-bottom-right-radius: .25rem;border-bottom-left-radius: .25rem;border-right: 0;border-left: 0;border-radius: 0;\">".$m_data['old']['oficina']."</li>\n";
    $msg.= "              </ul>\n";
    $msg.= "              <div class=\"card-block\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;-webkit-box-flex: 1;-webkit-flex: 1 1 auto;-ms-flex: 1 1 auto;flex: 1 1 auto;padding: 1.25rem;\">\n";

    if( $m_data['reemplazable'] == 1 ){
      $reemplazable="La vacante anterior quedaría liberada con fecha:";
      $fechaReemplazo=$m_data['fechaLiberacion'];
    }else{
      $reemplazable="La vacante anterior ha quedaría inactiva por lo que no necesita reemplazo";
      $fechaReemplazo="";
    }

    $msg.= "                <p>$reemplazable</p>";
    $msg.= "                <p>$fechaReemplazo</p>";
    $msg.= "              </div>\n";
    $msg.= "            </div>\n";
    $msg.= "            <div class=\"p-2 text-center\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;padding: .5rem .5rem!important;text-align: center!important;\">\n";
    $msg.= "              <h3 style=\"-webkit-box-sizing: inherit;box-sizing: inherit;orphans: 3;widows: 3;page-break-after: avoid;margin-top: 0;margin-bottom: .5rem;font-family: inherit;font-weight: 500;line-height: 1.1;color: inherit;font-size: 1.75rem;\">".$m_data['nombreAsesor']."</h3>\n";
    $msg.= "              <h1 style=\"-webkit-box-sizing: inherit;box-sizing: inherit;font-size: 2.5rem;margin: .67em 0;margin-top: 0;margin-bottom: .5rem;font-family: inherit;font-weight: 500;line-height: 1.1;color: inherit;\">=></h1>\n";
    $msg.= "              <h3 style=\"-webkit-box-sizing: inherit;box-sizing: inherit;orphans: 3;widows: 3;page-break-after: avoid;margin-top: 0;margin-bottom: .5rem;font-family: inherit;font-weight: 500;line-height: 1.1;color: inherit;font-size: 1.75rem;\">".$m_data['fechaCambio']."</h3>\n";
    $msg.= "            </div>\n";
    $msg.= "\n";
    $msg.= "            <!-- NEW Puesto -->\n";
    $msg.= "            <div class=\"p-2 card\" style=\"width: 20rem;-webkit-box-sizing: inherit;box-sizing: inherit;position: relative;display: flex;-webkit-box-orient: vertical;-webkit-box-direction: normal;-webkit-flex-direction: column;-ms-flex-direction: column;flex-direction: column;background-color: #fff;border: 1px solid rgba(0,0,0,.125);border-radius: .25rem;padding: .5rem .5rem!important;\">\n";
    $msg.= "              <div class=\"card-block\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;-webkit-box-flex: 1;-webkit-flex: 1 1 auto;-ms-flex: 1 1 auto;flex: 1 1 auto;padding: 1.25rem;\">\n";
    $msg.= "                <h4 class=\"card-title\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;margin-top: 0;margin-bottom: .75rem;font-family: inherit;font-weight: 500;line-height: 1.1;color: inherit;font-size: 1.5rem;\">Puesto Solicitado</h4>\n";
    $msg.= "                <p class=\"card-text\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;orphans: 3;widows: 3;margin-top: 0;margin-bottom: 0;\">Some quick example text to build on the card title and make up the bulk of the card's content.</p>\n";
    $msg.= "              </div>\n";
    $msg.= "              <ul class=\"list-group list-group-flush\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;margin-top: 0;margin-bottom: 0;display: flex;-webkit-box-orient: vertical;-webkit-box-direction: normal;-webkit-flex-direction: column;-ms-flex-direction: column;flex-direction: column;padding-left: 0;\">\n";
    $msg.= "                <li class=\"list-group-item\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;position: relative;display: flex;-webkit-flex-flow: row wrap;-ms-flex-flow: row wrap;flex-flow: row wrap;-webkit-box-align: center;-webkit-align-items: center;-ms-flex-align: center;align-items: center;padding: .75rem 1.25rem;margin-bottom: -1px;background-color: #fff;border: 1px solid rgba(0,0,0,.125);border-top-right-radius: .25rem;border-top-left-radius: .25rem;border-right: 0;border-left: 0;border-radius: 0;\">".utf8_encode($m_data['new']['dep'])."</li>\n";
    $msg.= "                <li class=\"list-group-item\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;position: relative;display: flex;-webkit-flex-flow: row wrap;-ms-flex-flow: row wrap;flex-flow: row wrap;-webkit-box-align: center;-webkit-align-items: center;-ms-flex-align: center;align-items: center;padding: .75rem 1.25rem;margin-bottom: -1px;background-color: #fff;border: 1px solid rgba(0,0,0,.125);border-right: 0;border-left: 0;border-radius: 0;\">";
    $msg.= "                ".utf8_encode($m_data['new']['puesto'])."</li>\n";
    $msg.= "                <li class=\"list-group-item\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;position: relative;display: flex;-webkit-flex-flow: row wrap;-ms-flex-flow: row wrap;flex-flow: row wrap;-webkit-box-align: center;-webkit-align-items: center;-ms-flex-align: center;align-items: center;padding: .75rem 1.25rem;margin-bottom: 0;background-color: #fff;border: 1px solid rgba(0,0,0,.125);border-bottom-right-radius: .25rem;border-bottom-left-radius: .25rem;border-right: 0;border-left: 0;border-radius: 0;\">".$m_data['new']['oficina']."s</li>\n";
    $msg.= "              </ul>\n";
    $msg.= "              <div class=\"card-block\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;-webkit-box-flex: 1;-webkit-flex: 1 1 auto;-ms-flex: 1 1 auto;flex: 1 1 auto;padding: 1.25rem;\">\n";
    $msg.= "                <a href=\"#\" class=\"card-link\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;background-color: transparent;-webkit-text-decoration-skip: objects;color: #0275d8;text-decoration: underline;-ms-touch-action: manipulation;touch-action: manipulation;\">Card link</a>\n";
    $msg.= "                <a href=\"#\" class=\"card-link\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;background-color: transparent;-webkit-text-decoration-skip: objects;color: #0275d8;text-decoration: underline;-ms-touch-action: manipulation;touch-action: manipulation;margin-left: 1.25rem;\">Another link</a>\n";
    $msg.= "              </div>\n";
    $msg.= "            </div>\n";
    $msg.= "          </div>\n";
    $msg.= "          <hr class=\"my-4\" style=\"-webkit-box-sizing: content-box;box-sizing: content-box;height: 0;overflow: visible;margin-top: 1.5rem!important;margin-bottom: 1.5rem!important;border: 0;border-top: 1px solid rgba(0,0,0,.1);\">\n";
    $msg.= "          <p class=\"text-center\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;orphans: 3;widows: 3;margin-top: 0;margin-bottom: 1rem;text-align: center!important;\">\n";
    $msg.= "            <a class=\"btn btn-primary btn-lg\" href=\"#\" role=\"button\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;background-color: #0275d8;-webkit-text-decoration-skip: objects;color: #fff;text-decoration: underline;-ms-touch-action: manipulation;touch-action: manipulation;cursor: pointer;display: inline-block;font-weight: 400;line-height: 1.25;text-align: center;white-space: nowrap;vertical-align: middle;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;border: 1px solid transparent;padding: .75rem 1.5rem;font-size: 1.25rem;border-radius: .3rem;-webkit-transition: all .2s ease-in-out;-o-transition: all .2s ease-in-out;transition: all .2s ease-in-out;border-color: #0275d8;\">Ver Solicitud</a>\n";
    $msg.= "          </p>\n";
    $msg.= "        </div>\n";
    $msg.= "        <div class=\"card-footer text-muted\" style=\"-webkit-box-sizing: inherit;box-sizing: inherit;padding: .75rem 1.25rem;background-color: #f7f7f9;border-top: 1px solid rgba(0,0,0,.125);border-radius: 0 0 calc(.25rem - 1px) calc(.25rem - 1px);color: #636c72!important;\">\n";
    $msg.= "          © ComeyCome 2017. Todos los der$msg.=s reservados\n";
    $msg.= "        </div>\n";
    $msg.= "      </div>\n";
    $msg.= "\n";
    $msg.= "    </div>\n";
    $msg.= "  </body>\n";
    $msg.= "</html>\n";
    $msg.= "\n";

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Notificaciones ComeyCome <operaciones@pricetravel.com>";

    mail("$user@pricetravel.com","Solicitud de Cambio de Puesto (".$m_data['nombreAsesor'].")",$msg,$headers);

    // echo $msg;

  }


}

?>
