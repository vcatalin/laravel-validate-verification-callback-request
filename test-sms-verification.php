<?php

declare(strict_types=1);

/*
    The key from one of your Verification Apps, found here https://dashboard.sinch.com/verification/apps
*/
const KEY = "<REPLACE_WITH_VERIF_APP_KEY>";

/*
    The secret from the Voice App that uses the key above, found here https://dashboard.sinch.com/verification/apps
*/
const SECRET = "<REPLACE_WITH_VERIF_APP_SECRET>";

/*
    The number that will receive the SMS PIN. Test accounts are limited to verified numbers.
    The number must be in E.164 Format, e.g. Netherlands 0639111222 -> +31639111222
*/
const TO = "<REPLACE_WITH_TO_NUMBER>";

const URL = "https://verificationapi-v1.sinch.com/verification/v1/verifications";
const METHOD = "POST";

$smsVerficationPayload = [
    "identity" => [
        "type" => "number",
        "endpoint" => TO
    ],
    "method" => "sms"
];

$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_HTTPHEADER => [
    "Content-Type: application/json",
    "Authorization: Basic " . base64_encode(KEY . ":" . SECRET)
  ],
  CURLOPT_POSTFIELDS => json_encode($smsVerficationPayload),
  CURLOPT_URL => URL,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => METHOD,
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
