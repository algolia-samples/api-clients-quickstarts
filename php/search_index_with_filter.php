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

// Fetch search results with a filter
$response = $client->search(
    ['requests' => [
        ['indexName' => $ALGOLIA_INDEX_NAME,
            'query' => 'foo',
            'hitsPerPage' => 50,
            'filters' => "category:computers"
        ],
    ],
    ],
);

// play with the response
print_r($response['results'][0]['hits']);
print("\n");
?>