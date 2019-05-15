<?php
include("DBCallsHoraV.php");



$a = array();
       $cols = array();
       $rows = array();
       $cols[] = array("id"=>"","label"=>"Hora","pattern"=>"","type"=>"number");
       $cols[] = array("id"=>"","label"=>"Today","pattern"=>"","type"=>"number"); 
       $cols[] = array("id"=>"","label"=>"Y","pattern"=>"","type"=>"number"); 
       $cols[] = array("id"=>"","label"=>"LW","pattern"=>"","type"=>"number"); 
       $cols[] = array("id"=>"","label"=>"Forecast","pattern"=>"","type"=>"number"); 
       $cols[] = array("id"=>"","label"=>"Precision %","pattern"=>"","type"=>"number"); 
       $cols[] = array("id"=>"","label"=>"Top H Prec.","pattern"=>"","type"=>"number"); 
       $cols[] = array("id"=>"","label"=>"Top L Prec.","pattern"=>"","type"=>"number");  
       $i=0;
       while ($i<48){
       	switch ($i){
		case 46:
			
			$forecast= $CVf[0];
			if ($CVf[0]!=0){
			$pres[$i]=($CVt[$i]/$CVf[0]);
			}else{$pres[$i]=1;}
			break;
			
		case 47:
			$forecast= $CVf[1];
			if ($CVf[1]!=0){
			$pres[$i]=($CVt[$i]/$CVf[1]);
			}else{$pres[$i]=1;}
			break;
		default:
			$forecast= $CVf[$i+2];
			if ($CVf[$i+2]!=0){
			$pres[$i]=($CVt[$i]/$CVf[$i+2]);
			}else{$pres[$i]=1;}
			break;
	}
	if ($pres[$i]>2){$prn=2;}else{$prn=$pres[$i];}
	if ($prn<0){$prn=0;}
	if ($i>45 or $i<18){$prn=NULL;}
	if ($CVt[$i]==0){$today=NULL;}else{$today=$CVt[$i];}
          $rows[] = array("c"=>array(array("v"=>$CVHora[$i],"f"=>null),array("v"=>$today,"f"=>null),array("v"=>$CVy[$i],"f"=>null),array("v"=>$CVlw[$i],"f"=>null),array("v"=>$forecast,"f"=>null),array("v"=>$prn,"f"=>null),array("v"=>1.15,"f"=>null),array("v"=>0.85,"f"=>null)));
          $i++;
       }
       $a = array("cols"=>$cols,"rows"=>$rows);
      


       



echo  json_encode($a);

?>