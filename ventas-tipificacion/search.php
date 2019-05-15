<?php
    include('../connectDB.php');

    //get search term
    $searchTerm = $_GET['term'];

    //get matched data from skills table
    $query = "SELECT DISTINCT nombre_agencia FROM ag_tipificacion WHERE nombre_agencia LIKE '%$searchTerm%' ORDER BY nombre_agencia ASC";
    $result=mysql_query($query);
    $num=mysql_numrows($result);
    $i=0;
    while ($i<$num) {
        $data[] = mysql_result($result,$i,'nombre_agencia');
    $i++;
    }

    //return json data
    echo json_encode($data);
?>