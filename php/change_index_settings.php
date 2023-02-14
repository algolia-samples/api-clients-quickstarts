<?php

# Install the API client: https://www.algolia.com/doc/api-client/getting-started/install/php/?client=php
require __DIR__.'/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

# Get your Algolia Application ID and (admin) API key from the dashboard: https://www.algolia.com/account/api-keys
# and choose a name for your index. Add these environment variables to a `.env` file:
$ALGOLIA_APP_ID = $_ENV['ALGOLIA_APP_ID'];
$ALGOLIA_API_KEY = $_ENV['ALGOLIA_API_KEY'];
$ALGOLIA_INDEX_NAME = $_ENV['ALGOLIA_INDEX_NAME'];

# Start the API client
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
$client = \Algolia\AlgoliaSearch\SearchClient::create($ALGOLIA_APP_ID, $ALGOLIA_API_KEY);

# Create an index (or connect to it, if an index with the name `ALGOLIA_INDEX_NAME` already exists)
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/#initialize-an-index
$index = $client->initIndex($ALGOLIA_INDEX_NAME);

# Set index settings
# https://www.algolia.com/doc/api-reference/api-methods/set-settings/
$index->setSettings(
    [
      'searchableAttributes' => ['actors', 'genre'],
      'customRanking' => ['desc(rating)'],
    ],
    // Option to forward the same settings to the replica indices, when forwarding settings, please make sure your replicas already exist.
    [
      'forwardToReplicas' => true
    ]
  )->wait();

# Printing settings 
# https://www.algolia.com/doc/api-reference/api-methods/get-settings/
print("Index settings:\n");
$settings = $index->getSettings();
var_dump($settings);

?>
