<?php

$config = include 'config.php';
$meetingSDKClientKey = $config['meetingSDKClientKey'];
$meetingSDKClientSecret = $config['meetingSDKClientSecret'];


$iat = time()    - 30;
$exp = $iat + 60 * 60 * 10;
//$client_request = $this->request->input('json_decode');

// Define the payload data for your JWT token
$token_payload = [
    'sdkKey' => $meetingSDKClientKey,
    'mn' => 9898533313,
    'role' => 1,
    'iat' => $iat, // Issued at timestamp
    'exp' => $exp,  // Expiration timestamp (2 hours from now)
    'appKey' => $meetingSDKClientKey,
    'tokenExp' => $exp 
];

// Encode the JWT token
$header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
$payload = json_encode($token_payload);

$headers_encoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
$payload_encoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

$signature = hash_hmac('sha256', $headers_encoded . '.' . $payload_encoded, $meetingSDKClientSecret, true);
$signature_encoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

$jwt = $headers_encoded . '.' . $payload_encoded . '.' . $signature_encoded;

echo $jwt;

?>