<?php
//include("../connectDB.php");
error_reporting(0);

$_POST['start']=$_POST['start'];
$_POST['end']=$_POST['start'];
$_POST['skill']=$_POST['skill'];
$_POST['submit']=1;
$esquemas=$_POST['esquemas'];

$_POST['start']='2017-04-10';
$_POST['end']='2017-04-10';
$_POST['skill']=35;
$_POST['submit']=1;
$esquemas=$_POST['esquemas'];

$esquema = explode(',',$esquemas);

IF($_POST['extra']==1){
	$extra=4;
}

IF(isset($_POST['tope'])){
	$tope=$_POST['tope'];
}else{
	$tope=1000;
}


//echo "EXTRA: $extra<br>";

include('forecast_erlang.php');
			for($y=0;$y<48;$y++){
				if($y<4){
					$index=$y+48;
				}else{
					$index=$y;
				}

				if($data[$_POST['start']]['forecast'][$y]==NULL){
					$td[$index]=0;
				}else{
					if($data[$_POST['start']]['necesarios'][$y]==NULL || $data[$_POST['start']]['necesarios'][$y]==""){
						$td[$index]=0;
					}else{
						$td[$index]=$data[$_POST['start']]['necesarios'][$y];
					}
				}
			}

$needed=$td;
$req=$needed;


function horario($x,$op,$esquema){
	global $extra;
	switch($op){
		case '+':
			switch($esquema){
				case 8:
					if($x>32){
						$sum=13+$extra;
					}elseif($x>29){
						$sum=14+$extra;
					}else{
						$sum=15+$extra;
					}

					return $x+$sum;

					break;
				default:

					return $x+$esquema;
					break;
			}
			break;
		case '-':
			switch($esquema){
				case 8:
					if($x>45){
						$sum=13+$extra;
					}elseif($x>43){
						$sum=14+$extra;
					}else{
						$sum=15+$extra;
					}

					return $x-$sum;

					break;
				default:

					return $x-$esquema;
					break;
			}
			break;
	}
}

function sumToNeeded($inicio,$esquema){
	global $needed, $req;
	for($i=$inicio;$i<=intval(horario($inicio,'+',$esquema));$i++){
		$req[$i]-=1;
	}
}

$x=38;
$y=51;
$index=0;
for($i=12;$i<=intval($x);$w=1){

	if(count($horario)>=$tope){break;}

	if($req[$i]>0){

		if($i==33 || $i==26){
			$horario[]=$i-1;
		}else{
			$horario[]=$i;
		}

		sumToNeeded($i,8);
		if($req[$i]==0){
			$i++;
		}
	}else{
		if($i<=28){
			$i++;
		}
	}

	if(count($horario)>=$tope){break;}

	if($req[$y]>0){

		if(horario($y,'-',8)==33 || horario($y,'-',8)==26){
			$horario[]=horario($y,'-',8)-1;
		}else{
			$horario[]=horario($y,'-',8);;
		}

		sumToNeeded(horario($y,'-',8),8);
		if($req[$y]==0){
			$y--;
		}
	}else{
		$y--;
	}

	$x=horario($y,'-',8);


}

sort($horario);

print json_encode($horario,JSON_PRETTY_PRINT);
