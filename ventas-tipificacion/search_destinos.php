<?php
    include('../connectDB.php');

    //get search term
    $searchTerm = $_GET['term'];
	
    //get matched data from skills table
    for($i=1;$i<5;$i++){
	    $query = "SELECT DISTINCT destinos_solicitados$i FROM ventas_tipificacion WHERE destinos_solicitados$i LIKE '%$searchTerm%' ORDER BY destinos_solicitados$i ASC";
		$result=mysql_query($query);
		if(mysql_error()){echo "ERROR! ".mysql_error()."<br><br>";}
	    $num=mysql_numrows($result);
		$x=0;
	    while ($x<$num) {
	    	if(!in_array(mysql_result($result,$x,'destinos_solicitados'.$i), $data)){
	    		$data[] = mysql_result($result,0,'destinos_solicitados'.$i);	
			}
	    $x++;
	    }
	}
	
	//return json data
    echo json_encode($data);
?>