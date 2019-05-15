<?php
    include('../connectDB.php');

    //get search term
    $searchTerm = $_GET['term'];

    //get matched data from skills table
    $query = "SELECT DISTINCT otro FROM sac_tipificacion WHERE otro LIKE '%$searchTerm%' ORDER BY otro ASC";
    $result=mysql_query($query);
    $num=mysql_numrows($result);
    $i=0;
    while ($i<$num) {
        $data[] = mysql_result($result,$i,'otro');
    $i++;
    }

    //return json data
    echo json_encode($data);
?>