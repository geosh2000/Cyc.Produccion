<?php
include("connectDB.php");

$ds=$_GET['ds'];
$ms=$_GET['ms'];
$ys=$_GET['ys'];
$de=$_GET['de'];
$me=$_GET['me'];
$ye=$_GET['ye'];

$Dstart="$ys-$ms-$ds";
$Dend="$ys-$me-$de";

$i=1;
$flag=0;
while($i<=7){
	$dw[$i]=$_GET['dia'.$i];
	if($dw[$i]==1){ 
		$flag++;
		$dwD[$flag]=$i; 
	}
$i++;
}

$i=1;
if($flag>0){
	while($i<=$flag){
		if($i==1){
			$queryDW="AND (";
		}else{ $queryDW="$queryDW OR";}
		
		$queryDW="$queryDW `Dia Semana`=$dwD[$i]";
		
			
	$i++;
	}
}

$queryRange=" WHERE `Date`>='$Dstart' AND `Date`<='$Dend'$queryDW)";


$query="SELECT * FROM 
	(SELECT CAST(CONCAT(`Historial Llamadas`.Anio,'-',`Historial Llamadas`.Mes,'-',`Historial Llamadas`.Dia) AS DATE) AS 'Date', DATE_FORMAT(CAST(CONCAT(`Historial Llamadas`.Anio,'-',`Historial Llamadas`.Mes,'-',`Historial Llamadas`.Dia) AS DATE),'%w') AS 'Dia Semana', `Historial Llamadas`.Dia AS 'Dia', `Historial Llamadas`.Mes AS 'Mes', `Historial Llamadas`.Anio AS 'Anio', `Historial Llamadas`.Skill AS 'Skill', `Total`.Llamadas as 'Total Llamadas', `Historial Llamadas`.`1`/`Total`.Llamadas AS '1', `Historial Llamadas`.`2`/`Total`.Llamadas AS '2', `Historial Llamadas`.`3`/`Total`.Llamadas AS '3', `Historial Llamadas`.`4`/`Total`.Llamadas AS '4', `Historial Llamadas`.`5`/`Total`.Llamadas AS '5', `Historial Llamadas`.`6`/`Total`.Llamadas AS '6', `Historial Llamadas`.`7`/`Total`.Llamadas AS '7', `Historial Llamadas`.`8`/`Total`.Llamadas AS '8', `Historial Llamadas`.`9`/`Total`.Llamadas AS '9', `Historial Llamadas`.`10`/`Total`.Llamadas AS '10', `Historial Llamadas`.`11`/`Total`.Llamadas AS '11', `Historial Llamadas`.`12`/`Total`.Llamadas AS '12', `Historial Llamadas`.`13`/`Total`.Llamadas AS '13', `Historial Llamadas`.`14`/`Total`.Llamadas AS '14', `Historial Llamadas`.`15`/`Total`.Llamadas AS '15', `Historial Llamadas`.`16`/`Total`.Llamadas AS '16', `Historial Llamadas`.`17`/`Total`.Llamadas AS '17', `Historial Llamadas`.`18`/`Total`.Llamadas AS '18', `Historial Llamadas`.`19`/`Total`.Llamadas AS '19', `Historial Llamadas`.`20`/`Total`.Llamadas AS '20', `Historial Llamadas`.`21`/`Total`.Llamadas AS '21', `Historial Llamadas`.`22`/`Total`.Llamadas AS '22', `Historial Llamadas`.`23`/`Total`.Llamadas AS '23', `Historial Llamadas`.`24`/`Total`.Llamadas AS '24', `Historial Llamadas`.`25`/`Total`.Llamadas AS '25', `Historial Llamadas`.`26`/`Total`.Llamadas AS '26', `Historial Llamadas`.`27`/`Total`.Llamadas AS '27', `Historial Llamadas`.`28`/`Total`.Llamadas AS '28', `Historial Llamadas`.`29`/`Total`.Llamadas AS '29', `Historial Llamadas`.`30`/`Total`.Llamadas AS '30', `Historial Llamadas`.`31`/`Total`.Llamadas AS '31', `Historial Llamadas`.`32`/`Total`.Llamadas AS '32', `Historial Llamadas`.`33`/`Total`.Llamadas AS '33', `Historial Llamadas`.`34`/`Total`.Llamadas AS '34', `Historial Llamadas`.`35`/`Total`.Llamadas AS '35', `Historial Llamadas`.`36`/`Total`.Llamadas AS '36', `Historial Llamadas`.`37`/`Total`.Llamadas AS '37', `Historial Llamadas`.`38`/`Total`.Llamadas AS '38', `Historial Llamadas`.`39`/`Total`.Llamadas AS '39', `Historial Llamadas`.`40`/`Total`.Llamadas AS '40', `Historial Llamadas`.`41`/`Total`.Llamadas AS '41', `Historial Llamadas`.`42`/`Total`.Llamadas AS '42', `Historial Llamadas`.`43`/`Total`.Llamadas AS '43', `Historial Llamadas`.`44`/`Total`.Llamadas AS '44', `Historial Llamadas`.`45`/`Total`.Llamadas AS '45', `Historial Llamadas`.`46`/`Total`.Llamadas AS '46', `Historial Llamadas`.`47`/`Total`.Llamadas AS '47', `Historial Llamadas`.`48`/`Total`.Llamadas AS '48' FROM `Historial Llamadas` 
	LEFT JOIN 
		(SELECT `1`+`2`+`3`+`4`+`5`+`6`+`7`+`8`+`9`+`10`+`11`+`12`+`13`+`14`+`15`+`16`+`17`+`18`+`19`+`20`+`21`+`22`+`23`+`24`+`25`+`26`+`27`+`28`+`29`+`30`+`31`+`32`+`33`+`34`+`35`+`36`+`37`+`38`+`39`+`40`+`41`+`42`+`43`+`44`+`45`+`46`+`47`+`48` AS 'Llamadas', `Dia`, `Mes`, `Anio`, `Skill` FROM `Historial Llamadas`) AS Total 
		ON `Historial Llamadas`.Dia = `Total`.Dia AND `Historial Llamadas`.Mes = `Total`.Mes AND `Historial Llamadas`.Anio = `Total`.Anio AND `Historial Llamadas`.Skill = `Total`.Skill) AS Tabla1$queryRange";


