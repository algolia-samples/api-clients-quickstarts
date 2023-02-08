<?php

# This script makes a GET request to the /searches endpoint on the Analytics REST API - https://www.algolia.com/doc/rest-api/analytics/. 
# To get the top 1000 searches over the last 7 days.
# There is no API client for Analytics, so this script uses the PHP Requests library to make the call.

# Install the API client: https://www.algolia.com/doc/api-client/getting-started/install/php/?client=php
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

# Get your Algolia Application ID and (admin) API key from the dashboard: https://www.algolia.com/account/api-keys
# and choose a name for your index. Add these environment variables to a `.env` file:
$ALGOLIA_APP_ID = $_ENV['ALGOLIA_APP_ID'];
$ALGOLIA_API_KEY = $_ENV['ALGOLIA_API_KEY'];
$ALGOLIA_INDEX_NAME = $_ENV['ALGOLIA_INDEX_NAME'];

# The Analytics API can be reached from multiple domains, each specific to a region. 
# You should use the domain that matches the region where your analytics data is stored and processed. 
# View your analytics region: https://www.algolia.com/infra/analytics
# The following domains are available:
# United States: https://analytics.us.algolia.com
# Europe (Germany): https://analytics.de.algolia.com

$URL_DOMAIN = $_ENV['URL_DOMAIN'];

$url = "$URL_DOMAIN/2/searches?index=$ALGOLIA_INDEX_NAME&limit=1000";

# Describing HTTP request
$request_options  = [
    'http' => [
        'method'  => 'GET',
        'header'  => join("\r\n", [
            "X-Algolia-Application-Id: $ALGOLIA_APP_ID",
            "X-Algolia-API-Key: $ALGOLIA_API_KEY",
        ]),
    ]
];

# Sending HTTP request
$result = file_get_contents($url, false, stream_context_create($request_options));

if ($result === FALSE) {
    exit("Failed HTTP request");
}

# Returning 1000 Top Searches
print("1000 Top Searches:\n");
var_dump($result);

# Format the resulting json string
$json = json_decode($result);
$formatted_result = json_encode($json, JSON_PRETTY_PRINT);

# Write json to file
if (file_put_contents("{$ALGOLIA_INDEX_NAME}_top_1000_searches.json", $formatted_result))
    echo "JSON file created successfully...\n";
else
    echo "Oops! Error creating json file...\n";
