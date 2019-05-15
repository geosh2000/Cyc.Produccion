<?php
	header('Content-Type: text/html; charset=utf-8');
    include('../connectDB.php');

    //get search term
    $searchTerm = $_POST['term'];

    //get matched data from skills table
    $query = "SELECT 
				* 
			FROM 
				(
					SELECT c.id as mg_id, b.id as me_id, a.id as d_id, CONCAT('<a style=\"color: #cc0052\">',c.Motivo_General,'</a> => <a style=\"color: #e65c00\">', b.Motivo_Especifico,'</a> => <a style=\"color: #2929a3\">',a.Detalle) as Detalle,
					keywords, c.Motivo_General, b.Motivo_Especifico, a.Detalle as Detail, Activo 
					FROM 
						sac_detalle a 
					LEFT JOIN 
						sac_motivos_especificos b ON a.motivo_especifico=b.id
					LEFT JOIN
						sac_motivos_generales c ON b.motivo_general=c.id
				) a WHERE (Detalle LIKE '%$searchTerm%' OR keywords LIKE '%$searchTerm%') AND
					Activo=1
				ORDER BY Motivo_General, Motivo_Especifico, Detail";
    $result=mysql_query($query);
    $num=mysql_numrows($result);
    $i=0;
	echo "-t-$num-t- ";
    while ($i<$num) {
    	echo "-mg$i-".mysql_result($result,$i,'mg_id')."-mg$i- "
    		."-me$i-".mysql_result($result,$i,'me_id')."-me$i- "
    		."-d$i-".mysql_result($result,$i,'d_id')."-d$i- "
    		."-route$i-".utf8_encode(mysql_result($result,$i,'Detalle'))."-route$i- ";
    $i++;
    }

?>