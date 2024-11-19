<?php

// Composer autoload file for packages
require 'vendor/autoload.php';

// Imports the recommended Google packages
use Google\Client;
use Google\Service\AnalyticsAdmin;

// Test to see if classes are loading properly
if (class_exists('Google\Service\AnalyticsAdmin')) {
    echo "AnalyticsAdmin class found!";
    exit;
} else {
    echo "AnalyticsAdmin class not found!";
    exit;
}

// Service account file path
$serviceAccountFile = 'C:\laragon\www\ga4-auto-add-custom-dimensions\reach-analytics2.json';

// Check that the service account file exists and throw exception if not
if (!file_exists($serviceAccountFile)) {
    throw new Exception("Service account file not found at $serviceAccountFile");
}

// Create a new client instance
$client = new Client();

// Try to set up new $client and exit if this isn't successful
try {
    // Set authentication configuration using the service account JSON file
    $client->setAuthConfig($serviceAccountFile);

    // Give 'edit' access to this client instance
    $client->addScope(AnalyticsAdmin::ANALYTICS_EDIT);

} catch (Exception $e) {
    // Display exception message
    echo "Error setting up authentication configuration: " . $e->getMessage() . "<br>";

    // Stop script if authentication configuration fails
    exit;
}

// Create an instance of the AnalyticsAdmin service
$analyticsAdminService = new AnalyticsAdmin($client);

// The Google Analytics property ID to which the custom dimensions should be added
// This will be done manually to start but should take an input form instead for the team to use
$propertyId = 'properties/466420105';

// Validate that the property ID format is correct
try {
    if (strpos($propertyId, 'properties/') !== 0) {
        throw new Exception("Invalid property ID format. It needs to start with 'properties/'.");
    }
} catch (Exception $e) {
    echo "Validation error: " . $e->getMessage() . "<br>";
    exit; // Stop script if property ID is invalid
}

// ASC recommended custom dimensions to add
$ascCustomDimensions = array("affiliation", "comm_outcome", "comm_status", "comm_type", "currency", "department", "element_text", "element_type", "element_type", "element_value", "event_action", "event_action_result", "event_owner", "flow_name", "flow_outcome", "form_name", "form_type", "item_category", "item_color", "item_condition", "item_fuel_type", "item_id", "item_make", "item_model", "item_number", "item_payment", "item_price", "item_type", "item_variant", "item_year", "media_type", "page_location", "page_type", "product_name", "promotion_name");

// Empty array that will store the custom dimensions pushed to GA4
$customDimensions = [];

// Iterate over each recommended ASC custom dimension and push them to the $customDimensions array
foreach ($ascCustomDimensions as $ascDimension) {
    $customDimensions[] = [
        'parameterName' => $ascDimension,
        'displayName' => $ascDimension,
        'description' => $ascDimension,
        'scope' => 'EVENT'
    ];
}

// Iterate over each custom dimension and create them
foreach ($customDimensions as $dimensionData) {
    $customDimension = new AnalyticsAdmin\GoogleAnalyticsAdminV1betaCustomDimension($dimensionData);

    try {
        $response = $analyticsAdminService->properties_customDimensions->create($propertyId, $customDimension);
        echo "Custom dimension created: " . $response->getName() . "<br>";
    } catch (Exception $e) {
        echo "Error creating custom dimension: " . $e->getMessage() . "<br>";
    }
}
