<?php







$query="SELECT

		Fechas.Fecha,

		COUNT(a.confirming_id) as RecievedMailsTotal,

		COUNT(IF(a.actividad!=8,a.confirming_id,NULL)) as RealMailsTotal,

		COUNT(IF(a.actividad=8,a.confirming_id,NULL)) as SPAMMailsTotal,

		CAST(COUNT(IF(a.actividad!=8 AND DuracionCicloCasoUfBo(a.em,2)<=1440,a.confirming_id,NULL)) / COUNT(IF(a.actividad!=8,a.confirming_id,NULL)) as DECIMAL(5,2)) as PuntualidadMailUfTotal,

        RecievedProcessTotal, RealProcessTotal, SPAMProcessTotal, AsesoresMailingTotal,

		ConfirmingProcessTotal, ConfirmingClientesTotal, ConfirmingProveedoresTotal, AsesoresConfirmingTotal,

		CAST(UfMailXHoraTotal as DECIMAL(4,2)) AS UfMailXHoraTotal,

		VolumenMCTotal,

		PuntualidadMCUfTotal,

		MCProcessTotal, AsesoresMCTotal



	FROM

		Fechas

	LEFT JOIN

		#casosxfecha

		bo_mailing a

	ON

		Fechas.Fecha=a.fecha_recepcion

	LEFT JOIN

		#mcxfecha

		(

			SELECT

				fecha_recepcion, COUNT(mejora_id) as VolumenMCTotal, CAST(COUNT(IF(DuracionCicloCasoUfBo(em,3)<=4320,mejora_id,NULL)) / COUNT(mejora_id) as DECIMAL(5,2)) as PuntualidadMCUfTotal

				FROM

					bo_mejora_continua

				GROUP BY

					fecha_recepcion

		) mc_a

	ON

		Fechas.Fecha=mc_a.fecha_recepcion

	LEFT JOIN

    	#MailingProcesados

		(

			SELECT

				CAST(CONCAT(YEAR(date_created),'-',MONTH(date_created),'-',DAY(date_created)) as DATE) as Fecha,

				COUNT(confirming_id) as RecievedProcessTotal,

				COUNT(IF(actividad!=8,confirming_id,NULL)) as RealProcessTotal,

				COUNT(IF(actividad=8,confirming_id,NULL)) as SPAMProcessTotal,

				COUNT(DISTINCT `user`) as AsesoresMailingTotal



			FROM

				bo_mailing

			GROUP BY

				Fecha

		) b

	ON

		a.fecha_recepcion=b.Fecha

	LEFT JOIN

    	#ConfirmingProcesados

		(

			SELECT

				CAST(CONCAT(YEAR(date_created),'-',MONTH(date_created),'-',DAY(date_created)) as DATE) as Fecha,

				COUNT(confirming_id) as ConfirmingProcessTotal,

				COUNT(IF(actividad=13,confirming_id,NULL)) as ConfirmingClientesTotal,

				COUNT(IF(actividad=14,confirming_id,NULL)) as ConfirmingProveedoresTotal,

				COUNT(DISTINCT `user`) as AsesoresConfirmingTotal



			FROM

				bo_confirming

			GROUP BY

				Fecha

		) confirming

	ON

		a.fecha_recepcion=confirming.Fecha

	LEFT JOIN

		(

			SELECT FechaOK as Fecha, AVG(Transacciones) as UfMailXHoraTotal FROM

				(SELECT

						`N Corto` as Asesor,

						CAST(CONCAT(YEAR(date_created),'-',MONTH(date_created),'-',DAY(date_created)) as DATE) as FechaOK,

						DAYNAME(CAST(CONCAT(YEAR(date_created),'-',MONTH(date_created),'-',DAY(date_created)) as DATE)) as DOW,

						HOUR(date_created) as hora,

						COUNT(*) as Transacciones

					FROM

						bo_mailing a,

						userDB b,

						Asesores c

					WHERE

						a.`user`=b.userid AND

						b.username=c.Usuario AND

						actividad!=8

					GROUP BY

						FechaOK, Asesor, hora) a

			GROUP BY

			FechaOK ) Ufporhora

	ON

		a.fecha_recepcion=Ufporhora.Fecha

	LEFT JOIN

		#MejoraProcesados

		(

			SELECT

				CAST(CONCAT(YEAR(date_created),'-',MONTH(date_created),'-',DAY(date_created)) as DATE) as Fecha,

				COUNT(mejora_id) as MCProcessTotal,

				COUNT(DISTINCT `user`) as AsesoresMCTotal

			FROM

				bo_mejora_continua

			GROUP BY

				Fecha

		) mc

	ON

		a.fecha_recepcion=mc.Fecha

	WHERE

		Fechas.Fecha>='$from' AND

		Fechas.Fecha<='$to'

	GROUP BY

		Fechas.Fecha";

