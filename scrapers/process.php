<?php

$conf = parse_ini_file("../configs/scrapers.ini");

$all_providers = explode(",",$conf["providers"]);
foreach($all_providers as $provider) {
	exec("php $provider.php >> ../logs/scrapers.log 2>&1");
}



?>