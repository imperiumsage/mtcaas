<?php
require_once("common.php");
require_once("simple_html_dom.php");

$today = date('Y-m-d');
$hour = date('H');

$html = file_get_html("http://www.xe.com/staticcharts/get/?INR,USD,1,1&cs=1");
$lastRate = $html->find('div[class="HLC"]',0)->lastChild()->plaintext;

$sql = "insert into hourly_bank_rate(date,hour,rate) values('$today',$hour,$lastRate) on duplicate key update rate = $lastRate";
print $sql."\n";
if(!$mysqli->query($sql)) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
}


?>