<?php

function getJSONResponse($url,$cookie) {
	$ch = curl_init();

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

$amount_in_usd = $argv[1];

print getJSONResponse("https://www.xoom.com/ajax/options-xfer-amount-ajax?receiveCountryCode=IN&sendAmount=$amount_in_usd");


?>