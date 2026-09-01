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
$ALGOLIA_INDEX_NAME = $_ENV['ALGOLIA_INDEX_NAME'];

# Get application region
# https://www.algolia.com/doc/libraries/sdk/methods/analytics#php

$ALGOLIA_APPLICATION_REGION = "us";

$client = AnalyticsClient::create($ALGOLIA_APP_ID, $ALGOLIA_API_KEY, $ALGOLIA_APPLICATION_REGION);

# Return top filters and put in CSV file
$response = $client->getTopFilterAttributes(
    $ALGOLIA_INDEX_NAME,
);

print("Top Filters:\n");
var_dump($response);

$fp = fopen('top_filters.csv', 'w');

$delimiter = ";";

// use keys as column titles
fputcsv( $fp, array_keys( $response['attributes'][0] ), $delimiter, escape: "");

foreach ( $response['attributes'] as $value ) {
    fputcsv( $fp, $value, $delimiter, escape: "");
}

fclose( $fp );

# Returning CTR and write each date in its own row in CSV
$response = $client->getClickThroughRate(
    $ALGOLIA_INDEX_NAME,
);

print("CTR:\n");
var_dump($response);

$fp = fopen('top_filters.csv', 'w');

$delimiter = ";";

// use keys as column titles
fputcsv( $fp, array_keys( $response['attributes'][0] ), $delimiter, escape: "");

foreach ( $response['attributes'] as $value ) {
    fputcsv( $fp, $value, $delimiter, escape: "");
}

fclose( $fp );

#Returning CVR
$response = $client->getConversionRate(
    $ALGOLIA_INDEX_NAME,
);

print("CVR:\n");
var_dump($response);
