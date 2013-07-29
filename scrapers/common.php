<?php

date_default_timezone_set('America/Los_Angeles');
$db_conf = parse_ini_file("../configs/db.ini");
$mysqli = new mysqli($db_conf["mysql_host"], $db_conf["mysql_user"], $db_conf["mysql_pass"], $db_conf["mysql_db"]);
if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);

}

$ch = curl_init();

function getJSONResponse($url,$cookie) {
	global $ch;


	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_COOKIEJAR, $cookie);
	curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.1.6) Gecko/20070725 Firefox/2.0.0.6");
	curl_setopt ($ch, CURLOPT_TIMEOUT, 0);
	curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
	//curl_setopt($ch, CURLOPT_HEADER, TRUE);
	$result = curl_exec($ch);
	return $result;
}

function getMonthlyCurrencyRateCSV($first,$last,$currency) {
	$url = "http://www.oanda.com/currency/historical-rates-classic?date_fmt=us&date=$last&date1=$first&exch=USD&expr=$currency&margin_fixed=0&format=CSV&redirected=1";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
	$html = curl_exec($ch);
	$start = "<PRE>";
	$end = "</PRE>";
	$startpos = stripos($html, $start);
	$endpos = stripos($html, $end);
	$start_of_substr = $startpos + strlen($start);
	$csv = substr($html, $start_of_substr, $endpos - $start_of_substr);
	return $csv;
}

function formatDate($input) {
	$datetime = new DateTime($input);
	return date('Y-m-d',$datetime->getTimeStamp());

}

?>