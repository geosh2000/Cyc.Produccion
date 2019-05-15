<?php

function sendMail($user, $cantidad, $dep, $puesto){
  $name=str_replace('.',' ',$user);
  $name=ucwords($name);
  
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
                                                                                <p><span style='color: #5266a2; font-size: 24px; line-height: 130%;'>¡Hola $name!<br>Han solicitado nuevas vacantes</span></p>
                                                                                <p style='line-height: 140%;'> El usuario <em><span style='color: #5266a2;'><strong>$user</strong></span>&nbsp;</em>ha solicitado <strong><span style='color: #5266a2;'>$cantidad</span>&nbsp;</strong>nuevas vacantes para:<br><span style='text-decoration: underline; color: #5266a2;'><strong>$dep -&gt;&nbsp;$puesto</strong></span></p>
                                                                                <p style='line-height: 140%;'> Para que la vacante pueda activarse, es necesario que reciba tu autorizaci&oacute;n.</p>
                                                                                
                                                                                <p style='line-height: 140%;'> <a href='http://operaciones.pricetravel.com.mx/config/aprobaciones_pendientes.php' target='_blank'>Revisar y Autorizar Vacantes</a>
                                                                                </p><p style='line-height: 140%;'>¡Saludos! </p>
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
  $headers .= "From: Notificaciones ComeyCome <comeycome@pricetravel.com>";

  mail("$user@pricetravel.com","Nueva Vacante Solicitada ($user)",$msg,$headers);
  
}

$query="SELECT Departamento FROM PCRCs WHERE id=$dep";
if($result=Queries::query($query)){
  $fila=$result->fetch_assoc();
  $m_dep=$fila['Departamento'];
}

$query="SELECT Puesto FROM PCRCs_puestos WHERE id=$puesto";
if($result=Queries::query($query)){
  $fila=$result->fetch_assoc();
  $m_puesto=$fila['Puesto'];
}

$query="SELECT usuario FROM mail_lists WHERE notif='vacantes'";
if($result=Queries::query($query)){
  while($fila=$result->fetch_assoc()){
     sendMail($fila['usuario'],$cantidad,$m_dep,$m_puesto);
  }
}

?>