$query_ac="SELECT SUM(RecievedMailsTotal) as RecievedMailsTotal, SUM(RealMailsTotal) as RealMailsTotal, SUM(SPAMMailsTotal) as SPAMMailsTotal, CAST(SUM(divmail)/SUM(divmail2) as DECIMAL(5,2)) as PuntualidadMailUfTotal, SUM(RecievedProcessTotal) as RecievedProcessTotal, SUM(RealProcessTotal) as RealProcessTotal, SUM(SPAMProcessTotal) as SPAMProcessTotal, AVG(AsesoresMailingTotal) as AsesoresMailingTotal, SUM(ConfirmingProcessTotal) as ConfirmingProcessTotal, SUM(ConfirmingClientesTotal) as ConfirmingClientesTotal, SUM(ConfirmingProveedoresTotal) as ConfirmingProveedoresTotal, AVG(AsesoresConfirmingTotal) as AsesoresConfirmingTotal, AVG(UfMailXHoraTotal) as UfMailXHoraTotal, SUM(VolumenMCTotal) as VolumenMCTotal, SUM(div1)/SUM(VolumenMCTotal) as PuntualidadMCUfTotal, SUM(MCProcessTotal) as MCProcessTotal, SUM(AsesoresMCTotal) as AsesoresMCTotal, AVG(MCXHoraTotal) as MCXHoraTotal FROM

