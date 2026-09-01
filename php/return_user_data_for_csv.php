<?php

# This script makes a GET request to the /searches endpoint on the Analytics REST API - https://www.algolia.com/doc/rest-api/analytics/. 
# To get the top 1000 searches over the last 7 days.

require __DIR__.'/vendor/autoload.php';

use Algolia\AlgoliaSearch\Api\AnalyticsClient;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

# Algolia client credentials
$ALGOLIA_APP_ID = $_ENV['ALGOLIA_APP_ID'];
$ALGOLIA_API_KEY = $_ENV['ALGOLIA_API_KEY'];
$ALGOLIA_INDEX_NAME = "shopify_products";

# Get application region
# https://www.algolia.com/doc/libraries/sdk/methods/analytics#php

$ALGOLIA_APPLICATION_REGION = "us";

$client = AnalyticsClient::create($ALGOLIA_APP_ID, $ALGOLIA_API_KEY, $ALGOLIA_APPLICATION_REGION);

# Returning CTR and write each date in its own row in CSV
$response = $client->getUsersCount(
    $ALGOLIA_INDEX_NAME,
);

print("Number of Users:\n");
var_dump($response);

$fp = fopen('user_data.csv', 'w');

$delimiter = ";";

// use keys as column titles
fputcsv( $fp, array_keys( $response['dates'][0] ), $delimiter, escape: "");

foreach ( $response['dates'] as $value ) {
    fputcsv( $fp, $value, $delimiter, escape: "");
}

fclose( $fp );

#Returning Top Countries
$response = $client->getTopCountries(
    $ALGOLIA_INDEX_NAME,
);

echo $ALGOLIA_APP_ID;

print("Top Countries:\n");
var_dump($response);

if (!empty($response)) {
    $fp = fopen('top_countries_data.csv', 'w');

    $delimiter = ";";

    // use keys as column titles
    fputcsv( $fp, array_keys( $response['countries'][0] ), $delimiter, escape: "");

    foreach ( $response['countries'] as $value ) {
        fputcsv( $fp, $value, $delimiter, escape: "");
    }

    fclose( $fp );
}
