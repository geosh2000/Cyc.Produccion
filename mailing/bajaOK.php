<?php

function sendMail($user, $m_data){
  $name=str_replace('.',' ',$user);
  $name=ucwords($name);

  if($m_data['status']==1){
      $status_a='Aprobada';
      $status_b='aprobado';
  }else{
      $status_a='Declinada';
      $status_b='denegado';
  }
  
  $msg="<html xmlns='http://www.w3.org/1999/xhtml'><head>
              <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>Solicitud Aprobada</title>
                    <style type='text/css'>
                       @media only screen and (max-width: 480px){
                       body,table,td,p,a,li,blockquote{
                       -webkit-text-size-adjust:none !important;
                       }
                       }   @media only screen and (max-width: 480px){
                       body{
                       width:100% !important;
                       min-width:100% !important;
                       }
                       }   @media only screen and (max-width: 480px){
                       table[class=contentText]{
                       width:100% !important;
                       }
                       }   @media only screen and (max-width: 480px){
                       img[class=logoImage]{
                       width:100% !important;
                       }
                       }       @media only screen and (max-width: 480px){
                       table[id=containerMail],table[id=topHeaderMail],table[id=headerMail],table[id=bodyMail],table[id=footerMail]{
                       max-width:600px !important;
                       width:100% !important;
                       }
                       }    @media only screen and (max-width: 480px){
                       h2{
                       font-size:20px !important;
                       line-height:125% !important;
                       }
                       }   @media only screen and (max-width: 480px){
                       h3{
                       font-size:18px !important;
                       line-height:125% !important;
                       }
                       }   @media only screen and (max-width: 480px){
                       h4{
                       font-size:16px !important;
                       line-height:125% !important;
                       }
                       }
                    </style>
                 </head>
                <body leftmargin='0' marginwidth='0' topmargin='0' marginheight='0' offset='0' style='margin: 0;padding: 0;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;background-color: #F2F2F2;height: 100% !important;width: 100% !important;'>
                 <center>
                    <table align='center' border='0' cellpadding='0' cellspacing='0' height='100%' width='100%' id='bodyTable' style='border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;margin: 0;padding: 0;background-color: #F2F2F2;height: 100% !important;width: 100% !important;'>
                       <tbody>
                          <tr>
                             <td align='center' valign='top' style='mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;margin: 0;padding: 20px;border-top: 0;height: 100% !important;width: 100% !important;'>
                                <!-- Template para correo // -->
                                <table border='0' cellpadding='0' cellspacing='0' width='600' id='containerMail' style='border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;border: 0;'>
                                   <tbody>
                                      <tr>
                                         <td align='center' valign='top' style='mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                                            <!-- Top header // -->
                                            <table border='0' cellpadding='0' cellspacing='0' width='600' id='topHeaderMail' style='border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;background-color: #FFFFFF;border-top: 0;border-bottom: 0;'>
                                               <tbody>
                                                  <tr>
                                                     <td valign='top' style='padding-top: 9px;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                                                        <table border='0' cellpadding='0' cellspacing='0' width='100%' class='mcnTextBlock' style='border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                                                           <tbody>
                                                              <tr>
                                                                 <td valign='top' style='mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                                                                    <table align='left' border='0' cellpadding='0' cellspacing='0' width='366' class='contentText' style='border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                                                                       <tbody>
                                                                          <tr>
                                                                             <td valign='top' style='padding-top: 9px;padding-left: 18px;padding-bottom: 9px;padding-right: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #606060;font-family: Helvetica;font-size: 12px;line-height: 125%;text-align: left;'>
                                                                             </td>
                                                                          </tr>
                                                                       </tbody>
                                                                    </table>
                                                                    <table align='right' border='0' cellpadding='0' cellspacing='0' width='197' class='contentText' style='border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                                                                       <tbody>
                                                                          <tr>
                                                                             <td valign='top' style='padding-top: 9px;padding-right: 18px;padding-bottom: 9px;padding-left: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #606060;font-family: Helvetica;font-size: 11px;line-height: 125%;text-align: left;'>
                                                                                <a href='#' target='_blank' style='word-wrap: break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #3498db;font-weight: normal;text-decoration: underline; font-size:12px;'></a>
                                                                             </td>
                                                                          </tr>
                                                                       </tbody>
                                                                    </table>
                                                                 </td>
                                                              </tr>
                                                           </tbody>
                                                        </table>
                                                     </td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                            <!-- // termina top header -->
                                         </td>
                                      </tr>
                                      
                                      <tr>
                                         <td align='center' valign='top' style='mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                                            <!-- empieza cuerpo// -->
                                            <table border='0' cellpadding='0' cellspacing='0' width='600' id='bodyMail' style='border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;background-color: #FFFFFF;border-top: 0;border-bottom: 0;'>
                                               <tbody>
                                                  <tr>
                                                     <td valign='top' style='mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                                                        <table border='0' cellpadding='0' cellspacing='0' width='100%' style='border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                                                           <tbody>
                                                              <tr>
                                                                 <td valign='top' style='mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                                                                    <table align='left' border='0' cellpadding='0' cellspacing='0' width='600' class='contentText' style='border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                                                                       <tbody>
                                                                          <tr>
                                                                             <td valign='top' style='padding-top: 9px;padding-right: 18px;padding-bottom: 9px;padding-left: 18px;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #606060;font-family: Helvetica;font-size: 15px;text-align: center;'>
                                                                                <p><span style='color: #5266a2; font-size: 24px; line-height: 130%;'>¡Hola $name!<br><br>Tu solicitud ha sido $status_a</span></p>
                                                                                <p style='line-height: 140%;'> El usuario <em><span style='color: #5266a2;'><strong>".$m_data['updater']."</strong></span>&nbsp;</em>ha $status_b la siguiente solicitud de baja:<br><br><span style='text-decoration: underline; color: #5266a2;'><strong>".$m_data['asesor']."<br></strong></span><br>con fecha: <span style='text-decoration: underline; color: #5266a2;'><strong>".$m_data['fecha_cambio']."</strong></span></p>";
    
                                                                                if($m_data['status']==1){
                                                                                
                                                                                    if($m_data['replace']==1){
                                                                                      $msg.="<p style='line-height: 140%;'>Se genera la nueva vacante:</p><p>".utf8_encode($m_data['old_dep'])." -> ".utf8_encode($m_data['old_puesto'])."</p><p>en: ".utf8_encode($m_data['old_ofi'])."</p><p>Liberada con fecha <strong>".$m_data['fecha']."</strong></p>";
                                                                                    }else{
                                                                                      $msg.="<p style='line-height: 140%;'>La vacante anterior ha quedaría inactiva por lo que no necesita reemplazo</strong></p>";
                                                                                    }
                                                                                }else{
                                                                                    $msg.= "<p style='line-height: 140%;'>Notas registradas: \"".$m_data['comentarios']."\"</strong></p>";
                                                                                }
                                                                                
                                                                                $msg.="<p style='line-height: 140%;'>¡Saludos! </p>
                                                                              <p style='line-height: 140%;'></p>
                                                                                <p style='line-height: 140%; font-weight: 100; color: #7f8c8d;'><em>El equipo de <span style='font-weight: 400;'>ComeyCome</span></em></p>
                                                                             </td>
                                                                          </tr>
                                                                          <tr>
                                                                             <td style='padding: 18px;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                                                                                <table border='0' cellpadding='0' cellspacing='0' width='100%' style='border-top-width: 1px;border-top-style: solid;border-top-color: #999999;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                                                                                   <tbody>
                                                                                      <tr>
                                                                                         <td style='mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                                                                                            <span></span>
                                                                                         </td>
                                                                                      </tr>
                                                                                   </tbody>
                                                                                </table>
                                                                             </td>
                                                                          </tr>
                                                                       </tbody>
                                                                    </table>
                                                                 </td>
                                                              </tr>
                                                           </tbody>
                                                        </table>
                                                     </td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                            <!-- // termina cuerpo -->
                                         </td>
                                      </tr>
                                      <tr>
                                         <td align='center' valign='top' style='mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                                            <!-- empieza footer // -->
                                            <table border='0' cellpadding='0' cellspacing='0' width='600' id='footerMail' style='border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;background-color: #FFFFFF;border-top: 0;border-bottom: 0;'>
                                               <tbody>
                                                  <tr>
                                                     <td valign='top' style='padding-bottom: 9px;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                                                        <table border='0' cellpadding='0' cellspacing='0' width='100%' style='border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                                                           <tbody>
                                                              <tr>
                                                                 <td valign='top' style='mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                                                                    <table align='left' border='0' cellpadding='0' cellspacing='0' width='600' class='contentText' style='border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                                                                       <tbody>
                                                                          <tr>
                                                                             <td valign='top' style='padding-top: 9px;padding-right: 18px;padding-bottom: 9px;padding-left: 18px;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #606060;font-family: Helvetica;font-size: 13px;line-height: 125%;text-align: left;'>
                                                                                <p style='text-align: center; line-height:140%'>Llámanos al <strong>01 800 007 0005</strong> </p>

                                                                                <p style='text-align: center; font-size:13px;'>2015 © &nbsp;ComeyCome. Todos los derechos reservados.&nbsp;</p>
                                                                             </td>
                                                                          </tr>
                                                                       </tbody>
                                                                    </table>
                                                                 </td>
                                                              </tr>
                                                           </tbody>
                                                        </table>
                                                     </td>
                                                  </tr>
                                               </tbody>
                                            </table>
                                            <!-- // termina footer -->
                                         </td>
                                      </tr>
                                   </tbody>
                                </table>
                                <!-- // termina template de correo -->
                             </td>
                          </tr>
                       </tbody>
                    </table>
                 </center>

              </body></html>";

  $headers = "MIME-Version: 1.0" . "\r\n";
  $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
  $headers .= "From: Notificaciones ComeyCome <operaciones@pricetravel.com>";

  mail("$user@pricetravel.com","Cambio de Status para solicitud de Baja (".$m_data['asesor'].")",$msg,$headers);
    
  //echo $msg;
  
}




