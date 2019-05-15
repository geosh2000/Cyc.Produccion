<?
include("../connectDB.php");

$i=2216;
while($i<=4616){
	$query="DELETE FROM t_Locs WHERE locs_id='$i'";
	mysql_query($query);
$i++;
}

?>