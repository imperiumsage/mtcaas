<?php
require_once("common.php");
require_once("simple_html_dom.php");

$process_conf = parse_ini_file("../configs/wellsfargo.ini");
$today = date('Y-m-d');

$slab_starts = explode(",",$process_conf['slab_starts']);
print print_r($slab_starts,true);
$slab_ends = explode(",",$process_conf['slab_ends']);


$index = 0;
foreach($slab_starts as $slab_start) {
	//print $slab_start."\n";
	$slab_end = $slab_ends[$index];
	$html = file_get_html("https://www.wellsfargo.com/as/grs/IN/55/ACCT_TO_ACCT/$slab_end");
	foreach($html->find('table') as $element) {
		if(stristr($element->class, "frmLayout") !== false) {
			//table with data
			$dataCells = $element->find("td");
			$rateData = explode(" ",$dataCells[0]->plaintext);
			$rate = $rateData[3];
			$feeData = explode(" ",str_ireplace("$", "", $dataCells[3]->plaintext));
			$fee = $feeData[0];
			$india_fee = 0;
			$sql = "insert into exchange_rate_daily(date,provider,slab_start,slab_end,rate,flat_fee,india_fee) values('$today','wellsfargo',$slab_start,$slab_end,$rate,$fee,$india_fee) on duplicate key update rate = $rate,flat_fee = $fee, india_fee = $india_fee";
			print $sql."\n";
			if(!$mysqli->query($sql)) {
				echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}
			break;
		}
	}



	$index += 1;
}


?>