$query="SELECT NombreAsesor(".$_SESSION['asesor_id'].",1) as updater, NombreAsesor(".$input['solicitante'].",1) as sol, NombreAsesor(".$input['asesor'].",1) as asesor";
if($result=Queries::query($query)){
  $fila=$result->fetch_assoc();
  $m_data['sol']=$fila['sol'];
  $m_data['updater']=$fila['updater'];
  $m_data['asesor']=$fila['asesor'];
}

$query="SELECT b.Departamento, c.Puesto, PDV FROM asesores_plazas a LEFT JOIN PCRCs b ON a.departamento=b.id LEFT JOIN PCRCs_puestos c ON a.puesto=c.id LEFT JOIN PDVs e ON a.oficina=e.id WHERE a.id=getLastVacante(".$input['asesor'].",0)";
if($result=Queries::query($query)){
  $fila=$result->fetch_assoc();
  $m_data['old_dep']=$fila['Departamento'];
  $m_data['old_puesto']=$fila['Puesto'];
  $m_data['old_ofi']=$fila['PDV'];
  
}

$query="SELECT b.Departamento, c.Puesto, PDV FROM asesores_plazas a LEFT JOIN PCRCs b ON a.departamento=b.id LEFT JOIN PCRCs_puestos c ON a.puesto=c.id LEFT JOIN PDVs e ON a.oficina=e.id WHERE a.id=".$input['vacante'];
if($result=Queries::query($query)){
  $fila=$result->fetch_assoc();
    $m_data['new_dep']=$fila['Departamento'];
    $m_data['new_puesto']=$fila['Puesto'];
    $m_data['new_ofi']=$fila['PDV'];
}

$m_data['replace']=$input['replace'];
$m_data['fecha']=$input['fecha_out'];
$m_data['fecha_cambio']=$input['fecha_in'];

$query="SELECT usuario FROM mail_lists WHERE notif='bajaOK'";
if($result=Queries::query($query)){
  while($fila=$result->fetch_assoc()){
     sendMail($fila['usuario'],$m_data);
  }
}

sendMail(strtolower(str_replace(' ','.',$m_data['sol'])),$m_data);


?>

