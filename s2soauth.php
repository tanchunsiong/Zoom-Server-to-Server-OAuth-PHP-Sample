<?php

$config = include 'config.php';


// Access the environment variables
$clientId  = $config['s2s_oauth_client_id'];
$clientSecret  = $config['s2s_oauth_client_secret'];
$accountId= $config['s2s_oauth_account_id'];
$oauthUrl = 'https://zoom.us/oauth/token?grant_type=account_credentials&account_id=' . $accountId;  // Replace with your OAuth endpoint URL


    global $clientSecret, $clientId, $oauthUrl;


    try {
        // Create the Basic Authentication header
        $authHeader = 'Basic ' . base64_encode($clientId . ':' . $clientSecret);
     
        // Initialize cURL session
        $ch = curl_init($oauthUrl);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: ' . $authHeader));

        // Execute cURL session and get the response
        $response = curl_exec($ch);

        // Check if the request was successful (status code 200)
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode == 200) {
            // Parse the JSON response to get the access token
            $oauthResponse = json_decode($response, true);
            $accessToken = $oauthResponse['access_token'];
            //return $accessToken;
            http_response_code(200); // Replace 200 with your desired status code
            // Set the "Content-Type" header to "application/json"
            header('Content-Type: application/json');
            echo json_encode($accessToken);
      
            
        } else {
            echo 'OAuth Request Failed with Status Code: ' . $httpCode . PHP_EOL;
            echo $response . PHP_EOL;
            return null;
        }

        // Close cURL session
        curl_close($ch);
    } catch (Exception $e) {
        echo 'An error occurred: ' . $e->getMessage() . PHP_EOL;
        return null;
    }

?>
