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

# Returning most frequent searches without results
$response = $client->getSearchesNoResults(
    $ALGOLIA_INDEX_NAME,
);

if ($response['searches'] !== null) {
    print("Top Searches Without Results:\n");
    var_dump($response);

    $fp = fopen('top_searches_without_results_data.csv', 'w');

    $delimiter = ";";

    // use keys as column titles
    fputcsv( $fp, array_keys( $response['searches'][0] ), $delimiter, escape: "");

    foreach ( $response['searches'] as $value ) {
        fputcsv( $fp, $value, $delimiter, escape: "");
    }

    fclose( $fp );
}

# Returning no results rate
$response = $client->getNoResultsRate(
    $ALGOLIA_INDEX_NAME,
);

if ($response['dates'] !== null) {
    print("No Results Rate:\n");
    var_dump($response);

    $fp = fopen('no_results_rate_data.csv', 'w');

    $delimiter = ";";

    // use keys as column titles
    fputcsv( $fp, array_keys( $response['dates'][0] ), $delimiter, escape: "");

    foreach ( $response['dates'] as $value ) {
        fputcsv( $fp, $value, $delimiter, escape: "");
    }

    fclose( $fp );
}

# Returning top filters for searches without results
$response = $client->getTopFiltersNoResults(
    $ALGOLIA_INDEX_NAME,
);

if ($response['values'] !== null) {
    print("Top Filters with No Results:\n");
    var_dump($response);

    $fp = fopen('top_filters_no_results.csv', 'w');

    $delimiter = ";";

    // use keys as column titles
    fputcsv( $fp, array_keys( $response['values'][0] ), $delimiter, escape: "");

    foreach ( $response['values'] as $value ) {
        fputcsv( $fp, $value, $delimiter, escape: "");
    }

    fclose( $fp );
}