<?php


/**
 * Generate signature using SHA 256 hmac algorithm along with secret key.
 *
 * @param String $nonce  current time
 * @param String $method method type 'GET' or 'POST'
 * @param String $path   path to hash
 * @param String $body   request body
 * @return string generated signature
 */
function generateSignature($nonce, $method, $path, $body){

    if ($body == null) {

        $body = "";
    }

    $preHash = $nonce . $method . $path . $body;

    return base64_encode(hash_hmac('sha256',$preHash , getenv(Constants::SECRET_KEY), true));
}