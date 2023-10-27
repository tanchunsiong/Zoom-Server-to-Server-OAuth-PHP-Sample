<?php

$config = include 'config.php';
$oauthClientId=$config['oauth_client_id'];
$oauthClientSecret= $config['oauth_client_secret'];
$refreshToken=$_GET['code'];



$url = 'https://zoom.us/oauth/token';

// Encode the client ID and client secret
$basic = base64_encode($oauthClientId . ':' . $oauthClientSecret);

$headers = [
    "Authorization: Basic $basic",
    "Content-Type: application/x-www-form-urlencoded"
];

$data = [
    'grant_type' => 'refresh_token',
    'refresh_token' => $refreshToken,
];

// Encode the data dictionary as x-www-form-urlencoded
$dataEncoded = http_build_query($data);

$options = [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_POSTFIELDS => $dataEncoded,
];

$ch = curl_init();
curl_setopt_array($ch, $options);

$response = curl_exec($ch);
$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

if (strpos($httpStatus, '200') !== false) {
    $responseJson = json_decode($response, true);
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode($responseJson);
    //echo $response;
} else {
    echo "$httpStatus\n";
}


?>
