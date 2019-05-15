<?php

include_once('../../modules/modules.php');

$connectdb=Connection::mysqliDB('CC');

$id=$_POST['id'];
$field=$_POST['field'];
$newVal="'".utf8_decode($_POST['newVal'])."'";
if($_POST['newVal']==""){$newVal="NULL";}

switch($field){
	case "Fecha_Nacimiento":
	case "Ingreso":
	case "Egreso":
	case "Vigencia_Visa":
	case "Vigencia_Pasaporte":
		timeAndRegion::setRegion('Cun');
		if($newVal!=""){
			$newVal="'".date('Y-m-d',strtotime($_POST['newVal']))."'";
		}else{
			$newVal='NULL';
		}
		break;
	default:
		break;
}


switch($field){
	case "pswd":
		$pass=password_hash($_POST['newVal'], PASSWORD_BCRYPT);
		$query="UPDATE userDB SET hashed_pswd='$pass' WHERE asesor_id=$id";
		break;
	case "profile":
		$query="UPDATE userDB SET profile=$newVal WHERE asesor_id= $id ";
		break;
	default:
		$query="UPDATE `Asesores` SET `$field`=$newVal WHERE id='$id'";
		$queryActive="UPDATE userDB SET active=$newVal WHERE asesor_id= $id ";
		break;
}


if($result=$connectdb->query($query)){
	if($field=="Activo"){
		if($result=$connectdb->query($queryActive)){
			$data['status']=1;
		}else{
			$data['status']=0;
			$data['msg']=utf8_encode("Guardado en DB Asesores || ERROR en userDB -> ".$connectdb->error." ON $queryActive");
		}
	}else{
		$data['status']=1;
	}

}else{
	$data['status']=0;
	$data['msg']=utf8_encode("ERROR! -> ".$connectdb->error." ON $query");
}

if($field=='Ingreso'){
	$query="SELECT id, departamento FROM asesores_puesto WHERE asesor=$id ORDER BY Fecha LIMIT 1";
	if($result=$connectdb->query($query)){
	  $fila=$result->fetch_assoc();
	  $puesto_id=$fila['id'];
		$dep_id=$fila['departamento'];
	}

	$query="UPDATE asesores_puesto SET Fecha=$newVal WHERE id=$puesto_id";
	$connectdb->query($query);

	if($dep_id==29){
		$query="SELECT id FROM asesores_pdv WHERE asesor=$id ORDER BY Fecha LIMIT 1";
		if($result=$connectdb->query($query)){
		  $fila=$result->fetch_assoc();
		  $pdv_id=$fila['id'];
		}

		$query="UPDATE asesores_pdv SET Fecha=$newVal WHERE id=$pdv_id";
		$connectdb->query($query);
	}
}

$connectdb->close();

echo json_encode($data, JSON_PRETTY_PRINT);





?>
