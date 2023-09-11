<?php

$config = include 'config.php';


// Access the environment variables
$clientId  = $config['client_id'];
$clientSecret  = $config['client_secret'];
$accountId= $config['account_id'];
$oauthUrl = 'https://zoom.us/oauth/token?grant_type=account_credentials&account_id=' . $accountId;  // Replace with your OAuth endpoint URL

function getAccessToken() {
    global $clientSecret, $clientId, $oauthUrl;


    try {
        // Create the Basic Authentication header
        $authHeader = 'Basic ' . base64_encode($clientId . ':' . $clientSecret);
        echo "authHeader: " . $authHeader .  PHP_EOL;
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
            return $accessToken;
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
}

?>
