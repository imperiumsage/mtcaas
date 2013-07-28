<?php
require_once("common.php");
require_once("simple_html_dom.php");

$today = date('Y-m-d');

//transfast is not as sophisticated
$html = file_get_html("https://www.transfast.com/sendmoney-IND.aspx");
//fixed rate
$rate = trim($html->find('td[id=rate_transfast_lockedin]',0)->plaintext);
$india_fee = 0;

//lower slab

$slab_start = 501;
$slab_end = 1000;
$fee_min = 4.49;
$sql = "insert into exchange_rate_daily(date,provider,slab_start,slab_end,rate,flat_fee,india_fee) values('$today','transfast',$slab_start,$slab_end,$rate,$fee_min,$india_fee) on duplicate key update rate = $rate,flat_fee = $fee_min, india_fee = $india_fee";
print $sql."\n";
if(!$mysqli->query($sql)) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

//upper slab
$slab_start = 1001;
$slab_end = 2500;
$fee_max = 0;
$sql = "insert into exchange_rate_daily(date,provider,slab_start,slab_end,rate,flat_fee,india_fee) values('$today','transfast',$slab_start,$slab_end,$rate,$fee_max,$india_fee) on duplicate key update rate = $rate,flat_fee = $fee_max, india_fee = $india_fee";
print $sql."\n";
if(!$mysqli->query($sql)) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
}


?>