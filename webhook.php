<?php

$config = include 'config.php';
$secretToken = $config['webhook_app_secret_token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // This block handles POST requests

    // Get the raw POST data from the request
    $input = file_get_contents("php://input");

    // Decode the JSON data
    $data = json_decode($input);

    // Check if the event type is "endpoint.url_validation"
    if ($data && isset($data->event) && $data->event === "endpoint.url_validation") {
        // Check if the payload contains the "plainToken" property
        if (isset($data->payload) && isset($data->payload->plainToken)) {
            // Get the plainToken from the payload
            $plainToken = $data->payload->plainToken;

            // Hash the plainToken using HMAC-SHA256
            $encryptedToken = hash_hmac("sha256", $plainToken, $secretToken);

            // Create the response JSON object
            $response = [
                "plainToken" => $plainToken,
                "encryptedToken" => $encryptedToken
            ];

            // Set the response HTTP status code to 200 OK
            http_response_code(200);

            // Set the response content type to JSON
            header("Content-Type: application/json");

            // Output the response JSON
            echo json_encode($response);

        } else {
            // Payload is missing the "plainToken" property
            http_response_code(400); // Bad Request
            echo "Payload is missing 'plainToken' property.";
        }
    } else {

        try{
        // Save the JSON data to a file
        $jsonFileName = '/var/www/php.asdc.cc/webhook.txt'; // Set the filename
        file_put_contents($jsonFileName, json_encode($data));
        echo "Data Saved: " .  json_encode($data);
        }
        catch(Exception $e){
            echo "An error occurred: " . $e->getMessage();
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // This block handles GET requests

    $jsonFileName = '/var/www/php.asdc.cc/webhook.txt'; 
    // Check if the file 'token.txt' exists
    if (file_exists('/var/www/php.asdc.cc/webhook.txt')) {
        try{
         // Read the JSON data from the file
         $jsonContents = file_get_contents($jsonFileName);

        // Parse the JSON data into a PHP object
        $jsonData = json_decode($jsonContents);

        // Convert the PHP object back to a JSON string
        $jsonString = json_encode($jsonData);

        // Echo the JSON string
        echo $jsonString;
        }
        catch(Exception $e){
            echo "An error occurred: " . $e->getMessage();
        }
    } else {
        // Token file does not exist
        echo "Token file does not exist.";
    }
} else {
    // Unsupported HTTP method
    http_response_code(405); // Method Not Allowed
    echo "Unsupported HTTP method.";
}
?>





