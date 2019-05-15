<?php
include("../../connectDB.php");

$db=$_GET['db'];
$fieldid=$_GET['fieldid'];
$valid=$_GET['valid'];
$i=1;
while($i<10){
    $field[$i]=$_GET["field$i"];
    $val[$i]="'".$_GET["val$i"]."'";
    if($field[$i]!=NULL){$num_fields++;}
$i++;
}

$query="UPDATE $db SET ";
$i=1;
while($i<=$num_fields){
    $query.="$field[$i]=$val[$i]";
    if($i!=$num_fields){$query.=", ";}
$i++;
}
$query.=" WHERE $fieldid='$valid'";
echo "$query<br>";
mysql_query($query);
if(mysql_errno()){
			    echo "status- ERROR -status msg- Error al actualizar status de $valid -msg";
                }else{
                    echo "status- OK -status msg- Validacion Exitosa de registro $valid -msg";}



?>