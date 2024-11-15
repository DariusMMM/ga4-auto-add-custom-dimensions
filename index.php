<?php

require 'vendor/autoload.php';

putenv('GOOGLE_APPLICATION_CREDENTIALS=C:\Users\Make Model Marketing\Desktop\auto-add-custom-dimensions\reach-analytics2.json');

use Google\Analytics\Admin\V1beta\Account;
use Google\Analytics\Admin\V1beta\Client\AnalyticsAdminServiceClient;
use Google\Analytics\Admin\V1beta\ListAccountsRequest;

//$property_id = '466446057';

$client = new AnalyticsAdminServiceClient();

$request = new ListAccountsRequest();
$response = $client->listAccounts($request);



//print 'Result:' . PHP_EOL;
//foreach($response->iterateAllElements() as $account) {
//    print 'Display name: ' . $account->getDisplayName() . '<br>' . PHP_EOL;
//}
