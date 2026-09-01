<?php

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

# Create synonym
$response = $client->saveSynonym(
    $ALGOLIA_INDEX_NAME,
    'id1',
    ['objectID' => 'id1',
        'type' => 'synonym',
        'synonyms' => [
            'car',
            'vehicule',
            'auto',
        ],
    ],
    true,
);


// print the response
var_dump($response);

# Retrieve the synonym
$response = $client->getSynonym(
    $ALGOLIA_INDEX_NAME,
    'id1',
);


// print the response
var_dump($response);

# List all synonyms
$response = $client->searchSynonyms(
    $ALGOLIA_INDEX_NAME,
);


// print the response
var_dump($response);

# Delete a synonym
$response = $client->deleteSynonym(
    $ALGOLIA_INDEX_NAME,
    'id1',
);


// print the response
var_dump($response);