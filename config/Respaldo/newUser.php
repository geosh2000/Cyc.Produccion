<?php
include("../modules/modules.php");

$connectdb=Connection::mysqliDB('CC');
timeAndRegion::setRegion('Cun');

$normalizeChars = array(
	    'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
	    'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
	    'Ï'=>'I', 'Ñ'=>'N', 'Ń'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
	    'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
	    'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
	    'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ń'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
	    'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f',
	    'ă'=>'a', 'î'=>'i', 'â'=>'a', 'ș'=>'s', 'ț'=>'t', 'Ă'=>'A', 'Î'=>'I', 'Â'=>'A', 'Ș'=>'S', 'Ț'=>'T',
	);


    //SAVE
    $nombre=utf8_decode($_POST['nombre']);
    $apellido=utf8_decode($_POST['apellido']);
    $nombre_completo="$nombre $apellido";
    $ncorto=utf8_decode(strtr($_POST['ncorto'], $normalizeChars));

    //delete double spaces
    $nombre=str_replace("  "," ",$nombre);
    $nombre_completo=str_replace("  "," ",$nombre_completo);
    $ncorto=str_replace("  "," ",$ncorto);

    //delete extra spaces
    while(substr($nombre_completo,-1,1)==" "){
      $nombre_completo=substr($nombre_completo, 0, -1);
    }

    while(substr($ncorto,-1,1)==" "){
      $ncorto=substr($ncorto, 0, -1);
    }

    while(substr($nombre,-1,1)==" "){
      $nombre=substr($nombre, 0, -1);
    }

    $usuario=strtolower(str_replace(" ",".",$ncorto));
    $departamento=$_POST['departamento'];
    $puesto=$_POST['puesto'];



    if($_POST['num_colaborador']==""){
      $numcol='NULL';
    }else{
      $numcol=$_POST['num_colaborador'];
    }

    IF($_POST['sup']==''){
      $sup="NULL";;
    }else{
      $sup=$_POST['sup'];
    }

    IF($_POST['pdv']==''){
      $pdv="NULL";;
    }else{
      $pdv=$_POST['pdv'];
    }

    if($_POST['ciudad']==""){
      $ciudad='NULL';
    }else{
      $ciudad=$_POST['ciudad'];
    }

    if($_POST['activo']=='true'){
      $activo=1;
    }else{
      $activo=0;
    }

    $ingreso=date('Y-m-d',strtotime($_POST['ingreso']));
    $esquema=$_POST['esquema'];
    $profile=$_POST['profile'];
    $plaza=$_POST['plaza'];

    $flag=true;

    $td['status']['AsesoresDB']="NA";
    $td['msg']['AsesoresDB']="NA";
    $td['status']['userDB']="NA";
    $td['msg']['userDB']="NA";
    $td['status']['supsDB']="NA";
    $td['msg']['supsDB']="NA";
    $td['status']['puestoDB']="NA";
    $td['msg']['puestoDB']="NA";
    $td['status']['pdvDB']="NA";
    $td['msg']['pdvDB']="NA";

    $query="INSERT INTO Asesores (Nombre, Nombre_Separado, Apellidos_Separado,`N Corto`,`id Departamento`, Activo,Ingreso,Egreso,Usuario,Esquema, num_colaborador, puesto, ciudad, plaza)
            VALUES ('$nombre_completo','$nombre','$apellido','$ncorto','$departamento','$activo','$ingreso','2030-12-31','$usuario','$esquema',$numcol, $puesto, $ciudad, $plaza)";
    if($result=$connectdb->query($query)){
      $td['status']['AsesoresDB']=1;
      $td['msg']['AsesoresDB']=utf8_encode('Registro Exitoso');
      $id=$connectdb->insert_id;
      $pass=password_hash('pricetravel2016', PASSWORD_BCRYPT);

      $query="INSERT INTO userDB (username, hashed_pswd, profile, asesor_id, active) VALUES ('$usuario','$pass','$profile','$id','1')";
      if($result=$connectdb->query($query)){
        $userid=$connectdb->insert_id;
        $td['status']['userDB']=1;
        $td['msg']['userDB']=utf8_encode('Registro Exitoso');
        $query="INSERT INTO Supervisores (Fecha,asesor, supervisor) VALUES ('$ingreso','$id',$sup)";
        if($result=$connectdb->query($query)){
          $supid=$connectdb->insert_id;
          $td['status']['supsDB']=1;
          $td['msg']['supsDB']=utf8_encode('Registro Exitoso');

          $query="INSERT INTO asesores_puesto (asesor, fecha, departamento, puesto) VALUES ('$id', '$ingreso','$departamento', $puesto )";
          if($result=$connectdb->query($query)){
            $puestoid=$connectdb->insert_id;
            $td['status']['puestoDB']=1;
            $td['msg']['puestoDB']=utf8_encode('Registro Exitoso');

            $query="UPDATE asesores_movimiento_vacantes SET asesor_in=$id, fecha_in='$ingreso' WHERE vacante=$plaza AND fecha_in IS NULL";
              if($result=$connectdb->query($query)){
                $puestoid=$connectdb->insert_id;
                $td['status']['pdvDB']=1;
                $td['msg']['pdvDB']=utf8_encode('Registro Exitoso');

              }else{
                $flag=false;
                $td['status']['pdvDB']=0;
                $td['msg']['pdvDB']=utf8_encode("Error puestoDB -> ".$connectdb->error." ON $query");
              }


          }else{
            $flag=false;
            $td['status']['puestoDB']=0;
            $td['msg']['puestoDB']=utf8_encode("Error puestoDB -> ".$connectdb->error." ON $query");
          }

        }else{
          $flag=false;
          $td['status']['supsDB']=0;
          $td['msg']['supsDB']=utf8_encode("Error puestoDB -> ".$connectdb->error." ON $query");
        }

      }else{
        $flag=false;
        $td['status']['userDB']=0;
        $td['msg']['userDB']=utf8_encode("Error puestoDB -> ".$connectdb->error." ON $query");
      }

    }else{
      $flag=false;
      $td['status']['AsesoresDB']=0;
      $td['msg']['AsesoresDB']=utf8_encode("Error puestoDB -> ".$connectdb->error." ON $query");
    }

    if($flag){
      $td['global']=1;
    }else{
      if($td['status']['userDB']==0 || $td['status']['supsDB']==0 || $td['status']['puestoDB']==0){
        $query="DELETE FROM Asesores WHERE id=$id";
        $connectdb->query($query);
      }

      if($td['status']['supsDB']==0 || $td['status']['puestoDB']==0){
        $query="DELETE FROM userDB WHERE userid=$userid";
        $connectdb->query($query);
      }

      if($td['status']['puestoDB']==0){
        $query="DELETE FROM Supervisores WHERE rel_sup_id=$userid";
        $connectdb->query($query);
      }

      $td['global']=0;
    }



$connectdb->close();

echo json_encode($td,JSON_PRETTY_PRINT);

?>
