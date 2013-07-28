<?php
require_once("common.php");

$process_conf = parse_ini_file("../configs/xoom.ini");
$today = date('Y-m-d');



$slab_starts = explode(",",$process_conf['slab_starts']);
//print print_r($slab_starts,true);
$slab_ends = explode(",",$process_conf['slab_ends']);
$index = 0;
foreach($slab_starts as $slab_start) {
	//print $slab_start."\n";
	$slab_end = $slab_ends[$index];
	$xoomResponse = getJSONResponse("https://www.xoom.com/ajax/options-xfer-amount-ajax?receiveCountryCode=IN&sendAmount=$slab_end","xoom.txt");
	$rateObj = json_decode($xoomResponse,true);
	//print print_r($rateObj,true);
	$fee = $rateObj["result"]["disbursementTypes"]["DEPOSIT"]["fee"];
	$rate = $rateObj["result"]["fxRate"];
	$sql = "insert into exchange_rate_daily(date,provider,slab_start,slab_end,rate,flat_fee) values('$today','xoom',$slab_start,$slab_end,$rate,$fee) on duplicate key update rate = $rate,flat_fee = $fee";
	print $sql."\n";
	if(!$mysqli->query($sql)) {
		echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	$index += 1;
}


if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}


?>