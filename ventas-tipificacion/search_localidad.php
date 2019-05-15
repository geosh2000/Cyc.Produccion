<?php
    include('../connectDB.php');

    //get search term
    $searchTerm = $_GET['term'];

    //get matched data from skills table
    $query = "SELECT DISTINCT localidad_agencia FROM ag_tipificacion WHERE localidad_agencia LIKE '%$searchTerm%' ORDER BY localidad_agencia ASC";
    $result=mysql_query($query);
    $num=mysql_numrows($result);
    $i=0;
    while ($i<$num) {
        $data[] = mysql_result($result,$i,'localidad_agencia');
    $i++;
    }

    //return json data
    echo json_encode($data);
?>