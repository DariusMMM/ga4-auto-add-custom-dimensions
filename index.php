<?php

// Composer autoload file for packages
require 'vendor/autoload.php';

// Imports the recommended Google packages
use Google\Analytics\Admin\V1beta\AnalyticsAdminServiceClient;
use Google\Analytics\Admin\V1beta\CustomDimension;
use Google\Analytics\Admin\V1beta\CustomDimension\DimensionScope;

// Test to see if classes are loading properly
if (class_exists('Google\Analytics\Admin\V1beta\AnalyticsAdminServiceClient')) {
    echo "AnalyticsAdmin class found!";
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

// The Google Analytics property ID to which the custom dimensions should be added
// This will be done manually to start but should take an input form instead for the team to use
$propertyId = 'properties/485343688';

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

// Try to set up a new client instance
// Return error message if unsuccessful
try {
    // Create a new client instance
    $client = new AnalyticsAdminServiceClient([
        'credentials' => $serviceAccountFile
    ]);
} catch (Exception $e) {
    // Display exception message
    echo "Error setting up authentication configuration: " . $e->getMessage() . "<br>";

    // Stop script if authentication configuration fails
    exit;
}

// Iterate over each recommended ASC custom dimension and create the correct format for GA4
foreach ($ascCustomDimensions as $ascDimension) {
    $customDimensions = new CustomDimension ([
        'parameter_name' => $ascDimension,
        'display_name' => $ascDimension,
        'description' => $ascDimension,
        'scope' => DimensionScope::EVENT
    ]);

    // Try to push the formatted ASC custom dimension to GA4
    try {
        $response = $client->createCustomDimension($propertyId, $customDimensions);
        echo "Custom dimension created: " . $response->getName() . "<br>";
    } catch (Exception $e) {
        echo "Error creating custom dimension: " . $e->getMessage() . "<br>";
    }
}
