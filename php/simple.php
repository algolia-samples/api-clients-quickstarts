<?php

require __DIR__.'/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

# Algolia client credentials
$ALGOLIA_APP_ID = $_ENV['ALGOLIA_APP_ID'];
$ALGOLIA_API_KEY = $_ENV['ALGOLIA_API_KEY'];
$ALGOLIA_INDEX_NAME = $_ENV['ALGOLIA_INDEX_NAME'];

# Initialize the client
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
$client = \Algolia\AlgoliaSearch\SearchClient::create($ALGOLIA_APP_ID, $ALGOLIA_API_KEY);

# Initialize an index
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/#initialize-an-index
$index = $client->initIndex($ALGOLIA_INDEX_NAME);

# Save Objects: Add multiple new objects to an index.
# https://www.algolia.com/doc/api-reference/api-methods/add-objects/
$newObject = ['objectID' => 1, 'name' => 'Foo'];
$res = $index->saveObjects([$newObject]);

# Waiting for the indexing task to complete
# https://www.algolia.com/doc/api-reference/api-methods/wait-task/
$res->wait();

# Search Index: Method used for querying an index.
# https://www.algolia.com/doc/api-reference/api-methods/search/
$objects = $index->search('Fo');
print_r($objects);