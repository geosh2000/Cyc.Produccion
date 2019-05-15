<?php ?>
<?php
include("../connectDB.php");
//header("Content-Type: text/html;charset=utf-8");
$tipo=$_POST['md'];

$i=0;
while($i<=100){
	if(!isset($_GET['a'.$i.'b1'])){$i++; continue;}
	$x=1;
	$data[$i]['Nombre']=str_replace('Ã¡', 'á', $_GET['a'.$i.'b1']);
	$data[$i]['Nombre']=str_replace('Ã©', 'é', $data[$i]['Nombre']);
	$data[$i]['Nombre']=str_replace('Ã­', 'í', $data[$i]['Nombre']);
	$data[$i]['Nombre']=str_replace('Ã³', 'ó', $data[$i]['Nombre']);
	$data[$i]['Nombre']=str_replace('Ãº', 'ú', $data[$i]['Nombre']);
	$data[$i]['usuario']=utf8_encode($_GET['a'.$i.'b2']);
	$data[$i]['ingreso']=date('Y-m-d', strtotime($_GET['a'.$i.'b3']));
	$data[$i]['ncorto']=utf8_encode($_GET['a'.$i.'b4']);
$i++;
}

$db="Asesores";

if($_GET['tipo']=='baja'){
	foreach($data as $key => $info){
		mysql_query("SET NAMES 'utf8'"); 
		$query="UPDATE Asesores SET Activo=0, Egreso='".$info['ingreso']."' WHERE Nombre='".$info['Nombre']."'";
		mysql_query($query);
		$query="UPDATE userDB SET active=0 WHERE username='".$info['usuario']."'";
		mysql_query($query);
		echo "BAJA OK";
		exit;
	}
}
		
foreach($data as $key => $info){
	mysql_query("SET NAMES 'utf8'"); 
	$query="INSERT INTO Asesores (Nombre, Usuario, `N Corto`, Ingreso, Egreso, Activo, `id Departamento`)"
			."VALUES ("
			."'".$info['Nombre']."', "
			."'".$info['usuario']."', "
			."'".$info['ncorto']."', "
			."'".$info['ingreso']."', "
			."'2999-12-31', "
			."1, "
			."29"
			.")";
	mysql_query($query);
	if(mysql_error()){
		if(mysql_errno()==1062){
			$query="UPDATE Asesores SET Usuario='".$info['usuario']."', Ingreso='".$info['ingreso']."', `N Corto`='".$info['ncorto']."', Activo=1, `id Departamento`=29"
					." WHERE Nombre='".$info['Nombre']."'";
			mysql_query($query);
			if(mysql_error()){
				$query="INSERT INTO Errores (site, error, consulta) VALUES ('asesores_update','".mysql_errno()."','".mysql_real_escape_string(mysql_error())."')";
				mysql_query($query);
			}else{
				$query="SELECT id FROM Asesores WHERE Nombre='".$info['Nombre']."'";
				$id=mysql_result(mysql_query($query),0,'id');
				mysql_query("SET NAMES 'utf8'"); 
				$query="INSERT INTO userDB (username, password, profile, asesor_id, active) VALUES ("
					."'".$info['usuario']."', "
					."'pricetravel2016', "
					."16, "
					."'".$id."', "
					."1 "
					.")";
				mysql_query($query);
				if(mysql_error()){
					$query="INSERT INTO Errores (site, error, consulta) VALUES ('asesores_upload_user_updated','".mysql_errno()."','".mysql_real_escape_string(mysql_error())."')";
					mysql_query($query);
					if(mysql_error()){
						echo mysql_error();
					}
				}	
			}
			
		}else{
			$query="INSERT INTO Errores (site, error, consulta) VALUES ('asesores_upload','".mysql_errno()."','".mysql_real_escape_string(mysql_error())."')";
			mysql_query($query);
			if(mysql_error()){
				echo mysql_error();
			}
		}
	}else{
		$id=mysql_insert_id();
		mysql_query("SET NAMES 'utf8'"); 
		$query="INSERT INTO userDB (username, password, profile, asesor_id, active) VALUES ("
			."'".$info['usuario']."', "
			."'pricetravel2016', "
			."16, "
			."'".$id."', "
			."1 "
			.")";
		mysql_query($query);
		if(mysql_error()){
			$query="INSERT INTO Errores (site, error, consulta) VALUES ('asesores_upload_user','".mysql_errno()."','".mysql_real_escape_string(mysql_error())."')";
			mysql_query($query);
			if(mysql_error()){
				echo mysql_error();
			}
		}
	}
}

echo "¡Updated!!";











?>