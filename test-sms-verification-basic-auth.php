<?php

declare(strict_types=1);

const URL = "https://verificationapi-v1.sinch.com/verification/v1/verifications";
const METHOD = "POST";

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

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_HTTPHEADER => [
    "Content-Type: application/json",
    "Authorization: Basic " . base64_encode($applicationKey . ":" . $applicationSecret)
    ],
    CURLOPT_POSTFIELDS => json_encode($smsVerficationPayload),
    CURLOPT_URL => URL,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => METHOD,
]);

$response = curl_exec($curl);
$error = curl_error($curl);
$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

curl_close($curl);

if ($error) {
    echo "cURL Error #:" . $error . "\n";
} else {
    echo $response . "\n";
    echo $statusCode . "\n";
}
