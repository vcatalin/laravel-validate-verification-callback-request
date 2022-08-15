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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::Post('/verification/events', function (Request $request) {
    /*
        The key from one of your Verification Apps, found here https://dashboard.sinch.com/verification/apps
    */
    $applicationKey = "<REPLACE_WITH_VERIF_APP_KEY>";

    /*
        The secret from the Voice App that uses the key above, found here https://dashboard.sinch.com/verification/apps
    */
    $applicationSecret= "<REPLACE_WITH_VERIF_APP_SECRET>";

    $authHeader = explode(' ', $request->header('authorization'));
    $callbackAuthHeaderValue = $authHeader[1];

    $b64DecodedApplicationSecret = base64_decode($applicationSecret, true);

    $callbackRequest = $request->all();

    $encodedCallbackRequest = utf8_encode(json_encode($callbackRequest, JSON_UNESCAPED_UNICODE));
    $md5CallbackRequest = md5($encodedCallbackRequest, true);
    $encodedMd5ToBase64CallbackRequest = base64_encode($md5CallbackRequest);

    $requestMethod = $request->method();
    $requestContentType = $request->header('content-type');
    $requestTimeStamp = $request->header('x-timestamp');
    $requestUriPath = $request->getPathInfo();

    $stringToSign = $requestMethod . "\n"
        . $encodedMd5ToBase64CallbackRequest . "\n"
        . $requestContentType . "\n"
        . $requestTimeStamp . "\n"
        . $requestUriPath;

    $authorizationSignature = base64_encode(hash_hmac("sha256", $stringToSign, $b64DecodedApplicationSecret, true));

    if (strcmp("{$applicationKey}:{$authorizationSignature}", $callbackAuthHeaderValue)) {
        $verificationResponse = [
            "action" => "allow"
        ];
        Log::info("Verification is succesful, the hashes match.");
    } else {
        $verificationResponse = [
            "action" => "deny"
        ];
        Log::info("Verification failed, the hashes do not match.");
    };

    return response()->json($verificationResponse, Response::HTTP_OK);
});
