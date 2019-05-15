<?php ?>
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
</head>
<?php
include("../connectDB.php");
header("Content-Type: text/html;charset=utf-8");

//GET Variables
$id=$_GET['id'];
$nombre=utf8_encode($_GET['Nombre']);
$ncorto=$_GET['NCorto'];
$iddepartamento=$_GET['idDepartamento'];
$activo=$_GET['Activo'];
if($_GET['Ingreso']!=""){$ingreso="'".date('Y/m/d', strtotime(utf8_encode($_GET['Ingreso'])))."'";}else{$ingreso="NULL";}
if(isset($_GET['Egreso'])){$egreso="'".date('Y/m/d', strtotime($_GET['Egreso']))."'";}else{$egreso="NULL";}
$usuario=$_GET['Usuario'];
$esquema=$_GET['Esquema'];
if($_GET['FechaNacimiento']!=""){$fechanacimiento="'".date('Y/m/d', strtotime($_GET['FechaNacimiento']))."'";}else{$fechanacimiento="0000/00/00";}
if($ingreso=="'1970/01/01'"){$ingreso='NULL';}
if($egreso=="'1970/01/01'"){$egreso='NULL';}
if($fechanacimiento=="'1970/01/01'"){$fechanacimiento='NULL';}
$userid=$_GET['userid'];
$ontraining=$_GET['on_training'];
$numcolaborador=$_GET['num_colaborador'];
$profile=$_GET['profile'];
$row=$_GET['row'];
$err_count=0;

//Option
$option=$_GET['option'];

switch($option){
    case 1:
        break;
    case 2:
        mysql_query("SET NAMES 'utf8'");
        $query1="UPDATE Asesores
                    SET Nombre='$nombre', `N Corto`='$ncorto', `id Departamento`='$iddepartamento',
                    Activo='$activo', Ingreso=$ingreso, Egreso=$egreso, Usuario='$usuario',
                    Esquema='$esquema', `Fecha Nacimiento`=$fechanacimiento, num_colaborador='$numcolaborador',
                    on_training='$ontraining'
                    WHERE id='$id'";
        $query2="UPDATE userDB
                    SET username='$usuario', profile='$profile', asesor_id='$id'
                    WHERE userid='$userid'";
        //UPDATE DB

        mysql_query($query1);
        if(mysql_errno()){
            echo "$key1 // $key_xfered MySQL error ".mysql_errno().": "
                 .mysql_error()."\n<br>When executing <br>\n$query1\n<br><br>";
                             $err_count++;
        }
        mysql_query("SET NAMES 'utf8'");
        mysql_query($query2);
        if(mysql_errno()){
            echo "$key1 // $key_xfered MySQL error ".mysql_errno().": "
                 .mysql_error()."\n<br>When executing <br>\n$query2\n<br><br>";
                             $err_count++;
        }
        if($err_count==0){
                $q="SELECT * FROM PCRCs WHERE id='$iddepartamento'";
                $pcrc_id=mysql_result(mysql_query($q),0,'Departamento');
                $q2="SELECT * FROM profilesDB WHERE id='$profile'";
                $perfil=mysql_result(mysql_query($q2),0,'profile_name');
                echo "<td id='id_$row'>$id</td>
            	<td id='newid_$row'>0</td>
                <td id='num_colaborador_$row'>$numcolaborador</td>
            	<td id='Nombre_$row'>$nombre</td>
            	<td id='N Corto_$row'>$ncorto</td>
            	<td id='id Departamento_$row'>$iddepartamento</td>
            	<td id='Activo_$row'>$activo</td>
                <td id='on_training_$row'>$ontraining</td>
            	<td id='Ingreso_$row'>$ingreso</td>
            	<td id='Egreso_$row'>$egreso</td>
            	<td id='Usuario_$row'>$usuario</td>
            	<td id='Esquema_$row5'>$esquema</td>
            	<td id='Fecha Nacimiento_$row'>$fechanacimiento</td>
            	<td id='pcrc_id_$row'>$iddepartamento</td>
            	<td id='Departamento_$row'>$pcrc_id</td>
            	<td id='userid_$row'>$userid</td>
            	<td id='username_$row'>$usuario</td>
            	<td id='profile_$row'>$profile</td>
            	<td id='profile_name_$row'>$perfil</td>
                <td><sh id='$row'>Editar</sh></td>";}
        break;
}





?>