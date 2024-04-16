<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Simple Tab Control</title>
<style>
/* Styles for the tab control */
.tab {
  overflow: hidden;
  border: 1px solid #ccc;
  background-color: #f1f1f1;
}

.tab button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 14px 16px;
  transition: 0.3s;
}

.tab button:hover {
  background-color: #ddd;
}

.tab button.active {
  background-color: #ccc;
}

.tabcontent {
  display: none;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-top: none;
}

.tabcontent:first-child {
  display: block;
}
</style>
</head>
<body>

<h1>Sample code for PHP with Zoom API / Zoom SDK</h1>
    <p>This is a sample code page for PHP. It shows the common code sample when interacting with Zoom Webhook, Zoom Meeting SDK Auth Signature, Zoom OAuth and Zoom REST API.</p>

<h2>Github Link</h2>

<a href="https://github.com/tanchunsiong/Zoom-Server-to-Server-OAuth-PHP-Sample/">github source code</a> <br/>
<h2>Live Demo</h2>
    <a href="/webhook.php">webhook</a> <br/>
    <a href="/s2soauth.php">s2soauth</a><br/>
    <a href="/redirecturlforoauth.php?code=xxxx">redirecturlforoauth?code=xxxx</a><br/>
    <a href="/oauthrefreshtoken.php?code=xxxx">oauthrefreshtoken?code=xxxx</a><br/>
    <a href="/oauthrefreshtoken2.php?code=xxxx">oauthrefreshtoken?code=xxxx  (curl library)</a><br/>
    <br/>
    <a href="/meetingSDKToken.php">Meeting SDK Token (firebase library)</a><br/>
    <a href="/meetingSDKToken2.php">Meeting SDK Token</a><br/>
    <br/>
    <a href="/callapi.php?accesstoken=xxxx">call API</a><br/>
    <a href="https://zoom.us/oauth/authorize?response_type=code&client_id=97sULMUxRQuQg1xdNFKngQ&redirect_uri=https%3A%2F%2Fphp.asdc.cc%2Fredirecturlforoauth.php"> application adding url</a><br/>

    <br/>
    <br/>
    <h2>Code Samples</h2>
<div class="tab">
  <a href="#Tab1"><button class="tablinks" onclick="openTab(event, 'Tab1')">Handling Webhook</button></a>
  <a href="#Tab2"><button class="tablinks" onclick="openTab(event, 'Tab2')">Get Access Token (S2S Oauth)</button></a>
  <a href="#Tab3"><button class="tablinks" onclick="openTab(event, 'Tab3')">Redirect URL for OAuth</button></a>
  <a href="#Tab4"><button class="tablinks" onclick="openTab(event, 'Tab4')">Meeting SDK Token (Firebase Library)</button></a>
  <a href="#Tab5"><button class="tablinks" onclick="openTab(event, 'Tab5')">Meeting SDK Token</button></a>
  <a href="#Tab6"><button class="tablinks" onclick="openTab(event, 'Tab6')">OAuth Refresh Token</button></a>
  <a href="#Tab7"><button class="tablinks" onclick="openTab(event, 'Tab7')">OAuth Refresh Token(CURL Library)</button></a>
  <a href="#Tab8"><button class="tablinks" onclick="openTab(event, 'Tab8')">Call REST API</button></a>
</div>


<div id="Tab1" class="tabcontent">
  <h3>Tab 1 Content</h3>

  <pre>
  <code>

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

</code>
</pre>
  
</div>

<div id="Tab2" class="tabcontent">
  <h3>Tab 2 Content</h3>
  <pre>
    <code>
    
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

    </code>
    </pre>
</div>

<div id="Tab3" class="tabcontent">
  <h3>Tab 3 Content</h3>
 <pre>
    <code>
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



</code>
</pre>
</div>


<div id="Tab4" class="tabcontent">
  <h3>Tab 4 Content</h3>
  <pre>
    <code>
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
</code>
</pre>

  
</div>


<div id="Tab5" class="tabcontent">
  <h3>Tab 5 Content</h3>
  <pre>
    <code>
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

</code>
</pre>

  
</div>


<div id="Tab6" class="tabcontent">
<h3>Tab 6 Content</h3>
  <pre>
    <code>
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
    </code>
</pre>

</div>



<div id="Tab7" class="tabcontent">
<h3>Tab 7 Content</h3>
  <pre>
    <code>
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

</code>
</pre>

  
</div>



<div id="Tab8" class="tabcontent">
<h3>Tab 8 Content</h3>
  <pre>
    <code>
#$access_token =$_GET['accesstoken'];
$access_token = 'xxxx.yyyy.zzzz';

$meeting_data = [
    "topic" =>  'hello world',
    "type" => 2,
    "start_time" => "2023-10-01T10:00:00Z",
    "duration" =>  120,
    "password" => "12345678",
    "agenda" => "40 mins limit demonstration",
    "pre_schedule" => false,
    "timezone"=> "Asia/Singapore",
    "default_password" => false
];

$api_url = 'https://api.zoom.us/v2/users/me/meetings';
$ch_meeting = curl_init($api_url );


curl_setopt($ch_meeting, CURLOPT_HTTPHEADER, array(
  "Authorization: Bearer $access_token",
  'Content-Type: application/json',
  'Accept: application/json'
));

curl_setopt($ch_meeting, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch_meeting, CURLOPT_POSTFIELDS, json_encode($meeting_data));
curl_setopt($ch_meeting, CURLOPT_RETURNTRANSFER, true);

    $meeting_response = curl_exec($ch_meeting);
    echo "Détails de la réunion : ";
    echo $meeting_response;

$this->revokeAccessToken($CLIENT_ID, $CLIENT_SECRET, $access_token);

</code>
</pre>

  
</div>


<script>
function openTab(evt, tabName) {
  // Get all elements with class="tabcontent" and hide them
  var tabcontent = document.getElementsByClassName("tabcontent");
  for (var i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }

  // Get all elements with class="tablinks" and remove the class "active"
  var tablinks = document.getElementsByClassName("tablinks");
  for (var i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }

  // Show the current tab, and add an "active" class to the button that opened the tab
  document.getElementById(tabName).style.display = "block";
  evt.currentTarget.className += " active";
}

// Check if there's a hash in the URL and open the corresponding tab
window.onload = function() {
  var hash = window.location.hash;
  if (hash) {
    var tabName = hash.substring(1);
    var tab = document.getElementById(tabName);
    if (tab) {
      var evt = document.createEvent("MouseEvents");
      evt.initEvent("click", true, true);
      document.querySelector("button.tablinks").dispatchEvent(evt);
    }
  }
};
</script>

</body>
</html>