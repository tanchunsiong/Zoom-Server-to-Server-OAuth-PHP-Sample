<?php

$config = include 'config.php';
$oauthClientId=$config['oauth_client_id'];
$oauthClientSecret= $config['oauth_client_secret'];
$refreshToken=$_GET['code'];



    $url = 'https://zoom.us/oauth/token';


    // Encode the client ID and client secret
    $credentials = "$oauthClientId:$oauthClientSecret";
   
    $credentialsEncoded = base64_encode($credentials);

    $headers = [
        "Authorization: Basic $credentialsEncoded",
        "Content-Type: application/x-www-form-urlencoded"
    ];
  

    $data = [
        'grant_type' => 'refresh_token',
        'refresh_token' => $refreshToken,
    ];
   
    // Encode the data dictionary as x-www-form-urlencoded
    $dataEncoded = http_build_query($data);
    
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
