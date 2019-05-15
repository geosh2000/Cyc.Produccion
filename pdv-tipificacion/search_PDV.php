<?php
    include('../connectDB.php');

    //get search term
    $searchTerm = $_GET['term'];

    //get matched data from skills table
    $query = "SELECT DISTINCT PDV FROM PDVs WHERE PDV LIKE '%$searchTerm%' ORDER BY PDV ASC";
    $result=mysql_query($query);
	if(mysql_error()){echo "Error: ".mysql_error();}
    $num=mysql_numrows($result);
    $i=0;
    while ($i<$num) {
        $data[] = utf8_encode(mysql_result($result,$i,'PDV'));
    $i++;
    }

    //return json data
    echo json_encode($data);
?>