(SELECT Fechas.Fecha, COUNT(a.confirming_id) as RecievedMailsTotal, COUNT(IF(a.actividad!=8,a.confirming_id,NULL)) as RealMailsTotal, COUNT(IF(a.actividad=8,a.confirming_id,NULL)) as SPAMMailsTotal, COUNT(IF(a.actividad!=8 AND DuracionCicloCasoUfBo(a.em,2)<=1440,a.confirming_id,NULL)) as divmail, COUNT(IF(a.actividad!=8,a.confirming_id,NULL)) as divmail2, RecievedProcessTotal, RealProcessTotal, SPAMProcessTotal, AsesoresMailingTotal, ConfirmingProcessTotal, ConfirmingClientesTotal, ConfirmingProveedoresTotal, AsesoresConfirmingTotal, CAST(UfMailXHoraTotal as DECIMAL(4,2)) AS UfMailXHoraTotal, VolumenMCTotal, div1, MCProcessTotal, AsesoresMCTotal, MCXHoraTotal FROM Fechas LEFT JOIN

#casosxfecha
bo_mailing a ON Fechas.Fecha=a.fecha_recepcion LEFT JOIN

#mcxfecha
( SELECT fecha_recepcion, COUNT(mejora_id) as VolumenMCTotal, CAST(COUNT(IF(DuracionCicloCasoUfBo(em,3)<=4320,mejora_id,NULL)) / COUNT(mejora_id) as DECIMAL(5,2)) as PuntualidadMCUfTotal, COUNT(IF(DuracionCicloCasoUfBo(em,3)<=4320,mejora_id,NULL)) as div1 FROM bo_mejora_continua GROUP BY fecha_recepcion ) mc_a ON Fechas.Fecha=mc_a.fecha_recepcion LEFT JOIN

#MailingProcesados
( SELECT CAST(CONCAT(YEAR(date_created),'-',MONTH(date_created),'-',DAY(date_created)) as DATE) as Fecha, COUNT(confirming_id) as RecievedProcessTotal, COUNT(IF(actividad!=8,confirming_id,NULL)) as RealProcessTotal, COUNT(IF(actividad=8,confirming_id,NULL)) as SPAMProcessTotal, COUNT(DISTINCT `user`) as AsesoresMailingTotal FROM bo_mailing GROUP BY Fecha ) b ON a.fecha_recepcion=b.Fecha LEFT JOIN

#ConfirmingProcesados
( SELECT CAST(CONCAT(YEAR(date_created),'-',MONTH(date_created),'-',DAY(date_created)) as DATE) as Fecha, COUNT(confirming_id) as ConfirmingProcessTotal, COUNT(IF(actividad=13,confirming_id,NULL)) as ConfirmingClientesTotal, COUNT(IF(actividad=14,confirming_id,NULL)) as ConfirmingProveedoresTotal, COUNT(DISTINCT `user`) as AsesoresConfirmingTotal FROM bo_confirming GROUP BY Fecha ) confirming ON a.fecha_recepcion=confirming.Fecha LEFT JOIN

( SELECT FechaOK as Fecha, AVG(Transacciones) as UfMailXHoraTotal FROM (SELECT `N Corto` as Asesor, CAST(CONCAT(YEAR(date_created),'-',MONTH(date_created),'-',DAY(date_created)) as DATE) as FechaOK, DAYNAME(CAST(CONCAT(YEAR(date_created),'-',MONTH(date_created),'-',DAY(date_created)) as DATE)) as DOW, HOUR(date_created) as hora, COUNT(*) as Transacciones FROM bo_mailing a, userDB b, Asesores c WHERE a.`user`=b.userid AND b.username=c.Usuario AND actividad!=8 GROUP BY FechaOK, Asesor, hora) a GROUP BY FechaOK ) Ufporhora
ON a.fecha_recepcion=Ufporhora.Fecha LEFT JOIN

( SELECT FechaOK as Fecha, AVG(Transacciones) as MCXHoraTotal FROM (SELECT `N Corto` as Asesor, CAST(CONCAT(YEAR(date_created),'-',MONTH(date_created),'-',DAY(date_created)) as DATE) as FechaOK, DAYNAME(CAST(CONCAT(YEAR(date_created),'-',MONTH(date_created),'-',DAY(date_created)) as DATE)) as DOW, HOUR(date_created) as hora, COUNT(*) as Transacciones FROM bo_mejora_continua a, userDB b, Asesores c WHERE a.`user`=b.userid AND b.username=c.Usuario AND actividad!=8 GROUP BY FechaOK, Asesor, hora) a GROUP BY FechaOK ) UfMCporhora
ON a.fecha_recepcion=UfMCporhora.Fecha LEFT JOIN

#MejoraProcesados
( SELECT CAST(CONCAT(YEAR(date_created),'-',MONTH(date_created),'-',DAY(date_created)) as DATE) as Fecha, COUNT(mejora_id) as MCProcessTotal, COUNT(DISTINCT `user`) as AsesoresMCTotal, CAST(COUNT(IF(DuracionCicloCasoUfBo(em,2)<=1440,mejora_id,NULL)) / COUNT(mejora_id) as DECIMAL(5,2)) as PuntualidadMCTotal FROM bo_mejora_continua GROUP BY Fecha ) mc ON a.fecha_recepcion=mc.Fecha WHERE Fechas.Fecha>='$from' AND Fechas.Fecha<='$to' GROUP BY Fechas.Fecha) a ";

$result=mysql_query($query);

$num=mysql_numrows($result);

$numfield=mysql_num_fields($result);

$i=0;

while($i<$numfield){

    $field[$i]=mysql_field_name($result,$i);

$i++;

}

$i=0;

while($i<$num){

    $x=0;

     while($x<$numfield){

        $data[$field[$x]][$i]=mysql_result($result,$i,$field[$x]);

     $x++;

     }

     $TotalFechas=$i;

$i++;

}

