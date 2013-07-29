<?php
	require_once("common.php");
	$mode = $argv[1];
	$startDate = $argv[2];
	$currDate = date('Y-m-d');
	list($endYear,$endMonth,$endDay) = sscanf($currDate,"%d-%d-%d");

	if($mode !== "backfill") {
		$startDate = date('Y-m-d');
	}
	list($startYear,$startMonth,$startDay) = sscanf($startDate,"%d-%d-%d");
	$first = date('n/j/y', mktime(0, 0, 0, $startMonth, $startDay, $startYear)); 
	$last = date('n/j/y', mktime(0, 0, 0, $endMonth, $endDay, $endYear));
	$currency = "INR";
	$csv = getMonthlyCurrencyRateCSV($first,$last,$currency)."\n";

	$daily_rates = explode(PHP_EOL,$csv);
	foreach($daily_rates as $row) {
		$columns = str_getcsv($row);
		$date = formatDate($columns[0]);
		$value_in_inr = $columns[1];
		if(empty($date) || empty($value_in_inr)) {
			continue;
		}
		$sql = "insert into bank_rate(date,rate) values('$date',$value_in_inr) on duplicate key update rate = $value_in_inr";
		print $sql."\n";
		if(!$mysqli->query($sql)) {
			echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	}


?>