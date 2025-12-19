<?php

# Install the API client: https://www.algolia.com/doc/api-client/getting-started/install/php/?client=php
require_once realpath(__DIR__ . "/vendor/autoload.php");

use Algolia\AlgoliaSearch\Api\SearchClient;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

# Get your Algolia Application ID and (admin) API key from the dashboard: https://www.algolia.com/account/api-keys
# and choose a name for your index. Add these environment variables to a `.env` file:
$ALGOLIA_APP_ID = $_ENV['ALGOLIA_APP_ID'];
$ALGOLIA_API_KEY = $_ENV['ALGOLIA_API_KEY'];
$ALGOLIA_INDEX_NAME = $_ENV['ALGOLIA_INDEX_NAME'];

# Start the API client
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
$client = SearchClient::create($ALGOLIA_APP_ID, $ALGOLIA_API_KEY);

# Add new objects to the index
# https://www.algolia.com/doc/api-reference/api-methods/add-objects/
// Add a new record to your Algolia index
// Edit the JSON object based on your record schema
$response = $client->saveObject(
    $ALGOLIA_INDEX_NAME,
    ['objectID' => '1',
        'name' => 'foo',
    ],
);

var_dump($response);

// Poll the task status to know when it has been indexed
$client->waitForTask($ALGOLIA_INDEX_NAME, $response['taskID']);

// Fetch search results, with typo tolerance
$response = $client->search(
    ['requests' => [
        ['indexName' => $ALGOLIA_INDEX_NAME,
            'query' => 'foo',
            'hitsPerPage' => 50,
        ],
    ],
    ],
);

// play with the response
var_dump($response);