$result=mysql_query($query_ac);
$num=mysql_numrows($result);
$numfield_ac=mysql_num_fields($result);
$i=0;
while($i<$numfield_ac){
    $field_ac[$i]=mysql_field_name($result,$i);
$i++;
}

$i=0;
while($i<$num){
   $x=0;
     while($x<$numfield_ac){

        $data_ac[$field_ac[$x]][$i]=mysql_result($result,$i,$field_ac[$x]);

     $x++;

     }


$i++;

}

?>



<div id="accordion">

  <h3>Informacion por dia</h3>

    <div>

    <table width='100%' class='tablesorter' id='tablesorter' style='text-align:center'>

    <thead>





<?php

    printRows('Fecha','M&eacutetrica','Canal','na','th');

?>

    </thead>

    <tbody>

    <?php

    printRows('RecievedMailsTotal','Volumen (Total)','Mailing','num');

    printRows('RealMailsTotal','Volumen (Sin SPAM)','Mailing','num');

    printRows('SPAMMailsTotal','SPAM','Mailing','num');

    printRows('PuntualidadMailUfTotal','Puntualidad','Mailing','%');

    printRows('RealProcessTotal','Procesados','Mailing','num');

    printRows('SPAMProcessTotal','Procesados (SPAM)','Mailing','num');

    printRows('AsesoresMailingTotal','Asesores','Mailing','num');

    printRows('UfMailXHoraTotal','Transacciones por Hora','Mailing','dec');

    printRows('ConfirmingProcessTotal','Procesados','Confirming','num');

    printRows('ConfirmingClientesTotal','Procesados (Cliente)','Confirming','num');

    printRows('ConfirmingProveedoresTotal','Procesados (Proveedor)','Confirming','num');

    printRows('AsesoresConfirmingTotal','Asesores','Confirming','num');

    printRows('VolumenMCTotal','Volumen','MC','num');

    printRows('PuntualidadMCUfTotal','Puntualidad','MC','%');

    printRows('MCProcessTotal','Procesados','MC','num');

    printRows('AsesoresMCTotal','Asesores','MC','num');







?>

            </tbody>

            </table>

        </div>

    <h3>Acumulado de fechas Seleccionadas</h3>

        <div>

            <table width='100%' id='acumulado' style='text-align: center'>

                <thead>

                <tr class='title' style='text-align: left'>

                    <th>Concepto</th>

                    <th>Area</th>

                    <th>Total</th>



                </tr>

                </thead>

                <tbody>

                <?php

                  printRows_ac_bo('RecievedMailsTotal','Volumen (Total)','Mailing','num');

    printRows_ac_bo('RealMailsTotal','Volumen (Sin SPAM)','Mailing','num');

    printRows_ac_bo('SPAMMailsTotal','SPAM','Mailing','num');

    printRows_ac_bo('PuntualidadMailUfTotal','Puntualidad','Mailing','%');

    printRows_ac_bo('RealProcessTotal','Procesados','Mailing','num');

    printRows_ac_bo('SPAMProcessTotal','Procesados (SPAM)','Mailing','num');

    printRows_ac_bo('AsesoresMailingTotal','Asesores','Mailing','num');

    printRows_ac_bo('UfMailXHoraTotal','Transacciones por Hora','Mailing','dec');

    printRows_ac_bo('ConfirmingProcessTotal','Procesados','Confirming','num');

    printRows_ac_bo('ConfirmingClientesTotal','Procesados (Cliente)','Confirming','num');

    printRows_ac_bo('ConfirmingProveedoresTotal','Procesados (Proveedor)','Confirming','num');

    printRows_ac_bo('AsesoresConfirmingTotal','Asesores','Confirming','num');

    printRows_ac_bo('MCXHoraTotal','Transacciones por Hora','MC','dec');

    printRows_ac_bo('VolumenMCTotal','Volumen','MC','num');

    printRows_ac_bo('PuntualidadMCUfTotal','Puntualidad','MC','%');

    printRows_ac_bo('MCProcessTotal','Procesados','MC','num');

    printRows_ac_bo('AsesoresMCTotal','Asesores','MC','num');



                ?>

            </tbody>

            </table>



        </div>



</div>

