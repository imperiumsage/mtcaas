<?php
require_once("common.php");

$process_conf = parse_ini_file("../configs/icici.ini");
$today = date('Y-m-d');
unlink("icici.txt");
getJSONResponse("https://m2inet.icicibank.co.in/m2iNet/exchangeRate.misc","icici.txt");

$slab_starts = explode(",",$process_conf['slab_starts']);
print print_r($slab_starts,true);
$slab_ends = explode(",",$process_conf['slab_ends']);
$searchText = "<table";

$index = 0;
foreach($slab_starts as $slab_start) {
	//print $slab_start."\n";
	$slab_end = $slab_ends[$index];
	$iciciResponse = getJSONResponse("https://m2inet.icicibank.co.in/m2iNet/exRateCalculator?productId=100002&&txnAmount=$slab_end&&txnFixedAmount=0&&fixedInr=N&&deliveryMode=200003&&currency=99","icici.txt");
	//print $iciciResponse."\n";	
	$endIndex = stripos($iciciResponse, $searchText);
	$rateObj = explode("##",substr($iciciResponse, 0, $endIndex));
	print print_r($rateObj,true);
	$fee = $rateObj[0];
	$india_fee = $rateObj[5]/$slab_end;
	$rate = $rateObj[4]; 
	$sql = "insert into exchange_rate_daily(date,provider,slab_start,slab_end,rate,flat_fee,india_fee) values('$today','icici',$slab_start,$slab_end,$rate,$fee,$india_fee) on duplicate key update rate = $rate,flat_fee = $fee, india_fee = $india_fee";
	print $sql."\n";
	if(!$mysqli->query($sql)) {
		echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	$index += 1;
}


?>