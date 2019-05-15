<?php
include("connectDB.php");
header("Content-Type: text/html;charset=utf-8");

$i=0;
while($i<=100){
	if(!isset($_POST['a'.$i.'b1'])){$i++; continue;}
	$data[$i]['Nombre']=$_POST['a'.$i.'b1'];
	$data[$i]['usuario']=$_POST['a'.$i.'b2'];
	$data[$i]['ingreso']=date('Y-m-d',strtotime($_POST['a'.$i.'b3']));
	$data[$i]['ncorto']=$_POST['a'.$i.'b4'];
	
$i++;
}

$query="SELECT a.id, Nombre, usuario, username, b.profile, b.asesor_id FROM Asesores a LEFT JOIN userDB b ON a.Usuario=b.username WHERE `id Departamento` IN (28,29,30) AND username IS NULL";
$result=mysql_query($query);
$num=mysql_numrows($result);
for($i=0;$i<$num;$i++){
	$data[$i]['id']=mysql_result($result, $i, 'id');
	$data[$i]['user']=mysql_result($result, $i, 'usuario');
}

foreach($data as $index => $info){
	$query="INSERT INTO Asesores (Nombre, Usuario, Ingreso, `N Corto`, `id Departamento`, Activo, Egreso, Esquema) VALUES ("
			."'".$info['Nombre']."', "
			."'".$info['usuario']."', "
			."'".$info['ingreso']."', "
			."'".$info['ncorto']."', "
			."29,"
			."1,"
			."'2999-12-31',"
			."8)";
	mysql_query($query);
	if(mysql_error()){
		$query="INSERT INTO Errores (site, error, consulta) VALUES ('create_asesor','".mysql_errno()."','".mysql_real_escape_string(mysql_error())."')";
		mysql_query($query);
		if(mysql_error()){
			echo mysql_error();
		}
	}else{
		$id=mysql_insert_id();
		$query="INSERT INTO userDB (username, password, profile, asesor_id, active) VALUES ("
				."'".$info['usuario']."', "
				."'pricetravel2016', "
				."16, "
				."'$id', "
				."1)";
		mysql_query($query);
		if(mysql_error()){
			$query="INSERT INTO Errores (site, error, consulta) VALUES ('create_user','".mysql_errno()."','".mysql_real_escape_string(mysql_error())."')";
			mysql_query($query);
			if(mysql_error()){
				echo mysql_error();
			}
		}
	}
}

echo "DONE";



?>