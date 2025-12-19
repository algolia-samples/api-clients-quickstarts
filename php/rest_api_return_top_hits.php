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

$response = $client->getTopSearches($ALGOLIA_INDEX_NAME);

# Returning 1000 Top Searches
print("1000 Top Searches:\n");
var_dump($response);

# Format the resulting json string
$formatted_result = json_encode($response, JSON_PRETTY_PRINT);

# Write json to file
if (file_put_contents("{$ALGOLIA_INDEX_NAME}_top_1000_searches.json", $formatted_result))
    echo "JSON file created successfully...\n";
else
    echo "Oops! Error creating json file...\n";
