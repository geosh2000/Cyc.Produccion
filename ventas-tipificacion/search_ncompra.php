<?php
    include('../connectDB.php');

    //get search term
    $searchTerm = $_GET['term'];

    //get matched data from skills table
    $query = "SELECT DISTINCT motivo_no_compra FROM ventas_tipificacion WHERE motivo_no_compra LIKE '%$searchTerm%' ORDER BY motivo_no_compra ASC";
    $result=mysql_query($query);
    $num=mysql_numrows($result);
    $i=0;
    while ($i<$num) {
        $data[] = mysql_result($result,$i,'motivo_no_compra');
    $i++;
    }

    //return json data
    echo json_encode($data);
?>