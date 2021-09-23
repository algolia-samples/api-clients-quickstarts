<?php

require __DIR__.'/vendor/autoload.php';

# Algolia client credentials
$ALGOLIA_APP_ID = getenv('ALGOLIA_APP_ID');
$ALGOLIA_API_KEY = getenv('ALGOLIA_API_KEY');
$ALGOLIA_INDEX_NAME = getenv('ALGOLIA_INDEX_NAME');

# Setup Algolia client and Index
$client = \Algolia\AlgoliaSearch\SearchClient::create($ALGOLIA_APP_ID, $ALGOLIA_API_KEY);
$index = $client->initIndex($ALGOLIA_INDEX_NAME);

# Run a search
print_r($index->search(""));