<?php

//Check existing registries
	$query="SELECT * FROM Ausentismos WHERE asesor=$asesor AND (((Inicio<='$datestart' AND Fin>='$datestart') OR  (Inicio<='$dateend' AND Fin>='$dateend')) OR (Inicio>'$datestart' AND Fin<'$dateend'))";
	$result=mysql_query($query);
	$num=mysql_numrows($result);
	
	
	if($num==0){

        if($ausentismo==12){
            $i=date('Y-m-d',strtotime($datestart));
            while(strtotime($i)<=strtotime($dateend)){
                $qsearch="SELECT id FROM `Historial Programacion` WHERE asesor='$asesor' AND Fecha='$i'";
                $tmp_id=mysql_result(mysql_query($qsearch),0,'id');
                $qret="DELETE FROM PyA_Exceptions WHERE horario_id='$tmp_id'";
                mysql_query($qret);
            $i=date('Y-m-d', strtotime($i.'+1 days'));
           
            }

        }
		$query="INSERT INTO Ausentismos (asesor,tipo_ausentismo,Inicio,Fin,Descansos,Beneficios,User,caso,Moper,ISI,Comments) VALUES ('$asesor','$ausentismo','".date('Y-m-d',strtotime($datestart))."','".date('Y-m-d',strtotime($dateend))."','$descansos','$beneficios','".$_SESSION['id']."','$caso','$moper','$isi','$comment')";
		mysql_query($query);
        if(mysql_error()){
            echo "<table class='tred' width='100%'>\n
			\t\t<tr class='title'>\n
				 \t\t<th>Error! La base de datos no se actualizo</th>\n
			\t</tr>\n
			</table>\n";
        }else{
                $ausid=mysql_insert_id();
                if($ausentismo==5){
        			$queryUpload = "INSERT INTO `Dias Pendientes Redimidos` (indice,id,dias,day,month,year,motivo,caso,User,id_ausentismo) VALUES (NULL,'$asesor','$dias','".date('d',strtotime('now'))."','".date('m',strtotime('now'))."','".date('Y',strtotime('now'))."','$motivo','$caso','".$_SESSION['id']."','$ausid')";
        		mysql_query($queryUpload);
        		}
            echo "<table class='t2' width='100%'>\n
			\t\t<tr class='green' style='color:white'>\n
				 \t\t<th>Registro Exitoso para $asesor_name - $ausentismo_name</th>\n
			\t</tr>\n
			</table>\n";
        }
		
		if($ausentismo==5){
			$queryUpload = "INSERT INTO `Dias Pendientes Redimidos` (indice,id,dias,day,month,year,motivo,caso,User,id_ausentismo) VALUES (NULL,'$asesor','$dias','".date('d',strtotime('now'))."','".date('m',strtotime('now'))."','".date('Y',strtotime('now'))."','$motivo','$caso','".$_SESSION['id']."')";
		mysql_query($queryUpload);
		}

		
		
	}else{
		echo "<table class='tred' width='100%'>\n
			\t\t<tr class='title'>\n
				 \t\t<th>Error! Existen fechas asignadas para este usuario que se sobreponen con las elegidas</th>\n
			\t</tr>\n
			</table>\n";
		
	}



?>