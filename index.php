<?php

require 'vendor/autoload.php';
require 'google-oauth.php';

use Google\Analytics\Admin\V1beta\Account;
use Google\Analytics\Admin\V1beta\Client\AnalyticsAdminServiceClient;
use Google\Analytics\Admin\V1beta\ListAccountsRequest;

//$property_id = '466446057';

// Service account JSON file
$serviceAccountFile = '/Applications/XAMPP/xamppfiles/htdocs/ga4-auto-add-custom-dimensions/reach-analytics2.json';
$credentials = json_decode(file_get_contents($serviceAccountFile), true);

// Service account information
$privateKey = $credentials['private_key']; # Taken from service account JSON file
$clientEmail = $credentials['client_email']; # Taken from service account JSON file
$tokenUrl = 'https://oauth2.googleapis.com/token';

// Create a JSON Web Token (JWT) header
$jwtHeader = createJwtHeader();

// Create a JWT payload
$jwtPayload = createJwtPayload($clientEmail, $tokenUrl);

// Create the JWT
$jwt = createJwt($jwtHeader, $jwtPayload, $privateKey);

// Make a POST request to get an access token
$tokenResponse = file_get_contents($tokenUrl, false, stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ])
    ]
]));

$tokenData = json_decode($tokenResponse, true);
$accessToken = $tokenData['access_token'];

// Step 2: Use the access token to interact with the Google Analytics Admin API
$propertyId = 'YOUR_PROPERTY_ID'; // Replace with your property ID

$customDimensions = [
    [
        'parameterName' => 'custom_dimension_1',
        'displayName' => 'Dimension 1',
        'scope' => 'EVENT'
    ],
    [
        'parameterName' => 'custom_dimension_2',
        'displayName' => 'Dimension 2',
        'scope' => 'EVENT'
    ]
];

foreach ($customDimensions as $dimension) {
    $response = file_get_contents(
        "https://analyticsadmin.googleapis.com/v1beta/properties/$propertyId/customDimensions",
        false,
        stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Authorization: Bearer $accessToken\r\n" .
                    "Content-Type: application/json\r\n",
                'content' => json_encode($dimension)
            ]
        ])
    );
    
    $responseData = json_decode($response, true);
    if (isset($responseData['error'])) {
        echo "Error creating custom dimension: " . $responseData['error']['message'] . "<br>";
    } else {
        echo "Custom dimension created: " . $responseData['name'] . "<br>";
    }
}
echo "hello world";
?>
