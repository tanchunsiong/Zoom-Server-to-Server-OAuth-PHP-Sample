<?php

$config = include 'config.php';
$oauthClientId = $config['oauth_client_id'];
$oauthClientSecret = $config['oauth_client_secret'];


$path='redirecturlforoauth.php';
$code =$_GET['code'];


    //echo "handleRedirectUrlDataRequest\n";
    $url = "https://zoom.us/oauth/token";
    $redirectUri = "https://php.asdc.cc/$path";
    //echo "$redirectUri\n";
    
    // Encode the client ID and client secret
    $credentials = "$oauthClientId:$oauthClientSecret";
    //echo "$credentials\n";
    $credentialsEncoded = base64_encode($credentials);

    $headers = [
        "Authorization: Basic $credentialsEncoded",
        "Content-Type: application/x-www-form-urlencoded"
    ];
    //echo "$credentialsEncoded\n";

    $data = [
        'grant_type' => 'authorization_code',
        'redirect_uri' => $redirectUri,
        'code' => $code
    ];
     //echo "$data\n";
    // Encode the data dictionary as x-www-form-urlencoded
    $dataEncoded = http_build_query($data);
    //echo "$dataEncoded\n";
    $options = [
        'http' => [
            'header' => implode("\r\n", $headers),
            'method' => 'POST',
            'content' => $dataEncoded
        ]
    ];
    $context = stream_context_create($options);
   
    $response = file_get_contents($url, false, $context);
    
    $httpStatus = $http_response_header[0]; // Get the HTTP status from the headers

    if (strpos($httpStatus, '200 OK') !== false) {
        //echo "response 200\n";
        $responseJson = json_decode($response, true); // Decode JSON as associative array
        
        // Optionally, you can return an HTTP status code
        http_response_code(200); // Replace 200 with your desired status code
        
        // Set the "Content-Type" header to "application/json"
        header('Content-Type: application/json');

        // Encode the JSON data and return it
        echo json_encode($responseJson);

   
    } else {
        // Handle the case where the response has an error status code
        echo "$httpStatus\n";
    }


?>