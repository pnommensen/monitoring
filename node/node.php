<?php
set_time_limit(60); // Attempt to set the timeout for the user

$github = file_get_contents('https://raw.githubusercontent.com/jsdelivr/monitoring/master/providers.json');
$json = json_decode($github, true);
$host = $_SERVER["SERVER_NAME"];

foreach ($json['providers'] as $provider){
	foreach ($json['paths'] as $path){
		if( check($provider,$path) === false ){
			$status[$provider] = '0'; 			
		}else{
			$status[$provider] = '100';		
		}
	}
}

print_r($status);
$data = http_build_query($status);
file_get_contents("http://ping.jsdelivr.com/server.php?$data&host=$host");

function check($hostname, $path){
$url = "https://$hostname/$path";
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $url,
	CURLOPT_CONNECTTIMEOUT => 5,
	CURLOPT_TIMEOUT => 5,
	CURLOPT_MAXREDIRS => 2,
	CURLOPT_SSL_VERIFYPEER => TRUE,
	CURLOPT_SSL_VERIFYHOST => FALSE,
	CURLOPT_VERBOSE => true,
	CURLOPT_CERTINFO => true,
	CURLOPT_CAINFO => getcwd().'\cacert.pem'
));
$resp = curl_exec($curl);
$info = curl_getinfo($curl);
$sslhost = $info['certinfo'][0]['Subject']['CN'];
$origin = md5(file_get_contents("http://origin.jsdelivr.net/$path"));
$curmd = md5($resp);

curl_close($curl);
	if ($info['http_code'] == '200' && $sslhost == 'cdn.jsdelivr.net' && $origin == $curmd){
		return true;		
	}
}

?>
