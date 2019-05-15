<?php

$pbx_file=file_get_contents('http://pbxcc.pricetravel.com.mx/ptapi/udip/');

preg_match_all("/<span class=\"card-title activator grey-text text-darken-4\">(.*)<i/", $pbx_file, $ext);
preg_match_all("/<p>(.*)<\/p>/", $pbx_file, $dev);


foreach($ext[1] as $index => $extension){
	$tmp=str_replace(" ", "", $extension);
	$tmp=str_replace("Ext:", "", $tmp);
	$data[$tmp]=$dev[1][$index];	
}

?>
