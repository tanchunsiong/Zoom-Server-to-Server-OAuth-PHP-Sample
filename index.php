<!DOCTYPE html>
<html>
<head>
    <title>Sample PHP Page</title>
</head>
<body>
    <h1>Welcome to My PHP Web Page</h1>
    <p>This is a sample PHP page.</p>

    <?php
    // Include the PHP file with the get_access_token function
    require_once 'S2SOAuth.php';

    // Call the get_access_token function
    $access_token = getAccessToken();

    // Display the access token (for demonstration purposes)
    if ($access_token) {
        echo "<p>Access Token: $access_token</p>";
    } else {
        echo "<p>Failed to retrieve Access Token.</p>";
    }
    ?>
</body>
</html>