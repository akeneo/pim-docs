<?php
$username = "admin";
$apiKey   = "79fadafa61723d34582f591586d63cee33199216";
$salt     = "2lmnle11aeucsgc0cw0go0o0gcow8sw";

$nonce   = uniqid();
$created = date('c');

$digest  = base64_encode(sha1(base64_decode($nonce) . $created . $apiKey.'{'.$salt.'}', true));

$headers = array();
$headers[] = 'CONTENT_TYPE: application/json';
$headers[] = 'Authorization: WSSE profile="UsernameToken"';
$headers[] =
    sprintf(
        'X-WSSE: UsernameToken Username="%s", PasswordDigest="%s", Nonce="%s", Created="%s"',
        $username,
        $digest,
        $nonce,
        $created
    );

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://pim-dev.local/api/rest/products/sku-000.json');
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);

$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "HTTP Return code:".$httpStatus."\n";

if (false === $result) {
    echo "ERROR:".curl_error($ch)."\n";
} else {
    echo "RESULT:$result\n";
}
