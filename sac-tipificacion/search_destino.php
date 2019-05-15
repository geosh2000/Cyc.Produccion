<?php
    include('../connectDB.php');

    //get search term
    $searchTerm = $_GET['term'];

    //get matched data from skills table
    $query = "SELECT DISTINCT Destino FROM us_servicios_adicionales WHERE Destino LIKE '%$searchTerm%' ORDER BY Destino ASC";
    $result=mysql_query($query);
    $num=mysql_numrows($result);
    $i=0;
    while ($i<$num) {
        $data[] = mysql_result($result,$i,'Destino');
    $i++;
    }

    //return json data
    echo json_encode($data);
?>