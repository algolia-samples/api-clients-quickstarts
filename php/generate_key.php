<?php

#API Key Generator
#This script will generate an API key for an Algolia application.
#The generated key will be valid for Search operations, and will be limited to 100 queries per hour.

# Install the API client: https://www.algolia.com/doc/api-client/getting-started/install/php/?client=php
require __DIR__.'/vendor/autoload.php';

use Algolia\AlgoliaSearch\Api\SearchClient;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

# Algolia client credentials
$ALGOLIA_APP_ID = $_ENV['ALGOLIA_APP_ID'];
$ALGOLIA_API_KEY = $_ENV['ALGOLIA_API_KEY'];
$ALGOLIA_INDEX_NAME = $_ENV['ALGOLIA_INDEX_NAME'];

# Initialize the client
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
$client = SearchClient::create($ALGOLIA_APP_ID, $ALGOLIA_API_KEY);

# Set permissions for API key
# https://www.algolia.com/doc/api-reference/api-methods/add-api-key/#method-param-acl

//Set the parameters for API key
//https://www.algolia.com/doc/rest-api/search/add-api-key

$acl = [
    'acl' => [
        'search',

        'addObject',
    ],
        'description' => 'Restricted search-only API key for algolia.com',
        'maxQueriesPerIPPerHour' => 100
    ];

# Create a new restricted search-only API key
print("Creating new key...\n");

$res = $client->addApiKey($acl);

$client->waitForTask($ALGOLIA_INDEX_NAME, $res['key']);

$new_key = $res['key'];

if ($new_key = $res['key']) {
    echo "Key generated successfully: $new_key \n";
} else {
    echo "Error while creating key\n";
}

//Wait for the API key to be created
$res = $client->waitForApiKey(
    $new_key,
    'add',
);

# Test the created key
print("Testing key...\n");

# Initialise a new client with the generated key
$newClient = SearchClient::create($ALGOLIA_APP_ID, $new_key);

# Test the new generated key by performing a search

$searchQuery =  [
    'requests' => [
        ['indexName' => $ALGOLIA_INDEX_NAME,
            'query' => '',
            'hitsPerPage' => 50,
        ],
    ],
];

if ($search_res = $newClient->search($searchQuery)) {
    echo "Successful key test\n";
} else {
    echo "Failed search with the new key\n";
}