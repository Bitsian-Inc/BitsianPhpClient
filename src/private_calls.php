<?php

require_once("Constants.php");

/**
 * Call Rest API using cURL functions.
 *
 * @param String $method
 * @param String $path
 * @param String $body
 * @return String response
 */
function callAPI($method, $path, $body){

    $url = Constants::BASE_URL .  $path;

    $nonce = round(microtime(true) * 1000);

    $signature = generateSignature($nonce, $method, $path, $body);

    echo $signature . "\n";

    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);

    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'BITSIAN-API-KEY:' . getenv(Constants::API_KEY),
        'BITSIAN-TIMESTAMP:' . $nonce,
        'BITSIAN-API-SIGN:' . $signature,
        'BITSIAN-PASSPHRASE:' . getenv(Constants::PASS_PHRASE),
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);


    if($method == "POST")
    {
        curl_setopt($curl,CURLOPT_POST,1);
        curl_setopt($curl,CURLOPT_POSTFIELDS, $body);
    }

    // EXECUTE:
    $result = curl_exec($curl);
    if(!$result){die("Connection Failure");}
    curl_close($curl);
    return $result;
}