<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::Post('/verification/events', function (Request $request) {
    /*
        The key from one of your Verification Apps, found here https://dashboard.sinch.com/verification/apps
    */
    $applicationKey = "<REPLACE_WITH_VERIF_APP_KEY>";

    /*
        The secret from the Verification App that uses the key above, found here https://dashboard.sinch.com/verification/apps
    */
    $applicationSecret= "<REPLACE_WITH_VERIF_APP_SECRET>";

    $authHeader = explode(' ', $request->header('authorization'));
    $callbackAuthHeader = explode(':', $authHeader[1]);
    $callbackKey = $callbackAuthHeader[0];
    $callbackSignature = $callbackAuthHeader[1];

    if ($callbackKey !== $applicationKey) {
        Log::info($callbackKey . " is different from " . $applicationKey);
        Log::info("The keys do not match, the HTTP request did not originate from Sinch!");
        return response()->json([], Response::HTTP_FORBIDDEN);
    }

    $callbackRequest = $request->getContent();
    $md5EncodedCallbackRequest = md5(mb_convert_encoding($callbackRequest, "UTF-8", "auto"), true);
    $base64EncodedMd5CallbackRequest = base64_encode($md5EncodedCallbackRequest);

    $requestMethod = $request->method();
    $requestContentType = $request->header('content-type');
    $requestTimeStamp = $request->header('x-timestamp');
    $requestUriPath = $request->getPathInfo();

    $stringToSign = $requestMethod . "\n"
        . $base64EncodedMd5CallbackRequest . "\n"
        . $requestContentType . "\n"
        . "x-timestamp:" . $requestTimeStamp . "\n"
        . $requestUriPath;

    $b64DecodedApplicationSecret = base64_decode($applicationSecret, true);

    $calculatedSignature = base64_encode(
        hash_hmac(
            "sha256",
            mb_convert_encoding($stringToSign, "UTF-8", "auto"),
            $b64DecodedApplicationSecret,
            true
        )
    );

    if ($callbackSignature !== $calculatedSignature) {
        Log::info($callbackSignature . " is different from " . $calculatedSignature);
        Log::info("The hashes do not match, the HTTP request did not originate from Sinch!");
        return response()->json([], Response::HTTP_FORBIDDEN);
    }

    Log::info("Verification Callback validation was succesful, the hashes match!");

    // Continue processing the data...

    $verificationResponse = [
        "action" => "allow" // or "deny"
    ];

    return response()->json($verificationResponse, Response::HTTP_OK);
});
