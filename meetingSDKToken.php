

<?php
// Step 1: Include Composer's autoload.php
require 'vendor/autoload.php';

// Step 2: Import the Firebase JWT class
use Firebase\JWT\JWT;


$config = include 'config.php';
$meetingSDKClientKey=$config['meetingSDKClientKey'];
$meetingSDKClientSecret= $config['meetingSDKClientSecret'];


$iat = time();
$exp = $iat + 60 * 60 * 2;
$token_payload = [

    'sdkKey' => $meetingSDKClientKey,
    'mn' => 9898533313,
    'role' => 1,
    'iat' => $iat,
    'exp' => $exp,
    'tokenExp' => $exp
];

$jwt = JWT::encode($token_payload, $meetingSDKClientSecret, 'HS256');
echo $jwt;

?>