$result=mysql_query($query);
$num=mysql_numrows($result);
$i=0;

while ($i<$num){
	$DiaSem[$i]=mysql_result($result,$i,'Dia Semana');
	$Total[$i]=mysql_result($result,$i,'Total Llamadas');
	$Dia[$i]=mysql_result($result,$i,'Dia');
	$Mes[$i]=mysql_result($result,$i,'Mes');
	$Anio[$i]=mysql_result($result,$i,'Anio');
	$x=1;
	while($x<=48){
		$hora[$i][$x]=mysql_result($result,$i,"$x");
	$x++;
	}
	
	
$i++;
}

$i=1;
while($i<=48){
	$x=0;
	$prom=0;
	while($x<$num){
		$prom=$prom+$hora[$x][$i];
	$x++;
	}
	$avg[$i-1]=$prom/$num*100;
$i++;
}


$a = array();
       $cols = array();
       $rows = array();
       $cols[] = array("id"=>"","label"=>"Hora","pattern"=>"","type"=>"number");
       /*
       $i=0;
       while($i<$num){
       		$cols[] = array("id"=>"","label"=>"$Dia[$i]/$Mes[$i]/$Anio[$i]","pattern"=>"","type"=>"number"); 
       	$i++;
       	}
       */
       $cols[] = array("id"=>"","label"=>"Average","pattern"=>"","type"=>"number"); 
        
       $i=0;
       $h=0;
       while ($i<48){
       	$rows[] = array("c"=>array(array("v"=>"$h","f"=>null),array("v"=>"$avg[$i]","f"=>null)));
          $i++;
          $h=$h+0.5;
       }
       $a = array("cols"=>$cols,"rows"=>$rows);
     

       



echo  json_encode($a);

//echo "<br><br><br>$query";


?>