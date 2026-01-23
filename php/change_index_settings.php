<?php

# Install the API client: https://www.algolia.com/doc/api-client/getting-started/install/php/?client=php
require __DIR__.'/vendor/autoload.php';

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

# Set index settings
# https://www.algolia.com/doc/api-reference/api-methods/set-settings/
$res = $client->setSettings(
    $ALGOLIA_INDEX_NAME, 
    [
      'searchableAttributes' => ['actors', 'genre'],
      'customRanking' => ['desc(rating)'],
    ],
    // Option to forward the same settings to the replica indices, when forwarding settings, please make sure your replicas already exist.
    [
      'forwardToReplicas' => true
    ]
    );

$client->waitForTask($ALGOLIA_INDEX_NAME, $res['taskID']);

# Printing settings 
# https://www.algolia.com/doc/api-reference/api-methods/get-settings/
print("Index settings:\n");
$settings = $client->getSettings($ALGOLIA_INDEX_NAME, 1);
var_dump($settings);

?>
