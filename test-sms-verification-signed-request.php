<?php

declare(strict_types=1);

const URL = "https://verificationapi-v1.sinch.com/verification/v1/verifications";

/*
    The key from one of your Verification Apps, found here https://dashboard.sinch.com/verification/apps
*/
$applicationKey  = "<REPLACE_WITH_VERIF_APP_KEY>";

/*
    The secret from the Verification App that uses the key above, found here https://dashboard.sinch.com/verification/apps
*/
$applicationSecret = "<REPLACE_WITH_VERIF_APP_SECRET>";

/*
    The number that will receive the SMS PIN. Test accounts are limited to verified numbers.
    The number must be in E.164 Format, e.g. Netherlands 0639111222 -> +31639111222
*/
$toNumber = "<REPLACE_WITH_TO_NUMBER>";

$smsVerficationPayload = [
    "identity" => [
        "type" => "number",
        "endpoint" => $toNumber
    ],
    "method" => "sms"
];

$encodedPayload = mb_convert_encoding(json_encode($smsVerficationPayload, JSON_UNESCAPED_UNICODE), 'UTF-8');
$md5EncodedPayload = md5($encodedPayload, true);
$encodedMd5ToBase64Payload = base64_encode($md5EncodedPayload);

$httpVerb = 'POST';
$requestContentType = 'application/json; charset=UTF-8';
date_default_timezone_set('UTC');
$timeNow = date(DateTime::ATOM);
$requestTimeStamp = "x-timestamp:" . $timeNow;
$requestUriPath = "/verification/v1/verifications";

$stringToSign = $httpVerb . "\n"
    . $encodedMd5ToBase64Payload . "\n"
    . $requestContentType . "\n"
    . $requestTimeStamp . "\n"
    . $requestUriPath;

$b64DecodedApplicationSecret = base64_decode($applicationSecret, true);

$calculatedSignature = base64_encode(hash_hmac("sha256", $stringToSign, $b64DecodedApplicationSecret, true));

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_HTTPHEADER => [
    "content-type: {$requestContentType}",
    "x-timestamp: {$timeNow}",
    "authorization: application {$applicationKey}:{$calculatedSignature}"
    ],
    CURLOPT_POSTFIELDS => json_encode($smsVerficationPayload),
    CURLOPT_URL => URL,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => $httpVerb,
]);

$response = curl_exec($curl);
$error = curl_error($curl);
$statusCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);

curl_close($curl);

if ($error) {
    echo "cURL Error #:" . $error . "\n";
} else {
    echo $response . "\n";
    echo $statusCode . "\n";
}
