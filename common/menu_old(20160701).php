<?php
$log=$_SESSION['login'];
$noProfile="Your profile is not allowed to acces this feature. Please check credentials with the administrator";
include("../connectDB.php");
$querysesion="SELECT session_id FROM userDB WHERE userid='".$_SESSION['id']."'";
$sesid=mysql_result(mysql_query($querysesion),0,'session_id');
//echo session_id()." $log // $sesid<br>$query";
if(session_id()!=$sesid){session_destroy(); }
$thpage=$_SERVER['PHP_SELF'];
?>
<script type="text/javascript">
$(function(){

    $( "#dialog-message" ).dialog({
        autoOpen: false,
        modal: true,
          buttons: {
            Ok: function() {
              $( this ).dialog( "close" );
            }
          }
        });

    

    //setInterval(function() {
    //    sendRequest();
    //}, 5000);

});
</script>

<nav>
<ul>
  <li><a href="#contact">Menu</a>
    <ul>
	  <li><a <?php  echo $menu_home; ?> href="../home">Home</a></li>
      <?php  if($_SESSION['asesor_indiv']==1 || $_SESSION['asesor_formularios_us']==1 || $_SESSION['asesor_cuartiles']==1 || $_SESSION['asesor_formulario_fcr']==1 || $_SESSION['asesor_formulario_ag']==1 || $_SESSION['asesor_formularios_bo']==1 || $_SESSION['asesor_formulario_mt']==1 || $_SESSION['asesor_formulario_mp']==1){}else{goto Menu_Next_Menu_Asesores;} ?>
      <li><a <?php  echo $menu_asesores; ?> href="#contact">Asesores</a>
        <ul>
	  		<?php  if($_SESSION['asesor_cuartiles']!=1){goto Menu_Next_asesor_cuartiles;} ?>
	  		<li><a href="../asesor-cuartiles">Cuartiles</a></li>
            <?php  Menu_Next_asesor_cuartiles: ?>
            <?php  if($_SESSION['asesor_formulario_ag']!=1){goto Menu_Next_asesor_formulario_ag;} ?>
	  		<li><a>Formularios<br>Agencias</a>
                <ul>
                    <li><a href="../ag-tipificacion">Tipificacion<br>de Llamadas</a></li>
                </ul>
            </li>
            <?php  Menu_Next_asesor_formulario_ag: ?>
            <?php  if($_SESSION['asesor_formularios_bo']!=1){goto Menu_Next_asesor_formularios_bo;} ?>
	  		<li><a>Formularios<br>BackOffice</a>
                <ul>
                    <li><a href="../bo-formularios-confirming">Confirming</a></li>
                    <li><a href="../bo-formularios-mailing">Mailing</a></li>
                    <li><a href="../bo-formularios-mejora">Mejora Continua</a></li>
                    <li><a href="../bo-formularios-reembolsos">Reembolsos</a></li>
                </ul>
            </li>
            <?php  Menu_Next_asesor_formularios_bo: ?>
            <?php  if($_SESSION['asesor_tipificacion_sac']!=1){goto Menu_Next_asesor_tipificacion_sac;} ?>
	  		<li><a>Formularios<br>Servicio a Clientes</a>
                <ul>
                    <li><a href="../sac-tipificacion">Tipificaci�n de Llamadas</a></li>
                    <?php  if($_SESSION['asesor_cuartiles']!=1){goto Menu_Next_asesor_cuartiles_reportetipificacion;} ?>
			  			<li><a href="../sac-tipificacion/reporte.php">Reporte de Tipificacion</a></li>
		            <?php  Menu_Next_asesor_cuartiles_reportetipificacion: ?>
                </ul>
            </li>
            <?php  Menu_Next_asesor_tipificacion_sac: ?>
            <?php  if($_SESSION['asesor_formulario_mt']!=1 && $_SESSION['asesor_formulario_mp']!=1){goto Menu_Next_asesor_formulario_trf;} ?>
	  		<li><a>Formularios<br>Trafico</a>
                <ul>
                    <?php  if($_SESSION['asesor_formulario_mp']!=1){goto Menu_Next_asesor_formulario_mp;} ?>
                    <li><a href="../trf-formulario-mp">Trafico MP</a></li>
                    <?php  Menu_Next_asesor_formulario_mp: ?>
                    <?php  if($_SESSION['asesor_formulario_mt']!=1){goto Menu_Next_asesor_formulario_mt;} ?>
                    <li><a href="../trf-formulario-mt">Actividades Trafico MT</a></li>
                    <li><a href="../trf-emisiones">Emisiones Trafico MT</a></li>
                    <li><a href="../trfMT-mejora">Mejora Continua Trafico MT</a></li>
                    <?php  Menu_Next_asesor_formulario_mt: ?>
                </ul>
            </li>
            <?php  Menu_Next_asesor_formulario_trf: ?>
            <?php  if($_SESSION['asesor_formulario_us']!=1){goto Menu_Next_asesor_formulario_us;} ?>
	  		<li><a>Formularios<br>Upsell</a>
                <ul>
                    <li><a href="../us-formulario-motivo">Motivos</a></li>
                </ul>
            </li>
            <?php  Menu_Next_asesor_formulario_us: ?>
            <?php  if($_SESSION['asesor_tipificacion_ventas']!=1){goto Menu_Next_asesor_formulario_ventas;} ?>
	  		<li><a>Formularios<br>Ventas</a>
                <ul>
                    <li><a href="../ventas-tipificacion">Tipificacion<br>de Llamadas</a></li>
                </ul>
            </li>
            <?php  Menu_Next_asesor_formulario_ventas: ?>


	  	</ul>
	  </li>
      <?php  Menu_Next_Menu_Asesores: ?>
      <?php  if($_SESSION['tablas_f']==1 || $_SESSION['reporte_copa']==1 || $_SESSION['reportes_aleatoriedad'] || $_SESSION['reportes_localizadores'] || $_SESSION['tablas_all']==1 || $_SESSION['tabla_precision_ib']){}else{goto Menu_Next_Menu1;} ?>
      <li><a <?php  echo $menu_tablas; ?> href="#contact">Reportes</a>
        <ul>
	  		<?php  if($_SESSION['reportes_aleatoriedad']!=1){goto Menu_Next_reportes_aleatoriedad;} ?>
	  		<li><a href="../aleatoriedad">Aleatoriedad</a></li>
            <?php  Menu_Next_reportes_aleatoriedad: ?>
	  		<?php  if($_SESSION['reportes_localizadores']!=1){goto Menu_Next_reportes_localizadores;} ?>
	  		<li><a href="../reporte-localizadores">Localizadores</a></li>
            <?php  Menu_Next_reportes_localizadores: ?>
            <?php  if($_SESSION['reporte_copa']!=1){goto Menu_Next_reporte_copa;} ?>
	  		<li><a href="../tablaCopa">Reporte Copa</a></li>
            <?php  Menu_Next_reporte_copa: ?>
            <?php  if($_SESSION['tablas_f']!=1){goto Menu_Next_TablaF;} ?>
	  		<li><a href="../tablaf">Tabla F</a></li>
            <?php  Menu_Next_TablaF: ?>
	  		<?php  if($_SESSION['tablas_all']!=1){goto Menu_Next_TablaAll;} ?>
            <li><a href="../tabla-all">Todo</a></li>
            <?php  Menu_Next_TablaAll: ?>
            <?php  if($_SESSION['tabla_precision_ib']!=1){goto Menu_Next_tabla_precision_ib;} ?>
            <li><a href="../prec-pronostico">Precision de<br>Pronosticos IB</a></li>
            <?php  Menu_Next_tabla_precision_ib: ?>
	  	</ul>
	  </li>
      <?php  Menu_Next_Menu1: ?>
	  <?php  if($_SESSION['monitor_y_lw']==1 ||
        $_SESSION['monitor_gtr']==1 ||
        $_SESSION['monitor_pya']==1){}else{goto Menu_Next_Menu2;} ?>
      <li><a <?php  echo $menu_monitores; ?> href="#about">Monitores</a>
	  	<ul>
            <?php  if($_SESSION['monitor_y_lw']!=1){goto Menu_Next_monitor_y_lw;} ?>
            <li><a href="../monitor-volumen">Volumen Llamadas (Y & LW)</a></li>
	  		<?php  Menu_Next_monitor_y_lw: ?>
            <?php  if($_SESSION['monitor_gtr']!=1){goto Menu_Next_monitor_gtr;} ?>
            <li><a href="../monitor-gtr">GTR</a></li>
	  		<?php  Menu_Next_monitor_gtr: ?>
            <?php  if($_SESSION['monitor_pya']!=1){goto Menu_Next_monitor_pya;} ?>
            <li><a href="../pya-monitor">PyA</a></li>
            <li><a href="../online_users">Asesores Online</a></li>
            <?php  Menu_Next_monitor_pya: ?>
            <?php  if($_SESSION['monitor_gtr']!=1){goto Menu_Next_monitor_sla;} ?>
            <li><a href="../slas-all">SLA</a></li>
            <?php  Menu_Next_monitor_sla: ?>
	  	</ul>
	  </li>
      <?php  Menu_Next_Menu2: ?>
      <?php  if($_SESSION['schedules_query']==1 ||
        $_SESSION['retardos']==1 ||
        $_SESSION['schedules_upload']==1 ||
        $_SESSION['schedules_change']==1 ||
        $_SESSION['schedules_diaspendientes']==1 ||
        $_SESSION['schedules_selectSpecial']==1 ||
        $_SESSION['sup_asign']==1 ||
        $_SESSION['payroll']==1 ||
        $_SESSION['monitor_pya']==1){}else{goto Menu_Next_Menu3;} ?>
	  <li><a <?php  echo $menu_programaciones; ?>>Programaciones</a>
	  	<ul>
	  		<li><a>Horarios</a>
	  			<ul>
                    <?php  if($_SESSION['schedules_query']!=1){goto Menu_Next_schedules_query;} ?>
                    <li><a href="../horarios-consulta">Consulta</a></li>
			  		<?php  Menu_Next_schedules_query: ?>
                    <?php  if($_SESSION['retardos']!=1){goto Menu_Next_retardos;} ?>
                    <li><a href="../retardos_sa">Retardos y Salidas Anticipadas</a></li>
			  		<?php  Menu_Next_retardos: ?>
                    <?php  if($_SESSION['schedules_upload']!=1){goto Menu_Next_schedules_upload;} ?>
                    <li><a href="../horarios-upload">Subir Horarios</a></li>
                    <li><a href="../editar-horarios">Editar Horarios</a></li>
			  		<?php  Menu_Next_schedules_upload: ?>
                    <?php  if($_SESSION['schedules_change']!=1){goto Menu_Next_ausentismos;} ?>
			  		<li><a href="../ausentismos">Registrar Ausentismos</a></li>
                    <li><a href="../mopers-pendientes">Mopers Pendientes</a></li>
			  		<?php  Menu_Next_ausentismos: ?>
                    <?php  if($_SESSION['schedules_change']!=1){goto Menu_Next_schedules_change;} ?>
			  		<li><a href="../cambios">Cambios</a></li>
			  		<?php  Menu_Next_schedules_change: ?>
                    <?php  if($_SESSION['schedules_diaspendientes']!=1){goto Menu_Next_schedules_diaspendientes;} ?>
			  		<li><a href="../dias-pendientes">Dias Pendientes/Redimidos</a></li>
			  		<?php  Menu_Next_schedules_diaspendientes: ?>
                    <?php  if($_SESSION['schedules_selectSpecial']!=1){goto Menu_Next_schedules_selectSpecial;} ?>
			  		<li><a href="../seleccionDiciembre">Seleccion Horarios Dic</a></li>
                    <?php  Menu_Next_schedules_selectSpecial: ?>
	  			</ul>
	  		</li>
	  		<?php  if($_SESSION['sup_asign']!=1){goto Menu_Next_sup_asign;} ?>
			<li><a href="../sup_asign">Asignacion de<br>Supervisor</a></li>
	  		<?php  Menu_Next_sup_asign: ?>
            <?php  if($_SESSION['validate_retardos']!=1){goto Menu_Next_validate_retardos;} ?>
			<li><a href="../validacion-retardos">Validacion<br>Incidencias</a></li>
	  		<?php  Menu_Next_validate_retardos: ?>
            <?php  if($_SESSION['monitor_pya']!=1){goto Menu_Next_payroll;} ?>
			<li><a href="../asistencia">Asistencia</a></li>
            <?php  Menu_Next_payroll: ?>
	  	</ul>
	  </li>
      <?php  Menu_Next_Menu3: ?>
	   <?php  if($_SESSION['upload_info']!=1){goto Menu_Next_upload_info;} ?>
	  	<li><a <?php  echo $menu_uploads; ?>>Uploads</a>
	  	<ul>
            <li><a href='../upl/testcalls.php'>Llamadas de Prueba</a></li>
            <li><a href='../upl/calls_upload.php'>Llamadas</a></li>
	  		<li><a href='../upl/xfer_check.php'>Validar Transferidas</a></li>

	  	</ul>
	  </li>
      <?php  Menu_Next_upload_info: ?>
	  <?php  if($_SESSION['config']!=1){goto Menu_Next_config;} ?>
	  	<li><a <?php  echo $menu_configuracion; ?>>Configuracion</a>
	  	<ul>
	  		<li><a href='../config/adduser.php'>Editar/Crear<br>Usuarios/Asesores</a></li>
        </ul>
	  </li>
      <?php  Menu_Next_config: ?>
	  <?php  if($log!=1){goto NoSession;} ?>
	  <li><a $menu_sesion>Sesi&oacuten</a>
        <ul>
        <li><a href='../common/password.php'>Cambiar Contrase&ntildea</a></li>
        <li><a href='../common/logout.php'>LogOut</a></li>
        </ul>
      </li>
      <?php  NoSession: ?>
	</ul>
  </li>
</ul>
</nav>

</div>
<div style="margin-left:50px; width:95%">

<?php if(isset($_SESSION[$credential]) && $_SESSION[$credential]!='1'){ echo $noProfile; exit;}

?>